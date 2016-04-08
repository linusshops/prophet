<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-02-12
 */

namespace LinusShops\Prophet\Commands;

use LinusShops\Prophet\Framework;
use LinusShops\Prophet\Frameworks\Repository;
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

        $frameworks = Repository::get()->getFrameworks();

        $modules = $config->getModules();

        /** @var Module $module */
        foreach ($modules as $module) {
            /** @var Framework $framework */
            $line = $module->getName().' ';
            foreach ($frameworks as $framework) {

                if ($framework->validateModule($module)) {
                    $line .= " <info>{$framework->getName()}</info>";
                }
            }

            $output->writeln($line);
        }
    }

}
