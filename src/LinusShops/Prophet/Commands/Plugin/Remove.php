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

class Remove extends Command
{
    protected function configure()
    {
        $this
            ->setName('plugin:remove')
            ->setDescription('Remove a plugin.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of the plugin to remove'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
    }
}
