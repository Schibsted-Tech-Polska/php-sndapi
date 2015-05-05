<?php

namespace Stp\SndApi\News\Test\Command;

use Stp\SndApi\News\Client;
use Stp\SndApi\News\Command\ArticleCommand;
use Stp\SndApi\News\Command\ArticlesBySectionIdCommand;
use Stp\SndApi\News\Command\SearchByInstanceCommand;
use Stp\SndApi\News\Command\SearchCollectionCommand;
use Stp\SndApi\News\Command\SectionByIdCommand;
use Stp\SndApi\News\Command\SectionByUniqueNameCommand;
use Stp\SndApi\News\Command\SectionsListCommand;
use Stp\SndApi\News\Command\ServiceDocumentCommand;
use Stp\SndApi\News\Command\SubsectionsListCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ApiCommandTest extends \PHPUnit_Framework_TestCase
{
    private function initCommand($command, $commandClass, $expectedMethod)
    {
        $application = new Application();
        $application->add(new $commandClass);

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder(Client::class)
            ->setConstructorArgs(['secret', 'sa'])
            ->getMock();

        /** @var ArticleCommand $command */
        $command = $application->find($command);
        $command->setNewsClient($client);

        $client->expects($this->once())
            ->method($expectedMethod)
            ->willReturn(['tested']);

        return $command;
    }

    public function commandProvider()
    {
        return [
            ['news:article:id', ArticleCommand::class, 'getArticle', ['id' => 100]],
            ['news:section:articles', ArticlesBySectionIdCommand::class, 'getArticlesBySectionId', [
                'id' => 100,
                'method' => 'auto',
                'parameters' => ['limit=50']
            ]],
            ['news:search:instance', SearchByInstanceCommand::class, 'searchByInstance', [
                'id' => 100,
                'contentType' => 'article'
            ]],
            ['news:search:collection', SearchCollectionCommand::class, 'searchByCollection', [
                'ids' => [
                    100,
                    200
                ]
            ]],
            ['news:section:id', SectionByIdCommand::class, 'getSectionById', [
                'id' => 100
            ]],
            ['news:section:uniquename', SectionByUniqueNameCommand::class, 'getSectionByUniqueName', [
                'name' => 'test'
            ]],
            ['news:sections:list', SectionsListCommand::class, 'getSectionsList', []],
            ['news:servicedocument', ServiceDocumentCommand::class, 'getServiceDocument', []],
            ['news:subsections:list', SubsectionsListCommand::class, 'getSubsectionsList', [
                'sectionId' => 100
            ]]
        ];
    }

    /**
     * @dataProvider commandProvider
     */
    public function testExecute($commandName, $commandClass, $commandMethod, $commandParams)
    {
        $command = $this->initCommand($commandName, $commandClass, $commandMethod);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            $commandParams + [
                '--secret' => 'secret',
                '--publicationId' => 'sa'
            ]
        );

        $this->assertContains('tested', $commandTester->getDisplay());
    }
}
