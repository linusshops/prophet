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

    public static function bootstrap()
    {
        if (self::isLoaded()) {
            return;
        }

        //Check if we can find the necessary Magento files
        $files = array(
            'functions' => 'app/code/core/Mage/Core/functions.php',
            'autoload'  => 'lib/Varien/Autoload.php'
        );

        foreach ($files as $file) {
            if (!file_exists($file)) {
                throw new Exceptions\ProphetException(
                    'Failed to load Magento'.$file.' not found'
                );
            }

            require_once($file);
        }

        Varien_Autoload::register();

        $mageFilename = 'app/Mage.php';

        if (!file_exists($mageFilename)) {
            throw new Exceptions\ProphetException(
                'Failed to load Magento'.$file.' not found'
            );
        }

        require_once $mageFilename;

        Mage::init();

        self::$loaded = true;
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
