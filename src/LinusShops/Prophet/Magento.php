<?php
/**
 * Boostraps the containing Magento installation
 *
 * @author Sam Schmidt
 * @date 2015-04-17
 * @company Linus Shops
 */

namespace LinusShops\Prophet;

class Magento
{
    protected static $loaded = false;

    protected static $magento = array(
        'functions' => 'app/code/core/Mage/Core/functions.php',
        'autoload' => 'lib/Varien/Autoload.php'
    );

    public static function bootstrap()
    {
        if (!self::isLoaded()) {

            foreach (self::$magento as $file) {
                require_once($file);
            }

            \Varien_Autoload::register();

            require_once 'app/Mage.php';

            \Mage::init();

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
