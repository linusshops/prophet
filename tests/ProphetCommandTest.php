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

namespace LinusShops\Prophet;

use LinusShops\Prophet\Commands\Scry;
use LinusShops\Prophet\Commands\Validate;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ProphetCommandTest extends \PHPUnit_Framework_TestCase
{
    private $path = './magento';

    public function getJson()
    {
        return <<<JSON
{
    "modules": [
        {
            "path": "vendor/linusshops/prophet-magento-test-module",
            "name": "test-module"
        }
    ]
}
JSON;
    }

    public function getJsonPath()
    {
        return $this->path.'/prophet.json';
    }

    public function setUp()
    {
        if (file_exists($this->getJsonPath())) {
            unlink($this->getJsonPath());
        }

        file_put_contents($this->getJsonPath(), $this->getJson());
    }

    public function tearDown()
    {
        if (file_exists($this->getJsonPath())) {
            unlink($this->getJsonPath());
        }
    }

    public function testScryFullExecution()
    {
        $output = shell_exec("./prophet scry -p ./magento");

        $this->assertRegExp('/OK \(1 test, 1 assertion\)/', $output);
    }

    public function testValidateCommand()
    {
        $application = new Application();
        $application->add(new Validate());

        $command = $application->find('validate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName()
        ), array(
            '-p' => './magento'
        ));

        //$commandTester->getDisplay();
    }
}
