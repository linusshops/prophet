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
    public function validate($pathPrefix = '')
    {
        $valid = true;

        $path = empty($pathPrefix) ? $this->getPath() : $pathPrefix.'/'.$this->getPath();

        //Confirm path is valid
        if (!is_dir($path)) {
            $valid = $this->addValidationError(
                $this->getName().
                ': Path ['.$path.'] is not valid.'
            );
        }

        //Confirm existence of phpunit.xml in path
        if (!file_exists($path.'/phpunit.xml')) {
            $valid = $this->addValidationError(
                $this->getName().
                ': ['.$path.'] does not contain a phpunit.xml.'
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

    public function createTestStructure($pathPrefix = '.')
    {
        file_put_contents($this->getPhpUnitPath($pathPrefix), $this->getPhpunitStandardXml());
        file_put_contents($this->getBehatPath($pathPrefix), $this->getBehatStandardConfig());

        //Create tests directory
        if (!file_exists($pathPrefix.'/'.$this->getPath())) {
            mkdir($pathPrefix.'/'.$this->getPath().'/tests');
            mkdir($pathPrefix.'/'.$this->getPath().'/tests/phpunit');
            mkdir($pathPrefix.'/'.$this->getPath().'/tests/behat');
        }

    }

    public function getAvailableTestFrameworks()
    {
        $frameworks = array();

        if (is_file($this->getPhpUnitPath())) {
            $frameworks[] = 'phpunit';
        }

        if (is_file($this->getBehatPath())) {
            $frameworks[] = 'behat';
        }

        return $frameworks;
    }

    public function getPhpUnitPath($pathPrefix = '.')
    {
        return $pathPrefix.'/'.$this->getPath().'/phpunit.xml';
    }

    public function getBehatPath($pathPrefix = '.')
    {
        return $pathPrefix.'/'.$this->getPath().'/behat.yml';
    }

    public function getPhpunitStandardXml()
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>

<phpunit colors="true">
    <testsuites>
        <testsuite name="Prophet Test Suite">
            <directory suffix="Test.php">./tests/</directory>
        </testsuite>
    </testsuites>

    <filter>
        <whitelist>
            <directory>./src</directory>
        </whitelist>
    </filter>
</phpunit>

XML;

    }

    public function getBehatStandardConfig()
    {
        return <<<YML
# behat.yml

default:
    autoload:
        '': %paths.base%/tests/behat/contexts
    suites:
        default:
          paths: [ %paths.base%/tests/behat ]
          contexts:
            - FeatureContext
            - Behat\MinkExtension\Context\MinkContext
    extensions:
        Behat\MinkExtension:
            base_url: https://develop.vagrant.dev
            sessions:
                headless:
                    selenium2:
                      wd_host: "http://localhost:8643/wd/hub"
                full:
                    selenium2: ~

YML;

    }
}
