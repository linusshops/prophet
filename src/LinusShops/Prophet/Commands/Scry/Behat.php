<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @date 2015-10-15
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Commands\Scry;


use Behat\Behat\ApplicationFactory;
use LinusShops\Prophet\Adapters\Behat\ProphetInput;
use LinusShops\Prophet\Commands\Scry;
use LinusShops\Prophet\Module;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Behat extends Scry
{

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('scry:behat')
            ->setDescription('Run behat tests for modules in prophet.json')
        ;
    }

    public function doTest(
        Module $module,
        InputInterface $input,
        OutputInterface $output
    ) {
        chdir($module->getPath());
        //Instantiate behat with a faked InputInterface to
        //avoid pollution from Prophet's cli input.
        $input = new ProphetInput();

        $factory = new ApplicationFactory();
        $factory->createApplication()->run($input);
    }
}
