<?php
/**
 * 
 *
 * @author Sam Schmidt
 * @date 2015-04-17
 * @company Linus Shops
 */

namespace LinusShops\Prophet;

class Prophet
{
    protected $modules = array();

    /**
     * Loads a prophet.json, replacing whatever is currently held by this object
     * @param string $prophet parsed JSON
     */
    public function loadConfig($prophet)
    {
        if (isset($prophet['modules'])) {
            $this->modules = $prophet['modules'];
        }
    }

    public function run()
    {

    }

}
