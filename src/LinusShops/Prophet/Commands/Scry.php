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
use Symfony\Component\Console\Output\OutputInterface;

class Scry extends ProphetCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('scry')
            ->setDescription('Run the test suites defined in prophet.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $moduleList = Config::getModuleList();
        if (empty($moduleList)) {
            $output->writeln('<error>No modules found in prophet.json.</error>');

            if ($output->isVeryVerbose()) {
                $output->writeln(print_r($moduleList, true));
            }

            return;
        }

        $output->writeln('Loading Magento classes');

        Magento::bootstrap();

        /** @var Module $module */
        foreach ($moduleList as $module) {
            $output->writeln('Starting tests for ['.$module->getName().']');

            $runner = new TestRunner();
            $runner->run($module->getPath());
        }
    }
}
