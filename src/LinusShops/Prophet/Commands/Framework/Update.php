<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-03-16
 */

namespace LinusShops\Prophet\Commands\Framework;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends Command
{
    protected function configure()
    {
        $this
            ->setName('framework:update')
            ->setDescription('Display a list of installed test frameworks.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Local name for the framework.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $curdir = getcwd();
        $name = $input->getArgument('name');
        $frameworkDir = PROPHET_ROOT_DIR.'/frameworks/'.$name;

        if (is_dir($frameworkDir)) {
            chdir($frameworkDir);
            passthru('git pull && composer install');
        } else {
            echo "{$name} not found.";
        }

        chdir($curdir);
    }
}
