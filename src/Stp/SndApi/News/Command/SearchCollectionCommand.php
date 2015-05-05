<?php

namespace Stp\SndApi\News\Command;

use SoapBox\Formatter\Formatter;
use Stp\SndApi\Common\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchCollectionCommand extends BaseCommand
{
    use NewsClientCommandTrait;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('news:search:collection')
            ->addArgument(
                'ids',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'Content IDs'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $newsClient = $this->getNewsClient($input);

        $contentIds = $input->getArgument('ids');

        $result = $newsClient->searchByCollection($contentIds);
        $formatter = Formatter::make($result, Formatter::ARR);

        $output->writeln($formatter->toYaml());

        return 0;
    }
}
