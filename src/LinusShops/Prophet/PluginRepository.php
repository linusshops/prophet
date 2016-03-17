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

    public static function load()
    {
        $base = PROPHET_ROOT_DIR.'/plugins';

        foreach (scandir($base) as $name) {
            $path = $base.'/'.$name.'/plugin.php';
            if (file_exists($path)) {
                /** @var Plugin $plugin */
                $plugin = require($path);
                $plugin->load();
                $plugin->register();
                self::$plugins[strtolower($name)] = $plugin;
            }
        }
    }

    public static function get($name)
    {
        return self::$plugins[strtolower($name)];
    }

    public static function has($name)
    {
        return isset(self::$plugins[strtolower($name)]);
    }
}
