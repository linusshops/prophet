<?php
/**
 * 
 *
 * @author Sam Schmidt
 * @date 2015-04-17
 * @company Linus Shops
 */

namespace LinusShops\Prophet;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProphetCommand extends Command
{
    protected $modules = array();

    protected function configure()
    {
        $this->addOption(
            'config',
            null,
            InputArgument::OPTIONAL,
            'Path to the prophet.json file',
            './prophet.json'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loaded = $this->checkFile($input, $output);

        $loaded = $loaded ? $this->loadConfig($input, $output) : $loaded;

        return $loaded;
    }

    private function checkFile(InputInterface $input, OutputInterface $output)
    {
        $exists = true;

        if (file_exists($input->getOption('config')) === false) {
            $output->writeln(
                "<error>Failed to parse {$input->getOption('config')}: file not found.</error>"
            );

            $exists = false;
        }

        return $exists;
    }

    private function loadConfig(InputInterface $input, OutputInterface $output)
    {
        $loaded = true;
        $json = json_decode(file_get_contents($input->getOption('config')), true);
        if ($json === false) {
            $output->writeln(
                "<error>Failed to parse {$input->getOption('config')}: invalid json.</error>"
            );

            $loaded = false;
        } else {
            ConfigRepository::setConfig(new Config($json));
        }

        return $loaded;
    }
}
