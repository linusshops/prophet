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
    private $modulePath = '/vendor/linusshops/prophet-magento-test-module';

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

    public function getPhpUnitXml()
    {
        return <<<XML
<phpunit colors="true">
    <testsuites>
        <testsuite name="Dummy Test Suite">
            <directory suffix="Test.php">./tests/</directory>
        </testsuite>
    </testsuites>
</phpunit>
XML;

    }

    public function getJsonPath()
    {
        return $this->path.'/prophet.json';
    }

    public function makePhpunitXml()
    {
        if (!file_exists($this->path.'/'.$this->modulePath.'/phpunit.xml')) {
            file_put_contents(
                $this->path.'/'.$this->modulePath.'/phpunit.xml',
                $this->getPhpUnitXml()
            );
        }
    }

    public function destroyPhpunitXml()
    {
        if (file_exists($this->path.'/'.$this->modulePath.'/phpunit.xml')) {
            unlink($this->path.'/'.$this->modulePath.'/phpunit.xml');
        }
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
        //Create dummy phpunit.xml

        ////

        $application = new Application();
        $application->add(new Validate());

        $command = $application->find('validate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--path' => './magento'
        ));

        echo $commandTester->getDisplay();
    }

    public function testValidateCommandWithoutPhpunit()
    {
        $application = new Application();
        $application->add(new Validate());

        $command = $application->find('validate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--path' => './magento'
        ));

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/is not valid/',$output);
        $this->assertRegExp('/does not contain a phpunit.xml/',$output);
    }
}
