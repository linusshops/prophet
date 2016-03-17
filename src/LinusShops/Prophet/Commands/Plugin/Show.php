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
use LinusShops\Prophet\Command;
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
        $frameworkDir = PROPHET_ROOT_DIR.'/plugins';

        $frameworks = array_diff(scandir($frameworkDir), array('.','..','.gitignore'));

        foreach ($frameworks as $f) {
            echo $f.PHP_EOL;
        }
    }
}
