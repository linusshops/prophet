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

class Barista extends Scry
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('scry:barista')
            ->setDescription('Run mocha+zombie tests for modules in prophet.json')
        ;
    }

    public function doTest(
        Module $module,
        InputInterface $input,
        OutputInterface $output
    ) {
        $cmd = "mocha {$module->getPath()}/tests/mocha";
        $this->cliHelper()->veryVerbose($cmd, $output);
        passthru($cmd);
    }
}
