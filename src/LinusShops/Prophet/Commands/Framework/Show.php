<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-03-16
 */

namespace LinusShops\Prophet\Commands\Framework;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Show extends Command
{
    protected function configure()
    {
        $this
            ->setName('framework:show')
            ->setDescription('Display a list of installed test frameworks.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $curdir = getcwd();

        $frameworkDir = PROPHET_ROOT_DIR.'/frameworks';

        $frameworks = array_diff(scandir($frameworkDir), array('.','..','.gitignore'));

        foreach ($frameworks as $f) {
            echo $f.PHP_EOL;
        }

        chdir($curdir);
    }
}
