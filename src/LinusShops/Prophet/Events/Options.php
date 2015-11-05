<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2015-11-05
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Events;


class Options
{
    private $options=array();

    public function __construct(array $options=array())
    {
        $this->options = $options;
    }

    public function set($name, $value)
    {
        $this->options[$name] = $value;
    }

    public function get($name)
    {
        return $this->options[$name];
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->options;
    }
}
