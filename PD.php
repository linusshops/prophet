<?php
use LinusShops\Prophet\Events;

/**
 * Facade class for Prophet Helpers
 *
 * @author Sam Schmidt
 * @date 2015-06-18
 * @company Linus Shops
 */
class PD
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

    public static function getRequest()
    {
        $classHelper = new \LinusShops\Prophet\Helpers\Classes();
        return $classHelper->getRequest();
    }

    public static function getResponse()
    {
        $classHelper = new \LinusShops\Prophet\Helpers\Classes();
        return $classHelper->getResponse();
    }

    public static function inspect($context = array())
    {
        $dbg = new \LinusShops\Prophet\Helpers\Debug();
        $dbg->start($context);
    }

    public static function listen($event, $callable)
    {
        Events::listen($event, $callable);
    }
}
