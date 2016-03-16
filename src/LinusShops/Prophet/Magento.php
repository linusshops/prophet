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

    public static function bootstrap(Options $options, $path = '.')
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

    public static function injectAutoloaders($modulePath, $rootPath)
    {
        //Register a custom autoloader so that controller classes
        //can be loaded for testing.
        $localPool = function ($classname) use ($modulePath, $rootPath) {
            if (strpos($classname, 'Controller') !== false) {
                $parts = explode('_', $classname);

                $loadpath = $rootPath.'/'.$modulePath.'/src/app/code/local/'
                    . $parts[0].'/'.$parts[1]
                    . '/controllers';
                for ($i = 2; $i<count($parts); $i++) {
                    $loadpath .= '/'.$parts[$i];
                }

                $loadpath .= '.php';

                if (file_exists($loadpath)) {
                    include $loadpath;
                }
            }
        };

        $communityPool = function ($classname) use ($modulePath, $rootPath) {
            if (strpos($classname, 'Controller') !== false) {
                $parts = explode('_', $classname);

                $loadpath = $rootPath.'/'.$modulePath.'/src/app/code/community/'
                    . $parts[0].'/'.$parts[1]
                    . '/controllers';
                for ($i = 2; $i<count($parts); $i++) {
                    $loadpath .= '/'.$parts[$i];
                }

                $loadpath .= '.php';

                if (file_exists($loadpath)) {
                    include $loadpath;
                }
            }
        };

        //Prophet override loader.
        //Prophet gives itself priority over all other loaders, as this
        //allows the injection of testing specific classes. If there is
        //a class you wish to override in testing, you can do this by adding
        //a file with its exact class name in the tests/phpunit/classes directory
        //in the module.
        $overrideLoader = function ($classname) use ($modulePath, $rootPath) {
            $loadpath = $rootPath.'/'.$modulePath.'/tests/phpunit/classes/'.$classname.'.php';

            if (file_exists($loadpath)) {
                include $loadpath;
            }
        };

        //This autoloader is prepended, as the Varien autoloader
        //will cause everything to die if it can't find the class. Also,
        //this will give us a hook in the future if Prophet ever
        //needs to intercept class loading.
        spl_autoload_register($communityPool, true, true);
        spl_autoload_register($localPool, true, true);
        spl_autoload_register($overrideLoader, true, true);
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
