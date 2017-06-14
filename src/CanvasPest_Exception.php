<?php

/** smtech\CanvasPest\CanvasPest_Exception */

namespace smtech\CanvasPest;

/**
 * All exceptions thrown by the CanvasPest object
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class /* @codingStandardsIgnoreStart */ CanvasPest_Exception /* @codingStandardsIgnoreEnd */ extends \Exception
{
    /** The API access method is not supported by the Canvas API */
    const UNSUPPORTED_METHOD = 1;

    /** The API access token provided is invalid */
    const INVALID_TOKEN = 2;

    /** Unanticipated JSON response from API */
    const INVALID_JSON_RESPONSE = 3;
}
