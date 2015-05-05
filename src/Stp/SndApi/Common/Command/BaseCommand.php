<?php

namespace Stp\SndApi\Common\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class BaseCommand extends Command
{
    protected function configure()
    {
        $this
            ->addOption(
                'secret',
                's',
                InputOption::VALUE_REQUIRED,
                'SND API secret'
            )
            ->addOption(
                'publicationId',
                'p',
                InputOption::VALUE_REQUIRED,
                'SND API publication id (common, sa, fvn, bt, ap)'
            );
    }
}
