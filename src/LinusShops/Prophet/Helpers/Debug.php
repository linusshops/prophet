<?php
/**
     *
     *
     * @author Sam Schmidt
     * @date 2015-06-18
     * @company Linus Shops
     */

namespace LinusShops\Prophet\Helpers;


use Psy\Shell;

class Debug
{
    public function start($context = array())
    {
        if (!is_array($context)) {
            $context = array(
                'var' => $context
            );
        }
        Shell::debug($context);
    }
}
