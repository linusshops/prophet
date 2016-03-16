<?php
/**
 * Run tests using the specified test framework
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-03-16
 */

namespace LinusShops\Prophet\Commands;

use LinusShops\Prophet\ConfigRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command
{
    protected $modules = [];

    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run tests using the specified test framework')
            ->addArgument(
                'framework',
                InputArgument::OPTIONAL,
                'The test framework to use',
                'phpunit'
            )
            ->addOption(
                'path',
                'p',
                InputArgument::OPTIONAL,
                'Path to the magento root (defaults to current directory)',
                '.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->isConfigValid($input, $output)) {
            return;
        }

        $res = $this->runTestFramework($input->getArgument('framework'));

        $res ? $output->writeln('Tests completed.') : $output->writeln('Tests failed');
    }

    protected function runTestFramework($framework)
    {
        $path = PROPHET_ROOT_DIR.'/frameworks/'.$framework.'/loader.php';
        if (!is_file($path)) {
            return false;
        }

        $cmd = "php {$path}";
        passthru($cmd);

        return true;
    }

    protected function isConfigValid(InputInterface $input, OutputInterface $output)
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
