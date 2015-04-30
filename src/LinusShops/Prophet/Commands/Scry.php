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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $modulesRequested = $input->getOption('module');

        $moduleList = Config::getModuleList();
        if (empty($moduleList)) {
            $output->writeln('<error>No modules found in prophet.json.</error>');

            if ($output->isVeryVerbose()) {
                $output->writeln(print_r($moduleList, true));
            }

            return;
        }

        if ($output->isVeryVerbose()) {
            $output->writeln('Loading Magento classes');
        }

        Magento::bootstrap();

        if (count($modulesRequested)>0) {
            $output->writeln('<info>Module list provided, will only test:</info>');

            foreach ($modulesRequested as $requestedModule) {
                $output->writeln('<info>    -- '.$requestedModule.'</info>');
            }
        }

        /** @var Module $module */
        foreach ($moduleList as $module) {
            if (!in_array($module->getName(), $modulesRequested)) {
                if ($output->isVerbose()) {
                    $output->writeln('Skipping '.$module->getName());
                }
                continue;
            }

            $output->writeln('Starting tests for ['.$module->getName().']');

            $runner = new TestRunner();
            $runner->run($module->getPath());
        }
    }
}
