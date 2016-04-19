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

    public function __construct($parsedConfigFile, $pathPrefix = '')
    {
        if (!is_array($parsedConfigFile)) {
            throw new InvalidConfigException('Invalid config: parsed config must be an array.');
        }

        if (isset($parsedConfigFile['modules'])) {
            $this->loadModules($parsedConfigFile['modules'], $pathPrefix);
        }
    }

    /**
     * @param string $path
     * @return Config
     * @throws InvalidConfigException
     */
    public static function getConfigFromFile($path = '.')
    {
        $prefix = $path;
        $path .= '/prophet.yml';

        if (!is_file($path)) {
            throw new InvalidConfigException("prophet.yml not found in path {$path}");
        }
        $yaml = new Parser();

        return new Config($yaml->parse(file_get_contents($path)), $prefix);
    }

    private function loadModules($modules, $pathPrefix)
    {
        foreach ($modules as $definition) {
            $module = new Module(
                $definition['name'],
                $pathPrefix.'/'.$definition['path']
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
