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
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ScryTest extends \PHPUnit_Framework_TestCase
{
    private $path = './magento/prophet.json';

    public function setUp()
    {
        if (file_exists($this->path)) {
            unlink($this->path);
        }

        file_put_contents($this->path, <<<JSON
{
    "modules": [
        {
            "path": "vendor/linusshops/prophet-magento-test-module",
            "name": "test-module"
        }
    ]
}
JSON
        );
    }

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

        echo $output;
    }

    public function tearDown()
    {
        if (file_exists($this->path)) {
            unlink($this->path);
        }
    }
}
