<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @date 2015-10-14
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Commands\Scry;


use LinusShops\Prophet\Commands\Scry;
use LinusShops\Prophet\Module;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Casper extends Scry
{

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('scry:casper')
            ->setDescription('Run in-browser tests using CasperJS')
        ;
    }

    public function doTest(
        Module $module,
        InputInterface $input,
        OutputInterface $output
    ) {
        $cmd = "cd {$module->getPath()} && casperjs";
        //TODO: read config file from module
        $this->cliHelper()->veryVerbose($cmd, $output);
        passthru($cmd);
    }
}
