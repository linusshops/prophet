<?php
/**
 *
 *
 * @author Sam Schmidt
 * @date 2015-04-17
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Commands\Plugin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends Command
{
    protected function configure()
    {
        $this
            ->setName('plugin:install')
            ->setDescription('Install a plugin.')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'The url to the repository (git/http/https accepted)'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
