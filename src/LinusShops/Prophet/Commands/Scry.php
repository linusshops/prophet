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
use LinusShops\Prophet\Magento;
use LinusShops\Prophet\Module;
use LinusShops\Prophet\ProphetCommand;
use LinusShops\Prophet\TestRunner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Scry extends ProphetCommand
{
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

        if (!$setupSuccessful) {
            return;
        }

        $modulesRequested = $input->getOption('module');

        if (Config::hasModules()) {
            $output->writeln('<error>No modules found in prophet.json.</error>');

            if ($output->isVeryVerbose()) {
                $output->writeln(print_r(Config::getModuleList(), true));
            }

            return;
        }

        if ($output->isVeryVerbose()) {
            $output->writeln('Loading Magento classes');
        }

        Magento::bootstrap();

        if (count($modulesRequested)>0 && $output->isVerbose()) {
            $this->showRequestedModuleList($modulesRequested, $output);
        }

        /** @var Module $module */
        foreach (Config::getModuleList() as $module) {
            if (count($modulesRequested)>0 && !in_array($module->getName(), $modulesRequested)) {
                if ($output->isVerbose()) {
                    $output->writeln('Skipping '.$module->getName());
                }
                continue;
            }

            if ($module->isIsolated() && !$input->getOption('isolated')) {
                $output->writeln("<info>Isolating {$module->getName()}</info>");
                $cmd = $_SERVER['argv'][0]." scry --isolated -m {$module->getName()}";
                if ($output->isVeryVerbose()) {
                    $output->writeln($cmd);
                }
                passthru($cmd);
            } else {
                $output->writeln('Starting tests for ['.$module->getName().']');

                $runner = new TestRunner();
                $runner->run($module->getPath());
            }
        }
    }

    private function showRequestedModuleList($modules, OutputInterface $output)
    {
        $output->writeln('<info>Module list provided, will only test:</info>');

        foreach ($modules as $requestedModule) {
            $output->writeln('<info>    -- '.$requestedModule.'</info>');
        }
    }
}
