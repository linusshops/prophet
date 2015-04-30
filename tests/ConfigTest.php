<?php
use LinusShops\Prophet\Config;
use LinusShops\Prophet\Exceptions\InvalidConfigException;

/**
 * 
 *
 * @author Sam Schmidt
 * @date 2015-04-30
 * @company Linus Shops
 */

class ConfigTest extends PHPUnit_Framework_TestCase {
    public function getValidParsedConfig() {
        return array(
            'modules' => array(
                array(
                    'name'=>'module1',
                    'path'=>'path/to/module1'
                ),
                array(
                    'name'=>'module1',
                    'path'=>'path/to/module1',
                    'options'=>array(
                        'isolate'=>true
                    )
                ),
                array(
                    'name'=>'module2',
                    'path'=>'path/to/module2',
                    'options'=>array(
                        'isolate'=>false
                    )
                )
            )
        );
    }

    public function testLoadConfig()
    {
        Config::loadConfig($this->getValidParsedConfig());

        $modules = Config::getModuleList();

        $this->assertTrue(count($modules)==3);

        $module = Config::getModule('module1');
        $this->assertInstanceOf('\LinusShops\Prophet\Module',$module);
    }

    /**
     * @expectedException InvalidConfigException
     */
    public function testInvalidLoadConfig()
    {
        $config = false;
        Config::loadConfig($config);
    }

    public function testWriteModule()
    {

    }
}
