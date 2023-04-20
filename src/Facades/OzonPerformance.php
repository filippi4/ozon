<?php

namespace Filippi4\Ozon\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static OzonPerformance \Filippi4\Ozon\OzonPerformance config($keys)
 * @method static mixed getCampaign(array $campaign_ids, string $adv_object_type, string $state)
 * @method static mixed getCampaignObjects(int $campaign_id)
 * @method static mixed getStatisticsExpense(int $campaigns, Carbon $dateFrom, Carbon $dateTo)
 * @method static mixed getStatisticsDaily(array $campaignId, Carbon $dateFrom, Carbon $dateTo)
 **/

class OzonPerformance extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ozon_performance';
    }
}
