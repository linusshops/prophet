<?php
use LinusShops\Prophet\Config;
use LinusShops\Prophet\ConfigRepository;
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
        ConfigRepository::setConfig(
            new Config($this->getValidParsedConfig())
        );
        $config = ConfigRepository::getConfig();

        $modules = $config->getModuleList();

        $this->assertTrue(count($modules)==3);

        /** @var \LinusShops\Prophet\Module $module */
        $module = $config->getModule('module1');
        $this->assertInstanceOf('\LinusShops\Prophet\Module', $module);
        $this->assertTrue($module->getName()=='module1');
        $this->assertTrue($module->getPath()=='path/to/module1');
        $this->assertFalse($module->isIsolated());

        $module = $config->getModule('module2');
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
        new Config($config);
    }

    public function testWriteModule()
    {
        if (file_exists('prophet.json')) {
            unlink('prophet.json');
        }

        ConfigRepository::setConfig(
            new Config($this->getValidParsedConfig())
        );
        $config = ConfigRepository::getConfig();

        $module = new \LinusShops\Prophet\Module('test','path/to/test');
        $config->writeModule($module);

        $this->assertFileExists('prophet.json');

        $prophet = file_get_contents('prophet.json');

        $parsed = json_decode($prophet, true);
        $config = new Config($parsed);
        $module = $config->getModule('test');
        $this->assertTrue($module->getName()=='test');
        unlink('prophet.json');
    }
}
