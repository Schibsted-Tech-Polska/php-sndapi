<?php

namespace Stp\SndApi\News\Test\Command;

use Stp\SndApi\News\Client;
use Stp\SndApi\News\Command\NewsClientCommandTrait;
use Symfony\Component\Console\Input\InputInterface;

class NewsClientCommandTraitTest extends \PHPUnit_Framework_TestCase
{
    use NewsClientCommandTrait;

    public function testGetAndInitNewsClient()
    {
        $input = $this->getMockBuilder(InputInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOption'])
            ->getMockForAbstractClass();

        $input->expects($this->any())
            ->method('getOption')
            ->withConsecutive(['key'], ['secret'], ['publicationId'])
            ->willReturnOnConsecutiveCalls('key', 'secret', 'sa');

        $this->assertEmpty($this->newsClient);

        $this->getNewsClient($input);

        $this->assertInstanceOf(Client::class, $this->newsClient);
    }

    public function testSetNewsClient()
    {
        $newsClient = new Client('key', 'secret', 'sa');

        $this->setNewsClient($newsClient);

        $this->assertEquals($newsClient, $this->newsClient);
    }
}
