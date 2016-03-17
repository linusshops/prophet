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
                InputArgument::OPTIONAL,
                'Url to git repository',
                null
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

        $repo = $input->getArgument('repository');

        if (empty($repo)) {
            $output->writeln("Repository not provided, checking defaults...");
            $defaultRepos = file_get_contents(PROPHET_ROOT_DIR.'/defaults/frameworks.json');
            $defaultRepos = json_decode($defaultRepos, true);
            if ($defaultRepos && isset($defaultRepos[$name])) {
                $repo = $defaultRepos[$name]['url'];
                $output->writeln("Using {$repo}...");
            } else {
                $output->writeln("{$name} has no default repository.");
                return;
            }
        }

        if ($this->shell('git clone '.$repo.' '.$name, $frameworkDir)) {
            $this->shell('composer install', $frameworkDir . '/' . $name);
        } else {
            $output->writeln('Failed to install from '.$repo);
        }
    }
}
