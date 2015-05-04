<?php
use LinusShops\Prophet\Commands\Scry;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * 
 *
 * @author Sam Schmidt
 * @date 2015-05-04
 * @company Linus Shops
 */

class ScryTest extends PHPUnit_Framework_TestCase
{
    public function testScry()
    {
        $application = new Application();
        $application->add(new Scry());

        $command = $application->find('scry');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '-p'=>'./magento'
        ));

        $output = $commandTester->getDisplay();
    }
}
