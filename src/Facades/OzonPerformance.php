<?php

namespace KFilippovk\Ozon\Facades;

use DateTime;

/**
 * Custom config
 * @method static \KFilippovk\Ozon\OzonPerformanceClient config($keys)
 **/

class OzonPerformance extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ozon_performance';
    }
}
