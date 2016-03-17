<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2015-11-06
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Commands;


use LinusShops\Prophet\ConfigRepository;
use LinusShops\Prophet\Command;

class Plugin extends Command
{
    /**
     * Copy the config example file to config.json if it exists and config.json
     * doesn't already exist.
     */
    protected function copyConfig($name)
    {
        $path = ConfigRepository::getPluginDirectory()."/$name";
        if (file_exists($path.'/config.example.json')
            && !file_exists($path.'/config.json')
        ) {
            copy(
                $path.'/config.example.json',
                $path.'/config.json'
            );
        }
    }
}
