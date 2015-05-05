<?php

namespace Stp\SndApi\Common\Test\Command;

use Stp\SndApi\Common\Command\BaseCommand;
use Stp\SndApi\Common\Test\InvokeInaccessibleMethodTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

class BaseCommandTest extends \PHPUnit_Framework_TestCase
{
    use InvokeInaccessibleMethodTrait;

    public function testConfigure()
    {
        /** @var Command|\PHPUnit_Framework_MockObject_MockObject $stub */
        $stub = $this->getMockBuilder(BaseCommand::class)
            ->disableOriginalConstructor()
            ->setMethods(['addOption'])
            ->getMock();

        $stub->expects($this->at(0))
            ->method('addOption')
            ->with(
                'secret',
                's',
                InputOption::VALUE_REQUIRED,
                $this->anything()
            )
            ->willReturn($stub);

        $stub->expects($this->at(1))
            ->method('addOption')
            ->with(
                'publicationId',
                'p',
                InputOption::VALUE_REQUIRED,
                $this->anything()
            )
            ->willReturn($stub);

        $this->invokeMethod($stub, 'configure');

    }
}
