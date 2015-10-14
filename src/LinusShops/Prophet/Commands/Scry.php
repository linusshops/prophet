<?php
/**
     * Command to execute the test suites
     *
     * @author Sam Schmidt
     * @date 2015-04-17
     * @company Linus Shops
     */

namespace LinusShops\Prophet\Commands;

use LinusShops\Prophet\Config;
use LinusShops\Prophet\ConfigRepository;
use LinusShops\Prophet\ConsoleHelper;
use LinusShops\Prophet\Events;
use LinusShops\Prophet\Module;
use LinusShops\Prophet\ProphetCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class Scry extends ProphetCommand
{
    /** @var ConsoleHelper  */
    private $cli;

    public function __construct()
    {
        parent::__construct();
    }

    public function cliHelper()
    {
        if ($this->cli == null) {
            $this->cli = new ConsoleHelper();
        }

        return $this->cli;
    }

    /**
     * @return string
     */
    public function getProphetCall()
    {
        return ConfigRepository::getProphetPath();
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->addOption(
                'module',
                'm',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'If set, prophet will only test modules matching the provided names.'
            )
            ->addOption(
                'coverage',
                'c',
                InputOption::VALUE_OPTIONAL,
                'If set, will display code coverage report',
                false
            )
            ->addOption(
                'filter',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Filter test methods by a regular expression'
            )
            ->addOption(
                'every',
                'e',
                InputOption::VALUE_OPTIONAL,
                'Sleep for X seconds, then rerun tests after completing a test run'
            )
        ;
    }

    protected function getRepeatInterval($optionEvery)
    {
        return (int)$optionEvery;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $setupSuccessful = parent::execute($input, $output);

        if ($setupSuccessful) {
            $config = ConfigRepository::getConfig();
            $modulesRequested = $input->getOption('module');

            $repeat = $this->getRepeatInterval($input->getOption('every'));

            if ($this->loadClasses($modulesRequested, $config, $input, $output)) {
                /** @var Module $module */

                do {
                    foreach ($config->getModuleList() as $module) {
                        if ($this->checkIfRequested($modulesRequested, $module,
                            $output)
                        ) {
                            $this->doTest($module, $input, $output);
                        }
                    }

                    if ($repeat) {
                        $output->writeln("<info>Next test run at ".date('Y-m-d H:i:s', strtotime("+{$repeat} seconds")).'</info>');
                        sleep($repeat);
                    }
                } while ($repeat);
            }
        }
    }

    private function loadClasses($modulesRequested, Config $config, InputInterface $input, OutputInterface $output)
    {
        $loaded = true;
        if ($config->hasModules()) {
            $output->writeln('<error>No modules found in prophet.json.</error>');
            $loaded = false;
        } else {
            $this->showRequestedModuleList($modulesRequested, $output);
        }

        return $loaded;
    }



    private function checkIfRequested($modulesRequested, $module, OutputInterface $output)
    {
        $requested = true;
        if (count($modulesRequested)>0 && !in_array($module->getName(), $modulesRequested)) {
            $this->cliHelper()->verbose('Skipping '.$module->getName(), $output);

            $requested = false;
        }

        return $requested;
    }

    private function showRequestedModuleList($modules, OutputInterface $output)
    {
        if (!$output->isVerbose() && count($modules)>0) {
            $output->writeln('<info>Module list provided, will only test:</info>');

            foreach ($modules as $requestedModule) {
                $output->writeln('<info>    -- '.$requestedModule.'</info>');
            }
        }
    }

    abstract function doTest(Module $module, InputInterface $input, OutputInterface $output);
}
