<?php
/**
 * Run tests using the specified test framework
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-03-16
 */

namespace LinusShops\Prophet\Commands;

use LinusShops\Prophet\Config;
use LinusShops\Prophet\ConfigRepository;
use LinusShops\Prophet\Module;
use LinusShops\Prophet\Command;
use LinusShops\Prophet\Injector;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command
{
    protected $modules = [];

    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run tests using the specified test framework')
            ->addArgument(
                'framework',
                InputArgument::OPTIONAL,
                'The test framework to use',
                'phpunit'
            )
            ->addOption(
                'module',
                'm',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'If set, prophet will only test modules matching the provided names.'
            )
            ->addOption(
                'path',
                'p',
                InputArgument::OPTIONAL,
                'Path to the magento root (defaults to current directory)',
                '.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->isConfigValid($input, $output)) {

            return;
        }
        $framework = $input->getArgument('framework');

        $config = ConfigRepository::getConfig();
        $modulesRequested = $input->getOption('module');

        if (!$this->loadClasses($modulesRequested, $config, $input, $output)) {
            return;
        }

        /** @var Module $module */
        foreach ($config->getModuleList() as $module) {
            if ($this->checkIfRequested($modulesRequested, $module, $output)) {
                Injector::setCurrentModulePath($module->getPath());
                $path = $module->getPath().'/tests/ProphetEvents.php';
                if (file_exists($path)) {
                    include $path;
                }

                $modulePath = $module->getPath();
                $magentoPath = $input->getOption('path');

                $output->writeln('Running ['.$framework.'] tests for '.$module->getName());

                $res = $this->runTestFramework($framework, $modulePath, $magentoPath);
                $res ? null : $output->writeln("Failed to load framework {$framework} - loader not found.");
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

    protected function runTestFramework($framework, $modulePath, $magentoPath)
    {
        $path = PROPHET_ROOT_DIR.'/frameworks/'.$framework.'/loader.php';
        if (!is_file($path)) {
            return false;
        }

        $cmd = "php {$path} ".PROPHET_ROOT_DIR." {$modulePath} {$magentoPath}";
        $this->shell($cmd);

        return true;
    }

    protected function isConfigValid(InputInterface $input, OutputInterface $output)
    {
        $loaded = $this->checkFile($input, $output);

        $loaded = $loaded ?
            ConfigRepository::loadConfig($input->getOption('path'), $input->getOption('path'))
            : $loaded;

        return $loaded;
    }

    private function checkFile(InputInterface $input, OutputInterface $output)
    {
        $exists = true;
        $path = $input->getOption('path').'/prophet.json';
        if (file_exists($path) === false) {
            $output->writeln(
                "<error>Failed to parse {$path}: file not found.</error>"
            );

            $exists = false;
        }

        return $exists;
    }

    private function checkIfRequested($modulesRequested, $module, OutputInterface $output)
    {
        $requested = true;
        if (count($modulesRequested)>0 && !in_array($module->getName(), $modulesRequested)) {
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
