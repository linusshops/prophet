<?php
/**
 *
 *
 * @author Sam Schmidt
 * @date 2015-04-17
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Commands\Plugin;

use LinusShops\Prophet\ConfigRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends Command
{
    protected function configure()
    {
        $this
            ->setName('plugin:update')
            ->setDescription('Update a plugin, or all plugins if none provided.')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Name of the plugin to update',
                null
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $curdir = getcwd();
        $name = $input->getArgument('name');
        chdir(ConfigRepository::getPluginDirectory());

        $plugins = array();

        if ($name == null) {
            $plugins = array_diff(scandir('.'), array('.','..','.gitignore'));
        } else {
            $plugins[] = $name;
        }

        foreach ($plugins as $plugin) {
            chdir(ConfigRepository::getPluginDirectory().'/'.$plugin);
            passthru('git pull');
            passthru('composer update');
        }

        chdir($curdir);
    }
}
