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

                self::$modules[] = $module;
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
        $prophet = file_get_contents('prophet.json');
        $prophet = json_decode($prophet, true);

        $prophet['modules'][] = array(
            'name'=>$module->getName(),
            'path'=>$module->getPath()
        );

        file_put_contents('prophet.json',json_encode($prophet));
    }
}
