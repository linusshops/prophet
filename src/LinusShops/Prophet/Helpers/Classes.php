<?php
/**
 * Provides access to special Prophet classes for use in tests
 *
 * @author Sam Schmidt
 * @date 2015-06-15
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Helpers;

use LinusShops\Prophet\Overrides\Request;
use LinusShops\Prophet\Overrides\Response;

class Classes
{
    public static function getRequest()
    {
        return new Request();
    }

    public static function getResponse()
    {
        return new Response();
    }
}
