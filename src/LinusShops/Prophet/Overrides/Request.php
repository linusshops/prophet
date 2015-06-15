<?php
/**
 *
 *
 * @author Sam Schmidt
 * @date 2015-06-15
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Overrides;

class Request extends Mage_Core_Controller_Request_Http
{
    private $requestMethod = null;

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->requestMethod;
    }

    /**
     * @param mixed $requestMethod
     */
    public function setMethod($requestMethod)
    {
        $this->requestMethod = $requestMethod;
    }
}
