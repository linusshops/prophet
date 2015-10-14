<?php
/**
     * 
     *
     * @author Sam Schmidt
     * @date 2015-05-04
     * @company Linus Shops
     */

namespace LinusShops\Prophet;


use Symfony\Component\Console\Output\OutputInterface;

class ConsoleHelper
{
    public function write($msg, OutputInterface $output)
    {
        $output->writeln($msg);
    }

    public function verbose($msg, $output)
    {
        if ($output->isVerbose()) {
            $output->writeln($msg);
        }
    }

    public function veryVerbose($msg, $output)
    {
        if ($output->isVeryVerbose()) {
            $output->writeln($msg);
        }
    }
}
