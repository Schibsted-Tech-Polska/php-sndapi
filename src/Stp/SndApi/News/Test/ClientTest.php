<?php

namespace Stp\SndApi\News\Test;

use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Stream\Stream;
use Stp\SndApi\Common\Test\InvokeInaccessibleMethodTrait;
use Stp\SndApi\News\Client;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    use InvokeInaccessibleMethodTrait;

    /**
     * @return Client
     */
    private function getClient()
    {
        return new Client('key', 'secret', 'sa');
    }

    /**
     * @param int $responseCode
     * @param null|array $headers
     * @param array $responseBody
     * @param callable|null $expectationCallback
     *
     * @return Client
     */
    private function getClientWithResponse(
        $responseCode = 200,
        $headers = [],
        $responseBody = [],
        $expectationCallback = null
    ) {
        $stream = Stream::factory(json_encode($responseBody));
        $validResponse = new Response($responseCode, $headers, $stream);

        $guzzleClient = $this->getMock(GuzzleHttpClient::class, ['send', 'createRequest']);
        $guzzleClient->expects($this->once())
            ->method('send')
            ->willReturn($validResponse);

        if ($expectationCallback) {
            call_user_func($expectationCallback, $guzzleClient);
        }

        $client = $this->getClient();
        $client->setClient($guzzleClient);

        return $client;
    }

    public function testConstructor()
    {
        $client = $this->getClient();

        $this->assertEquals('http://api.snd.no/news/v2', $client->getApiUrl());
        $this->assertEquals('key', $client->getApiKey());
        $this->assertEquals('secret', $client->getApiSecret());
        $this->assertEquals('sa', $client->getPublicationId());
    }

    public function testApiGet()
    {
        $client = $this->getClientWithResponse(200, [], [], function (GuzzleHttpClient $guzzleClient) {
            /** @var GuzzleHttpClient|\PHPUnit_Framework_MockObject_MockObject $guzzleClient */
            $guzzleClient->expects($this->once())
                ->method('createRequest')
                ->with(
                    'GET',
                    'http://api.snd.no/news/v2/'
                )
                ->willReturn(new Request('GET', '/'));
        });

        $this->invokeMethod($client, 'apiGet', ['/']);
    }

    /**
     * @expectedException \Stp\SndApi\Common\Exception\UnsatisfactoryResponseCodeException
     */
    public function testApiGetWith301Response()
    {
        $client = $this->getClientWithResponse(301, [], [], function (GuzzleHttpClient $guzzleClient) {
            /** @var GuzzleHttpClient|\PHPUnit_Framework_MockObject_MockObject $guzzleClient */
            $guzzleClient->expects($this->once())
                ->method('createRequest')
                ->with(
                    'GET',
                    'http://api.snd.no/news/v2/'
                )
                ->willReturn(new Request('GET', '/'));
        });

        $client->getServiceDocument();
    }

    /**
     * @expectedException \Stp\SndApi\Common\Exception\ItemDoesNotExistsException
     */
    public function testApiGetWith404Response()
    {
        $client = $this->getClientWithResponse(404, [], [], function (GuzzleHttpClient $guzzleClient) {
            /** @var GuzzleHttpClient|\PHPUnit_Framework_MockObject_MockObject $guzzleClient */
            $guzzleClient->expects($this->once())
                ->method('createRequest')
                ->with(
                    'GET',
                    'http://api.snd.no/news/v2/'
                )
                ->willReturn(new Request('GET', '/'));
        });

        $client->getServiceDocument();
    }

    /**
     * @expectedException \Stp\SndApi\Common\Exception\UnsatisfactoryResponseCodeException
     */
    public function testApiGetWith403Response()
    {
        $client = $this->getClientWithResponse(403, [], [], function (GuzzleHttpClient $guzzleClient) {
            /** @var GuzzleHttpClient|\PHPUnit_Framework_MockObject_MockObject $guzzleClient */
            $guzzleClient->expects($this->once())
                ->method('createRequest')
                ->with(
                    'GET',
                    'http://api.snd.no/news/v2/'
                )
                ->willReturn(new Request('GET', '/'));
        });

        $client->getServiceDocument();
    }

    public function testGetServiceDocument()
    {
        $client = $this->getClientWithResponse(200, [], [], function (GuzzleHttpClient $guzzleClient) {
            /** @var GuzzleHttpClient|\PHPUnit_Framework_MockObject_MockObject $guzzleClient */
            $guzzleClient->expects($this->once())
                ->method('createRequest')
                ->with(
                    'GET',
                    'http://api.snd.no/news/v2/'
                )
                ->willReturn(new Request('GET', '/'));
        });

        $result = $client->getServiceDocument();
        $this->assertEquals([], $result);
    }

    public function testGetSectionsList()
    {
        $client = $this->getClientWithResponse(
            200,
            [],
            [],
            function (GuzzleHttpClient $guzzleClient) {
                /** @var GuzzleHttpClient|\PHPUnit_Framework_MockObject_MockObject $guzzleClient */
                $guzzleClient->expects($this->once())
                    ->method('createRequest')
                    ->with(
                        'GET',
                        'http://api.snd.no/news/v2/publication/sa/sections'
                    )
                    ->willReturn(new Request('GET', '/sections'));
            }
        );

        $result = $client->getSectionsList();
        $this->assertEquals([], $result);
    }

    public function testGetSubsectionsList()
    {
        $client = $this->getClientWithResponse(
            200,
            [],
            [],
            function (GuzzleHttpClient $guzzleClient) {
                /** @var GuzzleHttpClient|\PHPUnit_Framework_MockObject_MockObject $guzzleClient */
                $guzzleClient->expects($this->once())
                    ->method('createRequest')
                    ->with(
                        'GET',
                        'http://api.snd.no/news/v2/publication/sa/sections/100/subsections'
                    )
                    ->willReturn(new Request('GET', '/sections/100/subsections'));
            }
        );

        $result = $client->getSubsectionsList(100);
        $this->assertEquals([], $result);
    }

    public function testGetSectionByUniqueName()
    {
        $client = $this->getClientWithResponse(
            200,
            [],
            [],
            function (GuzzleHttpClient $guzzleClient) {
                /** @var GuzzleHttpClient|\PHPUnit_Framework_MockObject_MockObject $guzzleClient */
                $guzzleClient->expects($this->once())
                    ->method('createRequest')
                    ->with(
                        'GET',
                        'http://api.snd.no/news/v2/publication/sa/sections/instance?uniqueName=sectionName'
                    )
                    ->willReturn(new Request('GET', '/sections/instance?uniqueName=sectionName'));
            }
        );

        $result = $client->getSectionByUniqueName('sectionName');
        $this->assertEquals([], $result);
    }

    public function testGetSectionById()
    {
        $client = $this->getClientWithResponse(
            200,
            [],
            [],
            function (GuzzleHttpClient $guzzleClient) {
                /** @var GuzzleHttpClient|\PHPUnit_Framework_MockObject_MockObject $guzzleClient */
                $guzzleClient->expects($this->once())
                    ->method('createRequest')
                    ->with(
                        'GET',
                        'http://api.snd.no/news/v2/publication/sa/sections/100'
                    )
                    ->willReturn(new Request('GET', '/sections/100'));
            }
        );

        $result = $client->getSectionById(100);
        $this->assertEquals([], $result);
    }

    /**
     * @expectedException \Stp\SndApi\News\Exception\InvalidMethodException
     */
    public function testGetArticlesBySectionIdWithInvalidMethod()
    {
        $client = $this->getClient();
        $client->getArticlesBySectionId(100, 'invalid');
    }

    /**
     * @expectedException \Stp\SndApi\News\Exception\InvalidMethodParametersException
     */
    public function testGetArticlesBySectionIdWithInvalidParameters()
    {
        $client = $this->getClient();
        $client->getArticlesBySectionId(100, 'auto', ['invalid' => 100]);
    }

    public function testGetArticlesBySectionIdWithParams()
    {
        $client = $this->getClientWithResponse(
            200,
            [],
            [],
            function (GuzzleHttpClient $guzzleClient) {
                /** @var GuzzleHttpClient|\PHPUnit_Framework_MockObject_MockObject $guzzleClient */
                $guzzleClient->expects($this->once())
                    ->method('createRequest')
                    ->with(
                        'GET',
                        'http://api.snd.no/news/v2/publication/sa/sections/100/desked?offset=50'
                    )
                    ->willReturn(new Request('GET', '/sections/100/desked?offset=50'));
            }
        );

        $result = $client->getArticlesBySectionId(100, 'desked', ['offset' => 50]);
        $this->assertEquals([], $result);
    }

    public function testGetArticle()
    {
        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder(Client::class)
            ->setConstructorArgs(['key', 'secret', 'sa'])
            ->setMethods(['searchByInstance'])
            ->getMock();

        $client->expects($this->any())
            ->method('searchByInstance')
            ->with(100100, 'article');

        $client->getArticle(100100);
    }

    /**
     * @expectedException \Stp\SndApi\News\Exception\InvalidContentTypeException
     */
    public function testGetSearchByInstanceWithInvalidContentType()
    {
        $client = $this->getClient();
        $client->searchByInstance(100100, 'invalid');
    }

    public function testGetSearchByInstance()
    {
        $client = $this->getClientWithResponse(
            200,
            [],
            [],
            function (GuzzleHttpClient $guzzleClient) {
                $url = 'http://api.snd.no/news/v2/publication/sa/searchContents/instance?contentId=100100' .
                    '&contentType=article';

                /** @var GuzzleHttpClient|\PHPUnit_Framework_MockObject_MockObject $guzzleClient */
                $guzzleClient->expects($this->once())
                    ->method('createRequest')
                    ->with(
                        'GET',
                        $url
                    )
                    ->willReturn(new Request('GET', '/searchContents/instance?contentId=100100&contentType=article'));
            }
        );

        $result = $client->searchByInstance(100100, 'article');
        $this->assertEquals([], $result);
    }

    public function testGetSearchByCollection()
    {
        $client = $this->getClientWithResponse(
            200,
            [],
            [],
            function (GuzzleHttpClient $guzzleClient) {
                /** @var GuzzleHttpClient|\PHPUnit_Framework_MockObject_MockObject $guzzleClient */
                $guzzleClient->expects($this->once())
                    ->method('createRequest')
                    ->with(
                        'GET',
                        'http://api.snd.no/news/v2/publication/sa/searchContents/collection?contentIds=100100,200200'
                    )
                    ->willReturn(new Request('GET', '/searchContents/collection?contentIds=100100,200200'));
            }
        );

        $result = $client->searchByCollection([100100, 200200]);
        $this->assertEquals([], $result);
    }
}
