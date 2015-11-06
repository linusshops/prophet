<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2015-11-06
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Commands\Plugin;


use LinusShops\Prophet\ConfigRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Show extends Command
{
    protected function configure()
    {
        $this
            ->setName('plugin:list')
            ->setDescription('Display a list of installed plugins.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $curdir = getcwd();
        chdir(ConfigRepository::getPluginDirectory());

        $plugins = array_diff(scandir('.'), array('.','..','.gitignore'));

        foreach ($plugins as $plugin) {
            echo $plugin.PHP_EOL;
        }

        chdir($curdir);
    }
}
