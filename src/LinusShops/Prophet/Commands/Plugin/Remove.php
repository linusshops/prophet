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
        $curdir = getcwd();
        $name = $input->getArgument('name');
        chdir(ConfigRepository::getPluginDirectory());

        if (strpos($name, 'prophet-plugin-')!==false) {
            passthru('rm -rf '.$name);
            echo "$name removed successfully.".PHP_EOL;
        }

        chdir($curdir);
    }
}
