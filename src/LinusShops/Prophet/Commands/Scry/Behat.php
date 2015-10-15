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
use LinusShops\Prophet\ConsoleHelper;
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
        $cli = new ConsoleHelper();
        chdir($module->getPath());

        //Confirm phantomjs and selenium server are running
        if (!$this->checkProcess('phantomjs')) {
            $cli->write('<error>phantomjs not running, exiting.</error>', $output);
            return;
        }

        if (!$this->checkProcess('selenium-server')) {
            $cli->write('<error>selenium-server not running, exiting.</error>', $output);
            return;
        }

        //Instantiate behat with a custom console Input to
        //avoid pollution from Prophet's cli input.
        $input = new ProphetInput(array());

        $factory = new ApplicationFactory();
        $factory->createApplication()->run($input);
    }

    public function checkProcess($name)
    {
        $running = false;
        exec("pgrep {$name}", $output, $return);
        if ($return == 0) {
            $running = true;
        }

        return $running;
    }
}
