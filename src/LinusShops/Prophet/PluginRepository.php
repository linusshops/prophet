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
        $base = dirname(ConfigRepository::getProphetPath()).'/plugins';

        foreach (scandir($base) as $pluginDir) {
            if (strpos($pluginDir, 'prophet-plugin-') !== false) {
                $path = $base.'/'.$pluginDir.'/plugin.php';
                if (file_exists($path)) {
                    $name = str_replace('prophet-plugin-', '', $pluginDir);
                    /** @var Plugin $plugin */
                    $plugin = require($path);
                    $plugin->load();
                    $plugin->register();
                    self::$plugins[$name] = $plugin;
                }
            }
        }
    }

    public function get($name) {
        return self::$plugins[$name];
    }
}
