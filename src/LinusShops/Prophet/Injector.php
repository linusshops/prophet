<?php
namespace LinusShops\Prophet;

use LinusShops\Prophet\Events;

/**
 * Facade class for Prophet Helpers
 *
 * @author Sam Schmidt
 * @date 2015-06-18
 * @company Linus Shops
 */
class Injector
{
    private static $currentModulePath;

    /**
     * @return mixed
     */
    public static function getCurrentModulePath()
    {
        return self::$currentModulePath;
    }

    /**
     * @param mixed $currentModulePath
     */
    public static function setCurrentModulePath($currentModulePath)
    {
        self::$currentModulePath = $currentModulePath;
    }

    public static function listen($event, $callable)
    {
        Events::listen($event, $callable);
    }
}
