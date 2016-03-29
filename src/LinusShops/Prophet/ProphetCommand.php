<?php
/**
     * 
     *
     * @author Sam Schmidt
     * @date 2015-04-17
     * @company Linus Shops
     */

namespace LinusShops\Prophet;

use LinusShops\Prophet\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProphetCommand extends Command
{
    protected $modules = array();

    protected function configure()
    {
        $this->addOption(
            'path',
            'p',
            InputArgument::OPTIONAL,
            'Path to the magento root (defaults to current directory)',
            '.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return Config::getConfigFromFile($input->getOption('path'));
    }
}
