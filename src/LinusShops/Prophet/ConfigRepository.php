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

    public static function getModuleNames()
    {
        $names = array();

        /** @var Module $module */
        foreach(self::$config->getModuleList() as $module) {
            $names[] = $module->getName();
        }

        return $names;
    }

    public static function getModules()
    {
        return self::$config->getModuleList();
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

    public static function loadConfig($path, $prefix = '')
    {
        $loaded = true;
        $path = $path.'/prophet.json';
        $json = json_decode(file_get_contents($path), true);
        if ($json === false) {
            $loaded = false;
        } else {
            self::setConfig(new Config($json, $prefix));
        }

        return $loaded;
    }

    public static function getPluginDirectory()
    {
        return dirname(ConfigRepository::getProphetPath()).'/plugins';
    }
}
