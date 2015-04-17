<?php
/**
 * 
 *
 * @author Sam Schmidt
 * @date 2015-04-17
 * @company Linus Shops
 */

namespace LinusShops\Prophet;

class TestRunner
{
    /**
     * Call the same functions used by the CLI PHPUnit
     * to engage the tests
     * @param string $phpunitXml path to the phpunit.xml to use
     * @return int
     *      0: Tests successful
     *      1: Tests failed
     *      2: Failed with exception
     */
    public function run($phpunitXml)
    {
        $runner = new \PHPUnit_TextUI_Command();
        $options = array(
            '--configuration' => $phpunitXml
        );

        return $runner->run($options, false);
    }
}
