<?php
/**
 * 
 *
 * @author Sam Schmidt
 * @date 2015-04-22
 * @company Linus Shops
 */

namespace LinusShops\Prophet;


class Module
{
    private $path;
    private $name;
    private $options;
    private $validationErrors = array();

    public function __construct($name, $path, $options = array())
    {
        $this->name = $name;
        $this->path = $path;

        $this->options = $options;
    }

    public function getOption($name) {
        if (!isset($this->options[$name])) {
            return null;
        }

        return $this->options[$name];
    }

    public function isIsolated() {
        return $this->getOption('isolate') === true;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Check if the module is valid for testing with prophet
     * @return boolean
     */
    public function validate()
    {
        $valid = true;

        //Confirm path is valid
        if (!is_dir($this->getPath())) {
            $valid = false;
            $this->validationErrors[] = $this->getName().': Path ['.$this->getPath().'] is not valid.';
        }

        //Confirm existence of phpunit.xml in path
        if ($valid && !file_exists($this->getPath().'/phpunit.xml')) {
            $valid = false;

            $this->validationErrors[] = $this->getName().': ['.$this->getPath().'] does not contain a phpunit.xml.';
        }

        return $valid;
    }

    /**
     * @return array
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    public function createTestStructure()
    {
        //Write phpunit.xml
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<phpunit stopOnFailure="false"
         colors="true" syntaxCheck="true" verbose="false"
         processIsolation="false" stderr="true">
    <testsuite name="{$this->getName()} Tests">
        <directory suffix="Test.php">tests</directory>
    </testsuite>

    <filter>
        <!--Indicate locations to check for test coverage-->
        <whitelist>
            <directory suffix=".php">src/app/code</directory>
        </whitelist>
    </filter>
</phpunit>
XML;

        file_put_contents($this->getPath().'/phpunit.xml', $xml);

        //Create tests directory
        mkdir($this->getPath().'/tests');
    }
}
