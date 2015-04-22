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
        $configFile = $input->getOption('config');
        if (file_exists($configFile) === false) {
            die("Failed to parse {$configFile}: file not found.".PHP_EOL);
        }

        $json = json_decode(file_get_contents($configFile), true);
        if ($json === false) {
            die("Failed to parse {$configFile}: invalid json.".PHP_EOL);
        }

        Config::loadConfig($json);

    }

}
