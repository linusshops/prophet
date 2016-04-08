<?php
/**
     * 
     *
     * @author Sam Schmidt
     * @date 2015-04-22
     * @company Linus Shops
     */

namespace LinusShops\Prophet;

use LinusShops\Prophet\Exceptions\InvalidConfigException;
use Symfony\Component\Yaml\Parser;

class Config
{
    private $modules;

    public function __construct($parsedConfigFile, $prefix = '')
    {
        if (!is_array($parsedConfigFile)) {
            throw new InvalidConfigException('Invalid config: parsed config must be an array.');
        }

        if (isset($parsedConfigFile['modules'])) {
            $this->loadModules($parsedConfigFile['modules']);
        }
    }

    /**
     * @param string $path
     * @return Config
     * @throws InvalidConfigException
     */
    public static function getConfigFromFile($path = '.')
    {
        $path .= '/prophet.yml';

        if (!is_file($path)) {
            throw new InvalidConfigException("prophet.yml not found in path {$path}");
        }
        $yaml = new Parser();

        return new Config($yaml->parse(file_get_contents($path)));
    }

    private function loadModules($modules)
    {
        foreach ($modules as $definition) {
            $module = new Module(
                $definition['name'],
                $definition['path']
            );

            $this->modules[$module->getName()] = $module;
        }
    }

    public function getModules()
    {
        return $this->modules;
    }

    public function hasModules()
    {
        return count($this->modules) > 0;
    }
}
