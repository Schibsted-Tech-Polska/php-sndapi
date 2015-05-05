<?php

namespace Stp\SndApi\News\Command;

use SoapBox\Formatter\Formatter;
use Stp\SndApi\Common\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchByInstanceCommand extends BaseCommand
{
    use NewsClientCommandTrait;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('news:search:instance')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Content ID'
            )
            ->addArgument(
                'contentType',
                InputArgument::REQUIRED,
                'Content type (article, person)'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Stp\SndApi\News\Exception\InvalidContentTypeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $newsClient = $this->getNewsClient($input);

        $contentId = $input->getArgument('id');
        $contentType = $input->getArgument('contentType');

        $result = $newsClient->searchByInstance($contentId, $contentType);
        $formatter = Formatter::make($result, Formatter::ARR);

        $output->writeln($formatter->toYaml());

        return 0;
    }
}
