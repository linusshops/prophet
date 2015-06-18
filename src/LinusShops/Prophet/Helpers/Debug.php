<?php
/**
 *
 *
 * @author Sam Schmidt
 * @date 2015-06-18
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Helpers;


class Debug
{
    public function start()
    {
        eval(\Psy\sh());
    }
}
