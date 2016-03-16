<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-03-16
 */

namespace LinusShops\Prophet;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class FrameworkManager
{
    public static function install(Event $event)
    {
        if (is_dir('./frameworks/behat') && is_dir('./frameworks/phpunit')) {
            shell_exec('cd ./frameworks/behat && composer install && cd -');
            shell_exec('cd ./frameworks/phpunit && composer install && cd -');
        }
    }

    public static function update(Event $event)
    {
        if (is_dir('./frameworks/behat') && is_dir('./frameworks/phpunit')) {
            shell_exec('cd ./frameworks/behat && composer update && cd -');
            shell_exec('cd ./frameworks/phpunit && composer update && cd -');
        }
    }
}
