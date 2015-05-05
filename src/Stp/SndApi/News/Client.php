<?php

namespace Stp\SndApi\News;

use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Message\Response;
use Stp\SndApi\Common\Client as CommonClient;
use Stp\SndApi\Common\Exception\ItemDoesNotExistsException;
use Stp\SndApi\Common\Exception\UnsatisfactoryResponseCodeException;
use Stp\SndApi\News\Exception\InvalidContentTypeException;
use Stp\SndApi\News\Exception\InvalidMethodException;
use Stp\SndApi\News\Exception\InvalidMethodParametersException;
use Stp\SndApi\News\Validator\ArticleListMethodValidator;
use Stp\SndApi\News\Validator\ArticlesListParametersValidator;
use Stp\SndApi\News\Validator\ContentTypeValidator;

class Client extends CommonClient
{
    /**
     * {@inheritdoc}
     */
    public function __construct($apiSecret, $publicationId)
    {
        parent::__construct($apiSecret, $publicationId);

        $this->setApiUrl(self::BASE_URL . '/news/v2');
    }

    /**
     * @param string $url
     * @return array|Response|null
     * @throws ItemDoesNotExistsException
     * @throws UnsatisfactoryResponseCodeException
     */
    protected function apiGet($url)
    {
        try {
            $response = parent::apiGet($url);

            if ($response->getStatusCode() !== 200) {
                throw new UnsatisfactoryResponseCodeException();
            }

            return $response;
        } catch (ClientErrorResponseException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new ItemDoesNotExistsException;
            }

            throw $e;
        }
    }

    /**
     * @return array|bool|float|int|string
     * @throws ItemDoesNotExistsException
     * @throws UnsatisfactoryResponseCodeException
     */
    public function getServiceDocument()
    {
        $response = $this->apiGet('/');

        return $response->json();
    }

    /**
     * @return array|bool|float|int|string
     * @throws ItemDoesNotExistsException
     * @throws UnsatisfactoryResponseCodeException
     */
    public function getSectionsList()
    {
        $response = $this->apiGet('/publication/{publicationId}/sections');

        return $response->json();
    }

    /**
     * @param int $sectionId
     * @return array|bool|float|int|string
     * @throws ItemDoesNotExistsException
     * @throws UnsatisfactoryResponseCodeException
     */
    public function getSubsectionsList($sectionId)
    {
        $response = $this->apiGet(sprintf('/publication/{publicationId}/sections/%d/subsections', $sectionId));

        return $response->json();
    }

    /**
     * @param string $sectionName
     * @return array|bool|float|int|string
     */
    public function getSectionByUniqueName($sectionName)
    {
        return $this->getSection(sprintf('/publication/{publicationId}/sections/instance?uniqueName=%s', $sectionName));
    }

    /**
     * @param int $sectionId
     * @return array|bool|float|int|string
     */
    public function getSectionById($sectionId)
    {
        return $this->getSection(sprintf('/publication/{publicationId}/sections/%d', $sectionId));
    }

    /**
     * @param string $url
     * @return array|bool|float|int|string
     * @throws ItemDoesNotExistsException
     * @throws UnsatisfactoryResponseCodeException
     */
    private function getSection($url)
    {
        $response = $this->apiGet($url);

        return $response->json();
    }

    /**
     * @param int $sectionId
     * @param string $method
     * @param array $parameters
     * @return array|bool|float|int|string
     * @throws InvalidMethodException
     * @throws InvalidMethodParametersException
     * @throws ItemDoesNotExistsException
     * @throws UnsatisfactoryResponseCodeException
     */
    public function getArticlesBySectionId($sectionId, $method, $parameters = [])
    {
        $methodValidator = new ArticleListMethodValidator($method);
        if (!$methodValidator->isValid()) {
            throw new InvalidMethodException(sprintf('Method "%s" is not allowed', $method));
        }

        $parametersValidator = new ArticlesListParametersValidator($method, $parameters);
        if (!$parametersValidator->isValid()) {
            throw new InvalidMethodParametersException(
                sprintf('Invalid parameters used for "%s" method', $method)
            );
        }

        $url = sprintf(
            '/publication/{publicationId}/sections/%d/%s?%s',
            $sectionId,
            $method,
            http_build_query($parameters)
        );
        $url = rtrim($url, '?&');

        $response = $this->apiGet($url);

        return $response->json();
    }

    /**
     * @param int $contentId
     * @return array|bool|float|int|string
     * @throws InvalidContentTypeException
     */
    public function getArticle($contentId)
    {
        return $this->searchByInstance($contentId, 'article');
    }

    /**
     * @param int $contentId
     * @param string $contentType
     * @return array|bool|float|int|string
     * @throws InvalidContentTypeException
     * @throws ItemDoesNotExistsException
     * @throws UnsatisfactoryResponseCodeException
     */
    public function searchByInstance($contentId, $contentType)
    {
        $contentTypeValidator = new ContentTypeValidator($contentType);

        if (!$contentTypeValidator->isValid()) {
            throw new InvalidContentTypeException(sprintf('Content Type "%s" is not allowed', $contentType));
        }

        $response = $this->apiGet(
            sprintf(
                '/publication/{publicationId}/searchContents/instance?contentId=%d&contentType=%s',
                $contentId,
                $contentType
            )
        );

        return $response->json();
    }

    /**
     * @param array $contentIds
     * @return array|bool|float|int|string
     * @throws ItemDoesNotExistsException
     * @throws UnsatisfactoryResponseCodeException
     */
    public function searchByCollection($contentIds)
    {
        $response = $this->apiGet(
            sprintf('/publication/{publicationId}/searchContents/collection?contentIds=%s', join(',', $contentIds))
        );

        return $response->json();
    }
}
