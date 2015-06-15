<?php
/**
 *
 *
 * @author Sam Schmidt
 * @date 2015-06-15
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Overrides;

class Response extends Mage_Core_Controller_Response_Http
{
    /**
     * When we're testing with this class, we don't care about actually sending
     * headers, since we'll be inspecting the class directly.
     *
     * @param bool|false $throw
     * @return bool
     */
    public function canSendHeaders($throw = false)
    {
        return true;
    }
}
