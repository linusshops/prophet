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

use LinusShops\Prophet\Commands\Init;
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

    public function getJsonWithInvalidPath()
    {
        return <<<JSON
{
    "modules": [
        {
            "path": "vendor/linusshops/prophet-magento-fake-module",
            "name": "fake-module"
        }
    ]
}
JSON;
    }

    public function getPhpUnitXml()
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>

<phpunit colors="true">
    <testsuites>
        <testsuite name="Prophet Test Suite">
            <directory suffix="Test.php">./tests/</directory>
        </testsuite>
    </testsuites>

    <filter>
        <whitelist>
            <directory>./src</directory>
        </whitelist>
    </filter>
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
        //This is kind of a hack for now- phpunit context gets shared
        //when executing nested instances of phpunit, so spin it off
        //and check the returned output for expected string
        $this->destroyPhpunitXml();
        $this->makePhpunitXml();
        $output = shell_exec("./prophet scry -p ./magento");

        $this->assertRegExp('/OK \(1 test, 1 assertion\)/', $output);
        $this->destroyPhpunitXml();
    }

    public function testMissingProphetJson()
    {
        $this->tearDown();

        $application = new Application();
        $application->add(new Validate());

        $command = $application->find('validate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--path' => './magento'
        ));

        $output =  $commandTester->getDisplay();

        $this->assertRegExp('/Failed to parse/', $output);
    }

    public function testValidateCommand()
    {
        $this->makePhpunitXml();

        $application = new Application();
        $application->add(new Validate());

        $command = $application->find('validate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--path' => './magento'
        ));

        $output =  $commandTester->getDisplay();

        $this->assertRegExp('/test-module validated/', $output);

        $this->destroyPhpunitXml();
    }

    public function testValidateCommandWithoutPhpunitXml()
    {
        $this->destroyPhpunitXml();
        $application = new Application();
        $application->add(new Validate());

        $command = $application->find('validate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--path' => './magento'
        ));

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/does not contain a phpunit.xml/', $output);
    }

    public function testValidateCommandWithInvalidPath()
    {
        file_put_contents($this->getJsonPath(), $this->getJsonWithInvalidPath());
        $application = new Application();
        $application->add(new Validate());

        $command = $application->find('validate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--path' => './magento'
        ));

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/is not valid/', $output);
    }

    public function testInitCommand()
    {
        $this->destroyPhpunitXml();

        $application = new Application();
        $application->add(new Init());
        $command = $application->find('init');

        //We'll need to mock the question helpers
        $question = $this->getMock(
            'Symfony\Component\Console\Helper\QuestionHelper',
            array('ask'));
        $question->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(true));

        $command->getHelperSet()->set($question, 'question');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--path' => './magento'
        ));

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Initializing test-module/', $output);
    }
}
