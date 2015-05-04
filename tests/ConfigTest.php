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
                    'name'=>'module2',
                    'path'=>'path/to/module2',
                    'options'=>array(
                        'isolate'=>true
                    )
                ),
                array(
                    'name'=>'module3',
                    'path'=>'path/to/module3',
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

        /** @var \LinusShops\Prophet\Module $module */
        $module = Config::getModule('module1');
        $this->assertInstanceOf('\LinusShops\Prophet\Module', $module);
        $this->assertTrue($module->getName()=='module1');
        $this->assertTrue($module->getPath()=='path/to/module1');
        $this->assertFalse($module->isIsolated());

        $module = Config::getModule('module2');
        $this->assertInstanceOf('\LinusShops\Prophet\Module', $module);
        $this->assertTrue($module->getName()=='module2');
        $this->assertTrue($module->getPath()=='path/to/module2');
        $this->assertTrue($module->isIsolated());
        $this->assertTrue($module->getOption('isolate'));
    }

    /**
     * @expectedException \LinusShops\Prophet\Exceptions\InvalidConfigException
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
