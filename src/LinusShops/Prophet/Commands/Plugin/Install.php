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
use LinusShops\Prophet\ConfigRepository;
use LinusShops\Prophet\Command;
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
                'repository',
                InputArgument::REQUIRED,
                'Url to git repository'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $curdir = getcwd();
        $url = $input->getArgument('repository');
        chdir(ConfigRepository::getPluginDirectory());
        $dirs = scandir('.');
        passthru('git clone '.$url);
        $newdirs = scandir('.');
        $diff = array_diff($newdirs, $dirs);
        $pluginName = array_pop($diff);
        chdir($pluginName);
        passthru('composer install');
        $this->copyConfig($pluginName);

        chdir($curdir);
    }
}
