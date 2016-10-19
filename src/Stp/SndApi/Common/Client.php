<?php

namespace Stp\SndApi\Common;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Stp\SndApi\Common\Exception\InvalidPublicationIdException;
use Stp\SndApi\Common\Validator\PublicationIdValidator;

abstract class Client implements LoggerAwareInterface
{
    const BASE_URL = 'http://api.snd.no';

    /**
     * @var GuzzleHttpClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $apiSecret;

    /**
     * @var string
     */
    protected $publicationId;

    /**
     * @var string
     */
    protected $apiUrl = self::BASE_URL;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $publicationId
     * @param string $apiUrl
     */
    public function __construct($apiKey, $apiSecret, $publicationId, $apiUrl = self::BASE_URL)
    {
        $this->client = new GuzzleHttpClient(['base_url' => $apiUrl]);
        $this->apiUrl = $apiUrl;
        $this->setApiKey($apiKey);
        $this->setApiSecret($apiSecret);
        $this->setPublicationId($publicationId);
        $this->setLogger(new NullLogger());
    }

    /**
     * @param RequestInterface $request
     */
    protected function signRequest(RequestInterface $request)
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $signature = sprintf('0x%s', hash_hmac('sha256', $now->format('d M Y H'), $this->getApiSecret()));

        $request->addHeader('X-Snd-ApiSignature', $signature);

        if (!empty($this->getApiKey())) {
            $request->addHeader('X-Snd-ApiKey', $this->getApiKey());
        }
    }

    /**
     * @param RequestInterface $request
     * @param bool $acceptJsonResponse
     */
    protected function buildRequest(RequestInterface $request, $acceptJsonResponse = true)
    {
        if ($acceptJsonResponse) {
            $request->addHeader('Accept', 'application/json');
        }
        $request->addHeader('Accept-Charset', 'UTF-8');
    }

    /**
     * @param string $url
     * @param bool $acceptJsonResponse
     *
     * @return ResponseInterface
     * @throws RequestException
     */
    protected function apiGet($url, $acceptJsonResponse = true)
    {
        if (0 === preg_match('/^\//', $url)) {
            $url = '/' . $url;
        }

        $url = str_replace('{publicationId}', $this->getPublicationId(), $url);

        $request = $this->client->createRequest('GET', $this->getApiUrl() . $url);
        $this->buildRequest($request, $acceptJsonResponse);
        $this->signRequest($request);

        return $this->client->send($request);
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    /**
     * @param string $apiSecret
     */
    public function setApiSecret($apiSecret)
    {
        $this->apiSecret = $apiSecret;
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @return string
     */
    public function getPublicationId()
    {
        return $this->publicationId;
    }

    /**
     * @param string $publicationId
     *
     * @throws InvalidPublicationIdException
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function setPublicationId($publicationId)
    {
        $publicationIdValidator = new PublicationIdValidator($publicationId);

        if (!$publicationIdValidator->isValid()) {
            throw new InvalidPublicationIdException;
        }

        $this->publicationId = $publicationId;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param GuzzleHttpClient $client
     */
    public function setClient(GuzzleHttpClient $client)
    {
        $this->client = $client;
    }
}
