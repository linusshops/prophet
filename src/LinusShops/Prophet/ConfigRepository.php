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

    private static $prophetPath;

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

    /**
     * @return mixed
     */
    public static function getProphetPath()
    {
        return self::$prophetPath;
    }

    /**
     * @param mixed $prophetPath
     */
    public static function setProphetPath($prophetPath)
    {
        self::$prophetPath = rtrim($prophetPath, '/');
    }


}
