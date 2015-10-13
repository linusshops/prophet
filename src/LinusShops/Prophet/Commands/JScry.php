<?php
/**
 * Execute Javascript test suites using Jest
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @date 2015-10-13
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Commands;

use LinusShops\Prophet\Config;
use LinusShops\Prophet\ProphetCommand;
use LinusShops\Prophet\ConsoleHelper;
use LinusShops\Prophet\ConfigRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class JScry extends ProphetCommand
{
    public function __construct()
    {
        parent::__construct();

        $this->cli = new ConsoleHelper();
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('jscry')
            ->setDescription('Run javascript tests')
            ->addOption(
                'module',
                'm',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'If set, prophet will only test modules matching the provided names.'
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
            $repeat = (int)$input->getOption('every');

            do {
                foreach ($config->getModuleList() as $module) {
                    if ($this->checkIfRequested($modulesRequested, $module,
                        $output)
                    ) {
                        if ($this->loadClasses($modulesRequested, $config,
                            $input,
                            $output)
                        ) {
                            $cmd = "cd {$module->getPath()} && jest";
                            $this->cli->veryVerbose($cmd, $output);
                            passthru($cmd);
                        }
                    }
                }
                if ($repeat) {
                    $output->writeln("<info>Next test run at " . date('Y-m-d H:i:s',
                            strtotime("+{$repeat} seconds")) . '</info>');
                    sleep($repeat);
                }
            } while ($repeat);
        }
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
