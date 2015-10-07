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
use LinusShops\Prophet\Magento;
use LinusShops\Prophet\Module;
use LinusShops\Prophet\ProphetCommand;
use LinusShops\Prophet\TestRunner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Scry extends ProphetCommand
{
    /** @var ConsoleHelper  */
    private $cli;

    public function __construct()
    {
        parent::__construct();

        $this->cli = new ConsoleHelper();
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
            ->setName('scry')
            ->setDescription('Run the test suites defined in prophet.json')
            ->addOption(
                'module',
                'm',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'If set, prophet will only test modules matching the provided names.'
            )
            ->addOption(
                'isolated',
                null,
                InputOption::VALUE_NONE,
                'Indicates to prophet that it is running as a subprocess, and'.
                ' should assume it has only one module to run.'
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $setupSuccessful = parent::execute($input, $output);

        if ($setupSuccessful) {
            $config = ConfigRepository::getConfig();
            $modulesRequested = $input->getOption('module');
            $dispatcher = new EventDispatcher();
            $repeat = (int)$input->getOption('every');
            $repeat = !$this->isIsolated($input) ? $repeat : false;

            if ($this->loadClasses($modulesRequested, $config, $input, $output)) {
                /** @var Module $module */

                do {
                    foreach ($config->getModuleList() as $module) {
                        if ($this->checkIfRequested($modulesRequested, $module,
                            $output)
                        ) {
                            if (!$this->isIsolated($input)) {
                                $output->writeln("<info>Isolating {$module->getName()}</info>");
                                $cmd = $this->getProphetCall()
                                    . " scry --isolated -m {$module->getName()} -p {$input->getOption('path')}";
                                if ($input->getOption('coverage')) {
                                    $cmd .= ' --coverage ' . $input->getOption('coverage');
                                }
                                if ($input->getOption('filter')) {
                                    $cmd .= ' --filter ' . $input->getOption('filter');
                                }
                                $this->cli->veryVerbose($cmd, $output);
                                passthru($cmd);
                            } else {
                                $path = $module->getPath() . '/tests/ProphetEvents.php';
                                if (file_exists($path)) {
                                    include $path;
                                }

                                $modulePath = $module->getPath();
                                $rootPath = $input->getOption('path');

                                $this->cli->veryVerbose('Loading Magento classes...',
                                    $output);

                                $dispatcher->dispatch(Events::PROPHET_PREMAGENTO);
                                Magento::bootstrap($input->getOption('path'));
                                $dispatcher->dispatch(Events::PROPHET_POSTMAGENTO);

                                //Register a custom autoloader so that controller classes
                                //can be loaded for testing.
                                $localPool = function ($classname) use (
                                    $modulePath,
                                    $rootPath
                                ) {
                                    if (strpos($classname,
                                            'Controller') !== false
                                    ) {
                                        $parts = explode('_', $classname);

                                        $loadpath = $rootPath . '/' . $modulePath . '/src/app/code/local/'
                                            . $parts[0] . '/' . $parts[1]
                                            . '/controllers';
                                        for ($i = 2; $i < count($parts); $i++) {
                                            $loadpath .= '/' . $parts[$i];
                                        }

                                        $loadpath .= '.php';

                                        if (file_exists($loadpath)) {
                                            include $loadpath;
                                        }
                                    }
                                };

                                $communityPool = function ($classname) use (
                                    $modulePath,
                                    $rootPath
                                ) {
                                    if (strpos($classname,
                                            'Controller') !== false
                                    ) {
                                        $parts = explode('_', $classname);

                                        $loadpath = $rootPath . '/' . $modulePath . '/src/app/code/local/'
                                            . $parts[0] . '/' . $parts[1]
                                            . '/controllers';
                                        for ($i = 2; $i < count($parts); $i++) {
                                            $loadpath .= '/' . $parts[$i];
                                        }

                                        $loadpath .= '.php';

                                        if (file_exists($loadpath)) {
                                            include $loadpath;
                                        }
                                    }
                                };

                                $overrideLoader = function ($classname) use (
                                    $modulePath,
                                    $rootPath
                                ) {
                                    $loadpath = $rootPath . '/' . $modulePath . '/tests/classes/' . $classname . '.php';

                                    if (file_exists($loadpath)) {
                                        include $loadpath;
                                    }
                                };

                                //This autoloader is prepended, as the Varien autoloader
                                //will cause everything to die if it can't find the class. Also,
                                //this will give us a hook in the future if Prophet ever
                                //needs to intercept class loading.
                                spl_autoload_register($communityPool, true,
                                    true);
                                spl_autoload_register($localPool, true, true);
                                spl_autoload_register($overrideLoader, true,
                                    true);

                                $output->writeln('Starting tests for [' . $module->getName() . ']');
                                $dispatcher->dispatch(Events::PROPHET_PREMODULE,
                                    new Events\Module($module));
                                $runner = new TestRunner($module);
                                $runner->run(
                                    $path = $input->getOption('path') . '/' . $module->getPath(),
                                    array(
                                        'coverage' => $input->getOption('coverage'),
                                        'filter' => $input->getOption('filter')
                                    )
                                );
                                $dispatcher->dispatch(Events::PROPHET_POSTMODULE,
                                    new Events\Module($module));
                            }
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

    private function isIsolated(InputInterface $input)
    {
        return $input->getOption('isolated');
    }

    private function checkIfRequested($modulesRequested, $module, OutputInterface $output)
    {
        $requested = true;
        if (count($modulesRequested)>0 && !in_array($module->getName(), $modulesRequested)) {
            $this->cli->verbose('Skipping '.$module->getName(), $output);

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
}
