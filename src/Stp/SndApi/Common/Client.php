<?php

namespace Stp\SndApi\Common;

use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Stp\SndApi\Common\Exception\InvalidPublicationIdException;
use Stp\SndApi\Common\Validator\PublicationIdValidator;

abstract class Client implements LoggerAwareInterface
{
    const BASE_URL = 'http://api.snd.no';

    /**
     * @var GuzzleClient
     */
    protected $client;

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
     * @param string $apiSecret
     * @param string $publicationId
     */
    public function __construct($apiSecret, $publicationId)
    {
        $this->client = new GuzzleClient($this->apiUrl);
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
    }

    /**
     * @param RequestInterface $request
     */
    protected function buildRequest(RequestInterface $request)
    {
        $request->addHeader('Accept', 'application/json');
        $request->addHeader('Accept-Charset', 'UTF-8');
    }

    /**
     * @param string $url
     * @return array|Response|null
     */
    protected function apiGet($url)
    {
        if (0 === preg_match('/^\//', $url)) {
            $url = '/' . $url;
        }

        $url = str_replace('{publicationId}', $this->getPublicationId(), $url);

        $request = $this->client->createRequest('GET', $this->getApiUrl() . $url);
        $this->buildRequest($request);
        $this->signRequest($request);

        return $this->client->send($request);
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
     * @param string $apiUrl
     */
    public function setApiUrl($apiUrl)
    {
        if (1 === preg_match('/\/$/', $apiUrl)) {
            $apiUrl = substr($apiUrl, 0, -1);
        }

        $this->apiUrl = $apiUrl;
        $this->client->setBaseUrl($this->apiUrl);
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
     * @param GuzzleClient $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }
}
