<?php
/**
     * Boostraps the containing Magento installation
     *
     * @author Sam Schmidt
     * @date 2015-04-17
     * @company Linus Shops
     */

namespace LinusShops\Prophet;

use LinusShops\Prophet\Events\Options;

class Magento
{
    protected static $loaded = false;

    protected static $magento = array(
        'functions' => '/app/code/core/Mage/Core/functions.php',
        'autoload' => '/lib/Varien/Autoload.php'
    );

    public static function bootstrap($path = '.', Options $options)
    {
        if (!self::isLoaded()) {

            foreach (self::$magento as $file) {
                require_once($path.$file);
            }

            \Varien_Autoload::register();

            require_once $path.'/app/Mage.php';

            $app = \Mage::app('default', 'store', $options->getAll());
            $app->getConfig()->loadEventObservers('global');
            $app->getConfig()->loadEventObservers('front');

            self::$loaded = true;
        }
    }

    /**
     * Are the Magento libraries loaded?
     * @return boolean
     */
    public static function isLoaded()
    {
        return self::$loaded;
    }
}
