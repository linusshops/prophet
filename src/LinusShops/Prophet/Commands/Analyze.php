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

use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Analyze extends Command
{
    protected function configure()
    {
        $this
            ->setName('analyze')
            ->setDescription(
                'Scan the project and attempt to make a prophet.json. When using'
                .' Analyze, prophet will search in the vendor directory, as it'
                .' expects you to be managing your modules with composer.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Check if the vendor directory exists
        if (!is_dir('vendor')) {
            $output->writeln('<error>No vendor directory found.</error>');
            return;
        }

        $output->writeln('Scanning vendor directory for testable modules...');

        //Descend into the vendor directory and build a list of testable modules
        $directory = new \RecursiveDirectoryIterator('vendor');
        $files = new \RecursiveIteratorIterator($directory);

        $paths = array();

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->getFilename() == 'phpunit.xml') {
                $paths[] = $file->getPath();
            }
        }

        print_r($paths);
        //Prompt the user on which modules to include in testing list

        //Write prophet.json
    }
}
