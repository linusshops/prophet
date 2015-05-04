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
        //For now, execute prophet and check its output against known values.
        //Some attempts with Symfony CommandTester and mocking were not
        //effective (specifically, the test contexts couldn't be separated),
        // so this should do for now.
        //TODO: revisit this, replace with mocks or different harness to separate contexts

        $output = shell_exec("./prophet scry -p ./magento");

        echo $output;
    }

    public function tearDown()
    {
        if (file_exists($this->path)) {
            unlink($this->path);
        }
    }
}
