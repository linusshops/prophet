<?php
/**
     *
     *
     * @author Sam Schmidt <samuel@dersam.net>
     * @date 2015-10-13
     * @company Linus Shops
     */

namespace LinusShops\Prophet\Commands\Scry;


use LinusShops\Prophet\Commands\Scry;
use LinusShops\Prophet\Module;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Jest extends Scry
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('scry:jest')
            ->setDescription('Run javascript tests using Jest')
        ;
    }

    function doTest(
        Module $module,
        InputInterface $input,
        OutputInterface $output
    ) {
        $cmd = "cd {$module->getPath()} && jest";
        $this->cliHelper()->veryVerbose($cmd, $output);
        passthru($cmd);
    }
}
