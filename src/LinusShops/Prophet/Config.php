<?php
/**
 * 
 *
 * @author Sam Schmidt
 * @date 2015-04-22
 * @company Linus Shops
 */

namespace LinusShops\Prophet;


class Config
{
    private static $modules;

    /**
     * Loads a prophet.json, replacing whatever is currently held by this object
     * @param string $prophet parsed JSON
     */
    public static function loadConfig($prophet)
    {
        if (isset($prophet['modules'])) {
            foreach ($prophet['modules'] as $definition) {
                $module = new Module(
                    $definition['name'],
                    $definition['path']
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
}
