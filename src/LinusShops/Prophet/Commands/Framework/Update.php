<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-03-16
 */

namespace LinusShops\Prophet\Commands\Framework;

use LinusShops\Prophet\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class Update extends Command
{
    protected function configure()
    {
        $this
            ->setName('framework:update')
            ->setDescription('Update an installed framework.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Local name for the framework.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $frameworkDir = PROPHET_ROOT_DIR.'/frameworks/'.$name;

        if (is_dir($frameworkDir)) {
            $cmd = 'git pull && composer install';
            $this->shell($cmd, $frameworkDir);
        } else {
            echo "{$name} not found.";
        }
    }
}
