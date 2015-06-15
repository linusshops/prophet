<?php
/**
 *
 *
 * @author Sam Schmidt
 * @date 2015-06-15
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Events;

use Symfony\Component\EventDispatcher\Event;

class Module extends Event
{
    protected $module;

    public function __construct(\LinusShops\Prophet\Module $module)
    {
        $this->module = $module;
    }

    public function getModule()
    {
        return $this->module;
    }
}