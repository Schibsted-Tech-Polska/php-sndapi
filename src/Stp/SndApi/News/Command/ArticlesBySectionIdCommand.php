<?php

namespace Stp\SndApi\News\Command;

use SoapBox\Formatter\Formatter;
use Stp\SndApi\Common\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ArticlesBySectionIdCommand extends BaseCommand
{
    use NewsClientCommandTrait;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('news:section:articles')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Section ID'
            )
            ->addArgument(
                'method',
                InputArgument::REQUIRED,
                'Articles list method (desked, auto)'
            )
            ->addArgument(
                'parameters',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'List of parameters i.e. limit=50'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Stp\SndApi\News\Exception\InvalidMethodException
     * @throws \Stp\SndApi\News\Exception\InvalidMethodParametersException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $newsClient = $this->getNewsClient($input);

        $sectionId = $input->getArgument('id');
        $method = $input->getArgument('method');
        $parameterStrings = $input->getArgument('parameters');
        $parameters = [];

        foreach ($parameterStrings as $parameter) {
            $parameterData = explode('=', $parameter);

            $parameters[$parameterData[0]] = $parameterData[1];
        }

        $result = $newsClient->getArticlesBySectionId($sectionId, $method, $parameters);
        $formatter = Formatter::make($result, Formatter::ARR);

        $output->writeln($formatter->toYaml());

        return 0;
    }
}
