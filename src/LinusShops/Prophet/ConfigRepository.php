<?php
/**
 * 
 *
 * @author Sam Schmidt
 * @date 2015-05-04
 * @company Linus Shops
 */

namespace LinusShops\Prophet;


class ConfigRepository {
    private static $config;

    /**
     * @return Config
     */
    public static function getConfig()
    {
        return self::$config;
    }

    /**
     * @param Config $config
     */
    public static function setConfig($config)
    {
        self::$config = $config;
    }


}
