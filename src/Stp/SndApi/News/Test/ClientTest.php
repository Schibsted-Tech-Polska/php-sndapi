<?php

namespace Stp\SndApi\News\Test;

use Guzzle\Common\Event;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Client as GuzzleClient;
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
        return new Client('secret', 'sa');
    }

    /**
     * @return Client
     */
    private function getClientWithResponse(
        $responseCode = 200,
        $headers = null,
        $responseBody = [],
        $requestCallback = null
    ) {
        $validResponse = new Response($responseCode, $headers, json_encode($responseBody));

        $plugin = new MockPlugin();
        $plugin->addResponse($validResponse);

        $guzzleClient = new GuzzleClient();
        $guzzleClient->addSubscriber($plugin);
        if ($requestCallback) {
            $guzzleClient->getEventDispatcher()->addListener('request.before_send', $requestCallback);
        }

        $client = $this->getClient();
        $client->setClient($guzzleClient);

        return $client;
    }

    public function testConstructor()
    {
        $client = $this->getClient();

        $this->assertEquals('http://api.snd.no/news/v2', $client->getApiUrl());
        $this->assertEquals('secret', $client->getApiSecret());
        $this->assertEquals('sa', $client->getPublicationId());
    }

    public function testApiGet()
    {
        $client = $this->getClientWithResponse(200);

        $this->invokeMethod($client, 'apiGet', ['/']);
    }

    /**
     * @expectedException \Stp\SndApi\Common\Exception\UnsatisfactoryResponseCodeException
     */
    public function testApiGetWith301Response()
    {
        $client = $this->getClientWithResponse(301);

        $client->getServiceDocument();
    }

    /**
     * @expectedException \Stp\SndApi\Common\Exception\ItemDoesNotExistsException
     */
    public function testApiGetWith404Response()
    {
        $client = $this->getClientWithResponse(404);

        $client->getServiceDocument();
    }

    /**
     * @expectedException \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function testApiGetWith403Response()
    {
        $client = $this->getClientWithResponse(403);

        $client->getServiceDocument();
    }

    public function testGetServiceDocument()
    {
        $client = $this->getClientWithResponse(200);

        $result = $client->getServiceDocument();
        $this->assertEquals([], $result);
    }

    public function testGetSectionsList()
    {
        $scope = $this;
        $client = $this->getClientWithResponse(
            200,
            null,
            [],
            function (Event $e) use ($scope) {
                /** @var Request $request */
                $request = $e['request'];

                $scope->assertContains('/sections', $request->getUrl());
            }
        );

        $result = $client->getSectionsList();
        $this->assertEquals([], $result);
    }

    public function testGetSubsectionsList()
    {
        $scope = $this;
        $client = $this->getClientWithResponse(
            200,
            null,
            [],
            function (Event $e) use ($scope) {
                /** @var Request $request */
                $request = $e['request'];

                $scope->assertContains('/sections/100/subsections', $request->getUrl());
            }
        );

        $result = $client->getSubsectionsList(100);
        $this->assertEquals([], $result);
    }

    public function testGetSectionByUniqueName()
    {
        $scope = $this;
        $client = $this->getClientWithResponse(
            200,
            null,
            [],
            function (Event $e) use ($scope) {
                /** @var Request $request */
                $request = $e['request'];

                $scope->assertContains('/sections/instance?uniqueName=sectionName', $request->getUrl());
            }
        );

        $result = $client->getSectionByUniqueName('sectionName');
        $this->assertEquals([], $result);
    }

    public function testGetSectionById()
    {
        $scope = $this;
        $client = $this->getClientWithResponse(
            200,
            null,
            [],
            function (Event $e) use ($scope) {
                /** @var Request $request */
                $request = $e['request'];

                $scope->assertContains('/sections/100', $request->getUrl());
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
        $scope = $this;
        $client = $this->getClientWithResponse(
            200,
            null,
            [],
            function (Event $e) use ($scope) {
                /** @var Request $request */
                $request = $e['request'];

                $scope->assertContains('/sections/100/desked?offset=50', $request->getUrl());
            }
        );

        $result = $client->getArticlesBySectionId(100, 'desked', ['offset' => 50]);
        $this->assertEquals([], $result);
    }

    public function testGetArticle()
    {
        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder(Client::class)
            ->setConstructorArgs(['secret', 'sa'])
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
        $scope = $this;
        $client = $this->getClientWithResponse(
            200,
            null,
            [],
            function (Event $e) use ($scope) {
                /** @var Request $request */
                $request = $e['request'];

                $scope->assertContains(
                    '/searchContents/instance?contentId=100100&contentType=article',
                    $request->getUrl()
                );
            }
        );

        $result = $client->searchByInstance(100100, 'article');
        $this->assertEquals([], $result);
    }

    public function testGetSearchByCollection()
    {
        $scope = $this;
        $client = $this->getClientWithResponse(
            200,
            null,
            [],
            function (Event $e) use ($scope) {
                /** @var Request $request */
                $request = $e['request'];

                $scope->assertContains('/searchContents/collection?contentIds=100100%2C200200', $request->getUrl());
            }
        );

        $result = $client->searchByCollection([100100, 200200]);
        $this->assertEquals([], $result);
    }
}
