<?php
/**
 * 
 *
 * @author Sam Schmidt
 * @date 2015-04-22
 * @company Linus Shops
 */

namespace LinusShops\Prophet;


class Module
{
    private $path;
    private $name;

    public function __construct($name, $path)
    {
        $this->name = $name;
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
