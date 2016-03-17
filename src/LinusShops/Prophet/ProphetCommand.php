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
        $loaded = $this->checkFile($input, $output);

        $loaded = $loaded ?
            ConfigRepository::loadConfig($input->getOption('path'), $input->getOption('path'))
            : $loaded;

        return $loaded;
    }

    private function checkFile(InputInterface $input, OutputInterface $output)
    {
        $exists = true;
        $path = $input->getOption('path').'/prophet.json';
        if (file_exists($path) === false) {
            $output->writeln(
                "<error>Failed to parse {$path}: file not found.</error>"
            );

            $exists = false;
        }

        return $exists;
    }


}
