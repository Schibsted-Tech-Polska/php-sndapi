<?php

namespace Stp\SndApi\News\Command;

use SoapBox\Formatter\Formatter;
use Stp\SndApi\Common\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SectionByUniqueNameCommand extends BaseCommand
{
    use NewsClientCommandTrait;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('news:section:uniquename')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Unique section name'
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

        $sectionName = $input->getArgument('name');

        $result = $newsClient->getSectionByUniqueName($sectionName);
        $formatter = Formatter::make($result, Formatter::ARR);

        $output->writeln($formatter->toYaml());

        return 0;
    }
}
