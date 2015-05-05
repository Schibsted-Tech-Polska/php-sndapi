<?php

namespace Stp\SndApi\News\Command;

use SoapBox\Formatter\Formatter;
use Stp\SndApi\Common\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ArticleCommand extends BaseCommand
{
    use NewsClientCommandTrait;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('news:article:id')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Article ID'
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

        $articleId = $input->getArgument('id');

        $result = $newsClient->getArticle($articleId);
        $formatter = Formatter::make($result, Formatter::ARR);

        $output->writeln($formatter->toYaml());

        return 0;
    }
}
