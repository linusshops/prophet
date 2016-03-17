<?php
/**
     *
     *
     * @author Sam Schmidt
     * @date 2015-06-15
     * @company Linus Shops
     */

namespace LinusShops\Prophet;

class Events
{
    /**
     * This event is thrown before beginning all module tests
     */
    const PROPHET_PREMODULE = 'prophet.premodule';

    /**
     * This event is thrown after completing all module tests
     */
    const PROPHET_POSTMODULE = 'prophet.postmodule';

    /**
     * Thrown before Magento is bootstrapped
     */
    const PROPHET_PREMAGENTO = 'prophet.premagento';

    /**
     * Thrown after Magento bootstrap
     */
    const PROPHET_POSTMAGENTO = 'prophet.postmagento';

    protected static $events = array();

    public static function listen($eventName, $callable)
    {
        self::$events[$eventName][] = $callable;
    }

    public static function dispatch($eventName, &$options = array())
    {
        if (isset(self::$events[$eventName])) {
            foreach (self::$events[$eventName] as $event) {
                $event($options);
            }
        }
    }
}
