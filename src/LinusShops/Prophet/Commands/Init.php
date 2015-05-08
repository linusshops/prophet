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
use LinusShops\Prophet\ConfigRepository;
use LinusShops\Prophet\Module;
use LinusShops\Prophet\ProphetCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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
        $setupSuccessful = parent::execute($input, $output);

        if ($setupSuccessful) {
            $config = ConfigRepository::getConfig();

            /** @var Module $module */
            foreach ($config->getModuleList() as $module) {
                if (!$module->validate()) {
                    $this->initModule($config, $module, $input, $output);
                } else {
                    $output->writeln("Skipping {$module->getName()}: already initialized.");
                }
            }
        }
    }

    private function initModule(Config $config, Module $module, InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Initializing {$module->getName()}");
        $module->createTestStructure($input->getOption('path'));

        //Add to prophet.json
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            "<info>Add module to prophet.json?</info>",
            false
        );

        if ($helper->ask($input, $output, $question)) {
            $config->writeModule($module);
        }
    }
}
