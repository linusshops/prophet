<?php
/**
 *
 *
 * @author Sam Schmidt
 * @date 2015-06-15
 * @company Linus Shops
 */

namespace LinusShops\Prophet;

final class Events
{
    /**
     * This event is thrown before beginning all module tests
     */
    const PROPHET_PREMODULE = 'prophet.premodule';

    /**
     * This event is thrown after completing all module tests
     */
    const PROPHET_POSTMODULE = 'prophet.postmodule';
}
