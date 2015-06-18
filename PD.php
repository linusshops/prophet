<?php

/**
 * Facade class for Prophet Helpers
 *
 * @author Sam Schmidt
 * @date 2015-06-18
 * @company Linus Shops
 */
class PD
{
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
}
