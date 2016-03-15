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
        chdir($module->getPath());

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
        $options = new Events\Options(array($module, 'behat'));
        Events::dispatch(Events::PROPHET_PREMODULE, $options);

        $app->run($input);

        Events::dispatch(Events::PROPHET_POSTMODULE, $options);
    }
}
