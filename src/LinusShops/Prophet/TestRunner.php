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
     * @param string $modulePath path to the phpunit.xml to use
     * @param bool $coverage
     * @return int
     * 0: Tests successful
     * 1: Tests failed
     * 2: Failed with exception
     */
    public function run($modulePath, $coverage = false, $filter = false)
    {
        $runner = new \PHPUnit_TextUI_Command();
        $options = array(
            'phpunit',
            '--configuration',
            $modulePath.'/phpunit.xml'
        );

        if ($filter !== false) {
            $options[] = '--filter';
            $options[] = $filter;
        }

        if ($coverage) {
            $options[] = '--coverage-text';
        }

        return $runner->run($options, false);
    }
}
