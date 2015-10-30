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
use LinusShops\Prophet\Events;
use LinusShops\Prophet\Module;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Behat extends Scry
{

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('scry:behat')
            ->setDescription('Run behat tests for modules in prophet.json')
            ->addOption(
                'feature',
                null,
                InputOption::VALUE_OPTIONAL,
                'Run a specific feature only.'
            )
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
        if ($this->checkProcess('phantomjs')) {
            $this->killPhantom();
        }

        $phantomjs = $this->startPhantom($output);
        if (!$phantomjs) {
            return;
        }

        if ($this->checkProcess('selenium-server')) {
            $this->killSelenium();
        }

        $selenium = $this->startSelenium($output);
        if (!$selenium) {
            return;
        }

        //Instantiate behat with a custom console Input to
        //avoid pollution from Prophet's cli input.
        $behatInput = array('behat');

        if ($input->getOption('feature') != null) {
            $behatInput[] = 'tests/behat/'.$input->getOption('feature');
        }

        $input = new ArgvInput($behatInput);

        $factory = new ApplicationFactory();
        $app = $factory->createApplication();
        $app->setAutoExit(false);
        $dispatcher = new EventDispatcher();
        $dispatcher->dispatch(Events::PROPHET_PREMODULE,
            new Events\Module($module, 'behat'));
        $app->run($input);
        $dispatcher->dispatch(Events::PROPHET_POSTMODULE,
            new Events\Module($module, 'behat'));
        //Phantom doesn't seem to respond to normal signalling.
        //Known issue when using GhostDriver- just kill it.
        $this->killPhantom();
        //Ugh.
        $this->killSelenium();
    }

    public function killPhantom()
    {
        shell_exec('pkill phantomjs');
    }

    public function startPhantom($output)
    {
        $cli = new ConsoleHelper();
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
            return false;
        }

        $cli->write('phantomjs started', $output);
        return $phantomjs;
    }

    public function killSelenium()
    {
        shell_exec('pkill -f selenium');
    }

    public function startSelenium($output)
    {
        $cli = new ConsoleHelper();
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
            return false;
        }

        $cli->write('selenium-server started', $output);
        return $selenium;
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
