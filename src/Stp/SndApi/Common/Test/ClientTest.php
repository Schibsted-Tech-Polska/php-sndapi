<?php

namespace Stp\SndApi\Common\Test;

use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use PHPUnit_Framework_MockObject_MockObject;
use Stp\SndApi\Common\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    use InvokeInaccessibleMethodTrait;

    /**
     * @return Client|PHPUnit_Framework_MockObject_MockObject
     */
    private function getStub()
    {
        return $this->getMockForAbstractClass(Client::class, ['apisecret', 'sa']);
    }

    public function testApiSecret()
    {
        $stub = $this->getStub();

        $stub->setApiSecret('test');
        $this->assertEquals('test', $stub->getApiSecret());
    }

    public function testApiUrl()
    {
        $stub = $this->getStub();

        $this->assertEquals('http://api.snd.no', $stub->getApiUrl());

        $stub->setApiUrl('http://api1.snd.no/');
        $this->assertEquals('http://api1.snd.no', $stub->getApiUrl());
    }

    public function testValidPublicationId()
    {
        $stub = $this->getStub();

        $stub->setPublicationId('sa');
        $this->assertEquals('sa', $stub->getPublicationId());
    }

    /**
     * @expectedException \Stp\SndApi\Common\Exception\InvalidPublicationIdException
     */
    public function testInvalidPublicationId()
    {
        $stub = $this->getStub();

        $stub->setPublicationId('invalidpublicationid');
    }

    public function testSignRequest()
    {
        $stub = $this->getStub();

        $client = new GuzzleClient();
        $request = $client->createRequest('GET', 'http://www.example.org/');

        $this->invokeMethod($stub, 'signRequest', [$request]);

        $this->assertNotNull($request->getHeader('X-Snd-ApiSignature'));
    }

    public function testBuildRequest()
    {
        $stub = $this->getStub();

        $client = new GuzzleClient();
        $request = $client->createRequest('GET', 'http://www.example.org/');

        $this->invokeMethod($stub, 'buildRequest', [$request]);

        $this->assertNotNull($request->getHeader('Accept'));
        $this->assertNotNull($request->getHeader('Accept-Charset'));
    }

    public function testApiGet()
    {
        $validResponse = new Response(200);

        $plugin = new MockPlugin();
        $plugin->addResponse($validResponse);

        $client = new GuzzleClient();
        $client->addSubscriber($plugin);

        $stub = $this->getStub();
        $stub->setClient($client);

        $response = $this->invokeMethod($stub, 'apiGet', ['']);
        $this->assertEquals($validResponse, $response);
    }
}
