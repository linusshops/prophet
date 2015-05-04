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

use LinusShops\Prophet\Config;
use LinusShops\Prophet\ConfigRepository;
use LinusShops\Prophet\Module;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class Analyze extends Command
{
    protected function configure()
    {
        $this
            ->setName('analyze')
            ->setDescription(
                'Scan the project and attempt to make a prophet.json. When using'
                .' Analyze, prophet will search in the vendor directory, as it'
                .' expects you to be managing your modules with composer. This'
                .' will only detect modules that are already configured to test'
                .' with prophet.'
            )
            ->addOption(
                'path',
                'p',
                InputOption::VALUE_REQUIRED,
                'Set location to create prophet.json (defaults to cwd)',
                'prophet.json'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        //Check if the vendor directory exists
        if (!is_dir('vendor')) {
            $output->writeln('<error>No vendor directory found.</error>');
            return;
        }

        //Check if prophet.json already exists, warn about possible overwrite.
        if (file_exists($input->getOption('path'))) {
            $question = new ConfirmationQuestion(
                "<error>prophet.json already exists. Overwrite?</error>",false
            );

            if (!$helper->ask($input, $output, $question)) {
                return;
            }
        }

        $output->writeln('Scanning vendor directory for testable modules...');

        //Descend into the vendor directory and build a list of testable modules
        $directory = new \RecursiveDirectoryIterator('vendor');
        $files = new \RecursiveIteratorIterator($directory);

        $paths = array();

        if ($output->isVerbose()) {
            $output->writeln('Scanning files..');
        }

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            if ($output->isVeryVerbose()) {
                $output->writeln($file->getRealPath());
            }

            if ($file->getFilename() == 'phpunit.xml') {
                $paths[] = $file->getPath();
            }
        }

        $modulesToWrite = array();

        foreach ($paths as $path) {
            $arrPath = explode(DIRECTORY_SEPARATOR, $path);
            $module = new Module($arrPath[count($arrPath)-1], $path);

            $output->writeln(
                "Detected {$module->getPath()} as a testable module."
            );

            $question = new ConfirmationQuestion(
                "<question>Add {$module->getName()} to prophet.json?</question>",false
            );

            if (!$helper->ask($input, $output, $question)) {
                $output->writeln("<comment>Ignoring {$module->getPath()}</comment>");
            } else {
                $modulesToWrite[] = $module;
            }
        }

        //Write prophet.json
        $output->writeln("Writing prophet.json");

        $pjson = array('modules'=>array());

        foreach ($modulesToWrite as $module) {
            $pjson['modules'][] = array(
                'path'=>$module->getPath(),
                'name'=>$module->getName()
            );
        }

        file_put_contents($input->getOption('path'),json_encode($pjson));
    }
}
