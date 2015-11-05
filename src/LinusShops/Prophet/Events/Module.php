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
    protected $harness;

    public function __construct(\LinusShops\Prophet\Module $module, $harness)
    {
        $this->module = $module;
        $this->harness = $harness;
    }

    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return mixed
     */
    public function getHarness()
    {
        return $this->harness;
    }
}
