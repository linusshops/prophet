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
use LinusShops\Prophet\Command;
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
            ->setDescription('Update a plugin.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of the plugin to update'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $pluginDir = PROPHET_ROOT_DIR.'/plugins/'.$name;

        if (is_dir($pluginDir)) {
            $cmd = 'git pull && composer install';
            $this->shell($cmd, $pluginDir);
        } else {
            echo "{$name} not found.";
        }
    }
}
