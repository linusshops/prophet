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
use Symfony\Component\Filesystem\Filesystem;

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
        $name = $input->getArgument('name');
        $frameworkDir = PROPHET_ROOT_DIR.'/plugins/'.$name;

        if (is_dir($frameworkDir)) {
            $fs = new Filesystem();
            $fs->remove($frameworkDir);
            echo "Framework {$name} removed.";
        } else {
            echo "{$name} not found.";
        }
    }
}
