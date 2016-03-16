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

class Install extends Command
{
    protected function configure()
    {
        $this
            ->setName('framework:install')
            ->setDescription('Install a test framework.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Local name for the framework.'
            )
            ->addArgument(
                'repository',
                InputArgument::REQUIRED,
                'Url to git repository'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $frameworkDir = PROPHET_ROOT_DIR.'/frameworks';

        if (!is_writable($frameworkDir)) {
            $output->writeln("{$frameworkDir} is not writable.");
            return;
        }

        $name = $input->getArgument('name');

        if (is_dir($frameworkDir.'/'.$name)) {
            $output->writeln("{$name} already exists.");
            return;
        }

        $this->shell('git clone '.$input->getArgument('repository').' '.$name, $frameworkDir);
        $this->shell('composer install', $frameworkDir.'/'.$name);
    }
}
