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

    public function getOption($name)
    {
        if (!isset($this->options[$name])) {
            return null;
        }

        return $this->options[$name];
    }

    public function isIsolated()
    {
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
            $valid = $this->addValidationError(
                $this->getName().
                ': Path ['.$this->getPath().'] is not valid.'
            );
        }

        //Confirm existence of phpunit.xml in path
        if (!file_exists($this->getPath().'/phpunit.xml')) {
            $valid = $this->addValidationError(
                $this->getName().
                ': ['.$this->getPath().'] does not contain a phpunit.xml.'
            );
        }

        return $valid;
    }

    private function addValidationError($message)
    {
        $this->validationErrors[] = $message;
        return false;
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
        file_put_contents($this->getPhpUnitPath(), $this->getPhpunitStandardXml());

        //Create tests directory
        mkdir($this->getPath().'/tests');
    }

    public function getPhpUnitPath()
    {
        return $this->getPath().'/phpunit.xml';
    }

    public function getPhpunitStandardXml()
    {
        return file_get_contents(ConfigRepository::getProphetPath().'/resources/phpunit.xml');
    }
}
