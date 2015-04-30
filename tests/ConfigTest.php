<?php
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

    }

    public function testInvalidLoadConfig()
    {

    }

    public function testGetModule()
    {

    }

    public function testGetModuleList()
    {

    }

    public function testWriteModule()
    {

    }
}
