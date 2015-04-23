<?php
/**
 * Command to verify that modules in prophet.json are all testable.
 *
 * @author Sam Schmidt
 * @date 2015-04-17
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Commands;

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
            ->setName('Check that all modules in prophet.json are valid to test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
    }
}
