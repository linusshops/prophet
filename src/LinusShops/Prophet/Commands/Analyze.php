<?php
/**
 * Scan the project, looking for testable modules.
 * Then, attempt to build a skeleton prophet.json.
 *
 * @author Sam Schmidt
 * @date 2015-04-23
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Analyze extends Command
{
    protected function configure()
    {
        $this
            ->setName('analyze')
            ->setDescription('Scan the project and attempt to make a prophet.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
