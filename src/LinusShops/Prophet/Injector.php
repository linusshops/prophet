<?php
namespace LinusShops\Prophet;

use LinusShops\Prophet\Events;

/**
 * Facade class for Prophet Helpers
 *
 * @author Sam Schmidt
 * @date 2015-06-18
 * @company Linus Shops
 */
class Injector
{
    private static $paths;

    protected static $loaded = false;

    protected static $magento = array(
        'functions' => '/app/code/core/Mage/Core/functions.php',
        'autoload' => '/lib/Varien/Autoload.php'
    );

    /**
     * @return mixed
     */
    public static function getModulePath()
    {
        return self::$paths['module'];
    }

    public static function setPaths(array $paths)
    {
        self::$paths = $paths;
    }

    public static function listen($event, $callable)
    {
        Events::listen($event, $callable);
    }

    public static function listenPremodule(callable $callable)
    {
        self::listen(Events::PROPHET_PREMODULE, $callable);
    }

    public static function listenPostmodule(callable $callable)
    {
        self::listen(Events::PROPHET_POSTMODULE, $callable);
    }

    public static function dispatch($eventName, &$options = array())
    {
        Events::dispatch($eventName, $options);
    }

    public static function dispatchPremodule()
    {
        self::dispatch(Events::PROPHET_PREMODULE);
    }

    public static function dispatchPostmodule()
    {
        self::dispatch(Events::PROPHET_POSTMODULE);
    }

    public static function bootMagento($path = '.')
    {
        if (!self::isLoaded()) {

            foreach (self::$magento as $file) {
                require_once($path.$file);
            }

            \Varien_Autoload::register();

            require_once $path.'/app/Mage.php';

            $app = \Mage::app('default', 'store', array());
            $app->getConfig()->loadEventObservers('global');
            $app->getConfig()->loadEventObservers('front');

            self::$loaded = true;
        }
    }

    public static function injectAutoloaders($modulePath, $rootPath, $prophetPath)
    {
        //Register a custom autoloader so that controller classes
        //can be loaded for testing.
        $localPool = function ($classname) use ($modulePath, $rootPath) {
            if (strpos($classname, 'Controller') !== false) {
                $parts = explode('_', $classname);
                if (!isset($parts[0]) || !isset($parts[2])) {
                    return;
                }

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

                if (!isset($parts[0]) || !isset($parts[2])) {
                    return;
                }

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

        //Prophet injectable loader
        //Prophet provides some common mock classes, such as requests and responses
        //This loader allows the injector to retrieve them. They can be overriden
        //by the override loader.
        $injectableLoader = function ($classname) use ($prophetPath) {
            if ($classname == 'LinusShops\\Prophet\\Events') {
                require $prophetPath.'/src/LinusShops/Prophet/Events.php';
                return;
            }

            if ($classname == 'LinusShops\\Prophet\\Events\\Options') {
                require $prophetPath.'/src/LinusShops/Prophet/Events/Options.php';
                return;
            }

            $parts = explode('\\', $classname);
            $class = implode('/', $parts);
            if (!in_array('LinusShops', $parts) || !in_array('Injectable', $parts)) {
                return;
            }
            $path = $prophetPath.'/src/'.$class.'.php';
            if (file_exists($path)) {
                require $path;
            }
        };

        //Prophet override loader.
        //Prophet gives itself priority over all other loaders, as this
        //allows the injection of testing specific classes. If there is
        //a class you wish to override in testing, you can do this by adding
        //a file with its exact class name in the tests/overrides/classes directory
        //in the module.
        $overrideLoader = function ($classname) use ($modulePath, $rootPath) {
            $loadpath = $rootPath.'/'.$modulePath.'/tests/overrides/classes/'.$classname.'.php';

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
        spl_autoload_register($injectableLoader, true, true);
        spl_autoload_register($overrideLoader, true, true);

        //Load the ProphetEvents file for the current module test
        $path = $modulePath.'/tests/ProphetEvents.php';
        if (file_exists($path)) {
            include $path;
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
