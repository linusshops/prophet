<?php
/**
     * Command to verify that modules in prophet.json are all testable.
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

class Validate extends ProphetCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('validate')
            ->setDescription('Check that all modules in prophet.json are valid to test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $setupSuccessful = parent::execute($input, $output);

        if (!$setupSuccessful) {
            return;
        }

        $config = ConfigRepository::getConfig();
        $this->validateModules($config->getModuleList(), $output, $config->getPathPrefix());
    }

    protected function validateModules($moduleList, OutputInterface $output, $pathPrefix = '')
    {
        /** @var Module $module */
        foreach ($moduleList as $module) {
            if ($module->validate($pathPrefix)) {
                $output->writeln($module->getName().' validated.');
            } else {
                foreach ($module->getValidationErrors() as $error) {
                    $output->write("<error>$error</error>");
                }
            }
        }
    }
}
