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

        $phantomjs = null;
        $selenium = null;

        //Confirm phantomjs and selenium server are running
        if (!$this->checkProcess('phantomjs')) {
            $phantomjs = proc_open(
                'phantomjs --webdriver=8643 --ssl-protocol=any --ignore-ssl-errors=true',
                array(
                    0 => array("pipe", "r"),
                    1 => array("pipe", "w"),
                    2 => array("pipe", "w")
                ),
                $pipes
            );
            sleep(2);
            if (!$this->checkProcess('phantomjs')) {
                $cli->write('<error>failed to start phantomjs.</error>', $output);
                return;
            }

            $cli->write('phantomjs started', $output);
        }

        if (!$this->checkProcess('selenium-server')) {
            $selenium = proc_open('selenium-server',
                array(
                    0 => array("pipe", "r"),
                    1 => array("pipe", "w"),
                    2 => array("pipe", "w")
                ),
                $pipes2
            );
            sleep(2);
            if (!$this->checkProcess('selenium-server')) {
                $cli->write('<error>selenium-server not running, exiting.</error>',
                    $output);
                return;
            }

            $cli->write('selenium-server started', $output);
        }

        //Instantiate behat with a custom console Input to
        //avoid pollution from Prophet's cli input.
        $input = new ProphetInput(array());

        $factory = new ApplicationFactory();
        $app = $factory->createApplication();
        $app->setAutoExit(false);
        $app->run($input);

        proc_terminate($phantomjs);
        proc_terminate($selenium);

    }

    public function checkProcess($name)
    {
        $running = false;
        exec("pgrep -f {$name}", $output, $return);
        if ($return == 0) {
            $running = true;
        }

        return $running;
    }
}
