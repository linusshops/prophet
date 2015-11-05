<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2015-11-05
 * @company Linus Shops
 */

namespace LinusShops\Prophet;


class PluginRepository
{
    private static $plugins = array();

    /**
     *
     * @param $name string Plugin name (the repo name, minus the prophet-plugin-)
     * @return Plugin
     */
    public static function get($name)
    {
        $plugin = null;

        if (isset(self::$plugins[$name])) {
            $plugin = self::$plugins[$name];
        } else {
            
        }
    }
}
