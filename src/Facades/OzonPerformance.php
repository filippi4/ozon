<?php

namespace KFilippovk\Ozon\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static OzonPerformance \KFilippovk\Ozon\OzonPerformance config($keys)
 * @method static void getCampaign(array $campaign_ids, string $adv_object_type, string $state)
 **/

class OzonPerformance extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ozon_performance';
    }
}
