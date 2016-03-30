<?php
/**
 *
 *
 * @author Sam Schmidt
 * @date 2015-04-17
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Commands\Plugin;

use LinusShops\Prophet\Commands\Plugin;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends Plugin
{
    protected function configure()
    {
        $this
            ->setName('plugin:install')
            ->setDescription('Install a plugin.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Local name for the plugin.'
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
        $pluginDir = PROPHET_ROOT_DIR.'/plugins';

        if (!is_writable($pluginDir)) {
            $output->writeln("{$pluginDir} is not writable.");
            return;
        }

        $name = $input->getArgument('name');

        if (is_dir($pluginDir.'/'.$name)) {
            $output->writeln("{$name} already exists.");
            return;
        }

        $repo = $input->getArgument('repository');

        if (empty($repo)) {
            $output->writeln("Repository not provided, checking defaults...");
            $defaultRepos = file_get_contents(PROPHET_ROOT_DIR.'/defaults/plugins.json');
            $defaultRepos = json_decode($defaultRepos, true);
            if ($defaultRepos && isset($defaultRepos[$name])) {
                $repo = $defaultRepos[$name]['url'];
                $output->writeln("Using {$repo}...");
            } else {
                $output->writeln("{$name} has no default repository.");
                return;
            }
        }

        if ($this->shell('git clone '.$repo.' '.$name, $pluginDir)) {
            $this->shell('composer install', $pluginDir . '/' . $name);
        } else {
            $output->writeln('Failed to install from '.$repo);
        }

        $this->copyConfig($name);
    }
}
