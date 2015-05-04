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
use LinusShops\Prophet\Exceptions\ProphetException;

class Config
{
    private static $modules;
    private static $prophetFilePath;

    /**
     * Loads a prophet.json, replacing whatever is currently held by this object
     * @param string $prophet parsed JSON
     */
    public static function loadConfig($prophet)
    {
        if (!is_array($prophet)) {
            throw new InvalidConfigException('Config::loadConfig expects an array.');
        }

        if (isset($prophet['modules'])) {
            foreach ($prophet['modules'] as $definition) {
                $module = new Module(
                    $definition['name'],
                    $definition['path'],
                    isset($definition['options']) ? $definition['options'] : array()
                );

                self::$modules[$module->getName()] = $module;
            }
        }
    }

    public static function getModule($name)
    {
        return self::$modules[$name];
    }

    public static function getModuleList()
    {
        return self::$modules;
    }

    public static function writeModule(Module $module) {
        self::$modules['modules'][] = array(
            'name'=>$module->getName(),
            'path'=>$module->getPath()
        );

        file_put_contents(
            self::getProphetFilePath(),
            json_encode(self::$modules)
        );
    }

    /**
     * @return mixed
     */
    public static function getProphetFilePath()
    {
        return self::$prophetFilePath==null ?
            'prophet.json'
            : self::$prophetFilePath;
    }

    /**
     * @param mixed $prophetFilePath
     */
    public static function setProphetFilePath($prophetFilePath)
    {
        self::$prophetFilePath = $prophetFilePath;
    }
}
