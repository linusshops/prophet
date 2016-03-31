<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-02-12
 */

namespace LinusShops\Prophet\Commands;

use LinusShops\Prophet\Module;
use LinusShops\Prophet\ProphetCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Show extends ProphetCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('show')
            ->setDescription(
                'Display all module names available to test, with test types available.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = parent::execute($input, $output);
        $modules = $config->getModules();

        /** @var Module $module */
        foreach ($modules as $module) {
            $tests = $module->getAvailableTestFrameworks();
            $line = $module->getName();

            if (count($tests) == 0) {
                $line = "<error>{$line}</error>";
            } else {
                $line .= ' ['.implode(', ', $tests).']';
            }

            $output->writeln($line);
        }
    }

}
