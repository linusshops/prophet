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
use LinusShops\Prophet\Magento;
use LinusShops\Prophet\Module;
use LinusShops\Prophet\ProphetCommand;
use LinusShops\Prophet\TestRunner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $setupSuccessful = parent::execute($input, $output);

        if ($setupSuccessful) {
            $config = ConfigRepository::getConfig();
            $modulesRequested = $input->getOption('module');

            if ($this->loadClasses($modulesRequested, $config, $output)) {
                /** @var Module $module */
                foreach ($config->getModuleList() as $module) {
                    if ($this->checkIfRequested($modulesRequested, $module, $output)) {
                        if ($this->isIsolated($module, $input)) {
                            $output->writeln("<info>Isolating {$module->getName()}</info>");
                            $cmd = $this->getProphetCall() . " scry --isolated -m {$module->getName()}";
                            $this->cli->veryVerbose($cmd, $output);
                            passthru($cmd);
                        } else {
                            $output->writeln('Starting tests for [' . $module->getName() . ']');

                            $runner = new TestRunner();
                            $runner->run($module->getPath());
                        }
                    }
                }
            }
        }
    }

    private function loadClasses($modulesRequested, Config $config, OutputInterface $output)
    {
        $loaded = true;
        if ($config->hasModules()) {
            $output->writeln('<error>No modules found in prophet.json.</error>');
            $loaded = false;
        } else {
            $this->cli->veryVerbose('Loading Magento classes...', $output);

            Magento::bootstrap();

            $this->showRequestedModuleList($modulesRequested, $output);
        }

        return $loaded;
    }

    private function isIsolated($module, InputInterface $input)
    {
        return $module->isIsolated() && !$input->getOption('isolated');
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
