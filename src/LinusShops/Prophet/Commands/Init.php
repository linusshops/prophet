<?php
/**
 * Initializes a module with the expected structure for testing.
 *
 * @author Sam Schmidt
 * @date 2015-04-17
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Commands;

use LinusShops\Prophet\Config;
use LinusShops\Prophet\Module;
use LinusShops\Prophet\ProphetCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Init extends ProphetCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('init')
            ->setDescription(
                'Initialize all modules in prophet.json for testing that do not contain the expected structure.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $moduleList = Config::getModuleList();

        /** @var Module $module */
        foreach ($moduleList as $module) {
            if (!$module->validate()) {
                $output->writeln("Initializing {$module->getName()}");
                $module->createTestStructure();
            } else {
                $output->writeln("Skipping {$module->getName()}: already initialized.");
            }
        }
    }
}