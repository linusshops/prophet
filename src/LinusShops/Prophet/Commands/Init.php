<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-04-01
 */

namespace LinusShops\Prophet\Commands;

use LinusShops\Prophet\Command;
use LinusShops\Prophet\Framework;
use LinusShops\Prophet\Frameworks\Repository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Init extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('init')
            ->setDescription('In a module directory, initialize a specified test framework')
            ->addArgument(
                'framework',
                InputArgument::REQUIRED,
                'The test framework to use'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Framework $framework */
        $framework = Repository::get()->getFramework($input->getArgument('framework'));
        $framework->init();
    }
}
