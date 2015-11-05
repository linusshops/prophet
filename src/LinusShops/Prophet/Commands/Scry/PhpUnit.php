<?php
/**
     *
     *
     * @author Sam Schmidt <samuel@dersam.net>
     * @date 2015-10-13
     * @company Linus Shops
     */

namespace LinusShops\Prophet\Commands\Scry;


use LinusShops\Prophet\Commands\Scry;
use LinusShops\Prophet\Events;
use LinusShops\Prophet\Magento;
use LinusShops\Prophet\Module;
use LinusShops\Prophet\TestRunner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PhpUnit extends Scry
{
    private $isolated;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('scry:phpunit')
            ->setDescription('Run phpunit tests for modules in prophet.json')
            ->addOption(
                'isolated',
                null,
                InputOption::VALUE_NONE,
                'Indicates to prophet that it is running as a subprocess, and'.
                ' should assume it has only one module to run.'
            )
        ;
    }

    private function isIsolated()
    {
        return $this->isolated;
    }


    public function doTest(Module $module, InputInterface $input, OutputInterface $output)
    {
        $this->isolated = $input->getOption('isolated');
        $dispatcher = new EventDispatcher();

        if (!$this->isIsolated()) {
            $output->writeln("<info>Isolating {$module->getName()}</info>");
            $cmd = $this->getProphetCall()
                . " scry:phpunit --isolated -m {$module->getName()} -p {$input->getOption('path')}";
            if ($input->getOption('coverage')) {
                $cmd .= ' --coverage '.$input->getOption('coverage');
            }
            if ($input->getOption('filter')) {
                $cmd .= ' --filter '.$input->getOption('filter');
            }
            $this->cliHelper()->veryVerbose($cmd, $output);
            passthru($cmd);
        } else {
            $modulePath = $module->getPath();
            $rootPath = $input->getOption('path');

            $this->cliHelper()->veryVerbose('Loading Magento classes...',
                $output);

            $options = array();

            Events::dispatch(Events::PROPHET_PREMAGENTO, $options);
            Magento::bootstrap($input->getOption('path'), $options);
            Events::dispatch(Events::PROPHET_POSTMAGENTO);

            //Register a custom autoloader so that controller classes
            //can be loaded for testing.
            $localPool = function($classname) use (
                $modulePath,
                $rootPath
            ) {
                if (strpos($classname,
                        'Controller') !== false
                ) {
                    $parts = explode('_', $classname);

                    $loadpath = $rootPath.'/'.$modulePath.'/src/app/code/local/'
                        . $parts[0].'/'.$parts[1]
                        . '/controllers';
                    for ($i = 2; $i<count($parts); $i++) {
                        $loadpath .= '/'.$parts[$i];
                    }

                    $loadpath .= '.php';

                    if (file_exists($loadpath)) {
                        include $loadpath;
                    }
                }
            };

            $communityPool = function($classname) use (
                $modulePath,
                $rootPath
            ) {
                if (strpos($classname,
                        'Controller') !== false
                ) {
                    $parts = explode('_', $classname);

                    $loadpath = $rootPath.'/'.$modulePath.'/src/app/code/local/'
                        . $parts[0].'/'.$parts[1]
                        . '/controllers';
                    for ($i = 2; $i<count($parts); $i++) {
                        $loadpath .= '/'.$parts[$i];
                    }

                    $loadpath .= '.php';

                    if (file_exists($loadpath)) {
                        include $loadpath;
                    }
                }
            };

            //Prophet override loader.
            //Prophet gives itself priority over all other loaders, as this
            //allows the injection of testing specific classes. If there is
            //a class you wish to override in testing, you can do this by adding
            //a file with its exact class name in the tests/phpunit/classes directory
            //in the module.
            $overrideLoader = function($classname) use (
                $modulePath,
                $rootPath
            ) {
                $loadpath = $rootPath.'/'.$modulePath.'/tests/phpunit/classes/'.$classname.'.php';

                if (file_exists($loadpath)) {
                    include $loadpath;
                }
            };

            //This autoloader is prepended, as the Varien autoloader
            //will cause everything to die if it can't find the class. Also,
            //this will give us a hook in the future if Prophet ever
            //needs to intercept class loading.
            spl_autoload_register($communityPool, true,
                true);
            spl_autoload_register($localPool, true, true);
            spl_autoload_register($overrideLoader, true,
                true);

            $output->writeln('Starting tests for ['.$module->getName().']');
            $eventData = array($module, 'phpunit');
            Events::dispatch(Events::PROPHET_PREMODULE, $eventData);
            $runner = new TestRunner($module);
            $runner->run(
                $path = $input->getOption('path').'/'.$module->getPath(),
                array(
                    'coverage' => $input->getOption('coverage'),
                    'filter' => $input->getOption('filter')
                )
            );
            Events::dispatch(Events::PROPHET_POSTMODULE, $eventData);
        }
    }

    protected function getRepeatInterval($optionEvery)
    {
        $repeat = parent::getRepeatInterval($optionEvery);
        return !$this->isIsolated() ? $repeat : false;
    }


}
