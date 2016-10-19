<?php

namespace Stp\SndApi\Common\Test;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Message\Response;
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
        return $this->getMockForAbstractClass(Client::class, ['apikey', 'apisecret', 'sa']);
    }

    public function jsonAcceptationProvider()
    {
        return [
            [true],
            [false]
        ];
    }

    public function testApiSecret()
    {
        $stub = $this->getStub();

        $stub->setApiSecret('test');
        $this->assertEquals('test', $stub->getApiSecret());
    }

    public function testApiKey()
    {
        $stub = $this->getStub();

        $stub->setApiKey('test');
        $this->assertEquals('test', $stub->getApiKey());
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

        $client = new GuzzleHttpClient();
        $request = $client->createRequest('GET', 'http://www.example.org/');

        $this->invokeMethod($stub, 'signRequest', [$request]);

        $this->assertNotNull($request->getHeader('X-Snd-ApiSignature'));
        $this->assertNotNull($request->getHeader('X-Snd-ApiKey'));
    }

    public function testSignRequestWithEmptyApiKey()
    {
        $stub = $this->getStub();
        $stub->setApiKey('');

        $client = new GuzzleHttpClient();
        $request = $client->createRequest('GET', 'http://www.example.org/');

        $this->invokeMethod($stub, 'signRequest', [$request]);

        $this->assertEmpty($request->getHeader('X-Snd-ApiKey'));
    }

    /**
     * @dataProvider jsonAcceptationProvider
     * @param bool $isJsonAccepted
     */
    public function testBuildRequest($isJsonAccepted)
    {
        $stub = $this->getStub();

        $client = new GuzzleHttpClient();
        $request = $client->createRequest('GET', 'http://www.example.org/');

        $this->invokeMethod($stub, 'buildRequest', [$request, $isJsonAccepted]);

        if ($isJsonAccepted) {
            $this->assertNotNull($request->getHeader('Accept'));
        } else {
            $this->assertEmpty($request->getHeader('Accept'));
        }
        $this->assertNotNull($request->getHeader('Accept-Charset'));

    }

    public function testApiGet()
    {
        $validResponse = new Response(200);

        $client = $this->getMock(GuzzleHttpClient::class, ['send']);
        $client->expects($this->once())
            ->method('send')
            ->willReturn($validResponse);

        $stub = $this->getStub();
        $stub->setClient($client);

        $response = $this->invokeMethod($stub, 'apiGet', ['']);
        $this->assertEquals($validResponse, $response);
    }
}
