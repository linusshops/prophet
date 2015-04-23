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
    private $validationErrors = array();

    public function __construct($name, $path)
    {
        $this->name = $name;
        $this->path = $path;
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
}
