<?php

namespace Filippi4\Ozon;

use Carbon\Carbon;
use Exception;
use Illuminate\Validation\ValidationException;

class OzonPerformance extends OzonPerformanceClient
{
    private const DT_FORMAT_DATE_TIME_TZ = 'Y-m-d\TH:i:s.v\Z';
    private const DT_FORMAT_DATE         = 'Y-m-d';

    /**
     * @throws ValidationException
     */
    public function config(array $keys): OzonPerformance
    {
        $this->validateKeys($keys);

        $this->config = $keys;

        return $this;
    }

    private function formatDate(?Carbon $dateTime, string $format = self::DT_FORMAT_DATE_TIME_TZ): ?string
    {
        return $dateTime ? $dateTime->format($format) : null;
    }

    /**
     * Список кампаний
     *
     * @param array|null $campaign_ids
     * @param string|null $adv_object_type
     * @param string|null $state
     * @return mixed
     */
    public function getCampaign(
        array $campaign_ids = null,
        string $adv_object_type = null,
        string $state = null
    ): mixed {
        $params = $this->getNotNullParams(compact('campaign_ids', 'adv_object_type', 'state'));

        return (new OzonData($this->getResponse('api/client/campaign', $params)))->data;
    }

    /**
     * Список товаров в кампании
     *
     * @param  int $campaignId
     * @param  int $page
     * @param  int $pageSize
     * @return mixed
     */
    public function getSearchPromoProducts(
        int $campaignId,
        int $page = 0,
        int $pageSize = 1000
    ): mixed {
        $params = compact('page', 'pageSize');
        return $this->postResponseWithJson('api/client/campaign/' . $campaignId . '/search_promo/products', $params);
    }

    /**
     * Список товаров в продвижении в поиске
     *
     * @param  int $page
     * @param  int $pageSize
     * @return mixed
     */
    public function getSearchPromoProductsV2(
        int $page = 0,
        int $pageSize = 1000
    ): mixed {
        $params = compact('page', 'pageSize');
        return $this->postResponseWithJson('api/client/campaign/search_promo/v2/products', $params);
    }

    /**
     * Статистика по товарной кампании
     *
     * @param  string $dateFrom
     * @param  string $dateTo
     * @return mixed
     */
    public function getProductCampaignStatistics(
        string $dateFrom,
        string $dateTo
    ): mixed {
        $params = compact('dateFrom', 'dateTo');
        return (new OzonData($this->getResponse('api/client/statistics/campaign/product/json', $params)))->data;
    }
    /**
     * Список рекламируемых объектов в кампании
     *
     * @param int $campaign_id Идентификатор кампании
     * @return mixed
     */

    /**
     * Список товаров в кампании
     *
     * @param  string $from
     * @param  string $to
     * @return mixed
     */
    public function getSearchPromoProductsReports(
        string $from,
        string $to
    ): mixed {
        $params = compact('from', 'to');
        return $this->postResponseWithJson('api/client/statistic/products/generate/json', $params);
    }

    public function getCampaignObjects(int $campaign_id): mixed
    {
        return (
            new OzonData(
                $this->getResponse(
                    'api/client/campaign/' . $campaign_id . '/objects'
                )
            )
        )->data;
    }

    /**
     * Статистика по расходу кампаний
     *
     * @param int|null $campaigns Идентификаторы кампаний
     * @param Carbon|null $dateFrom
     * @param Carbon|null $dateTo
     * @return mixed
     */
    public function getStatisticsExpense(
        int $campaigns = null,
        Carbon $dateFrom = null,
        Carbon $dateTo = null
    ): mixed {
        $dateFrom = $this->formatDate($dateFrom, self::DT_FORMAT_DATE);
        $dateTo   = $this->formatDate($dateTo, self::DT_FORMAT_DATE);

        $params = $this->getNotNullParams(compact('campaigns', 'dateFrom', 'dateTo'));

        return (new OzonData($this->getResponse('api/client/statistics/expense', $params, false)))->data;
    }

    /**
     * Дневная статистика по кампаниям
     *
     * @param array|null $campaignIds Список идентификаторов кампаний
     * @param Carbon|null $dateFrom Начальная дата периода отчёта
     * @param Carbon|null $dateTo Конечная дата периода отчёта
     * @return mixed
     */
    public function getStatisticsDaily(
        array $campaignIds = null,
        Carbon $dateFrom = null,
        Carbon $dateTo = null,
    ): mixed {
        $dateFrom = $this->formatDate($dateFrom, self::DT_FORMAT_DATE);
        $dateTo   = $this->formatDate($dateTo, self::DT_FORMAT_DATE);

        $params = $this->getNotNullParams(compact('campaignIds', 'dateFrom', 'dateTo'));

        return (new OzonData($this->getResponse('api/client/statistics/daily', $params, false)))->data;
    }

    /**
     * Cтатистика по кампаниям
     *
     * @param array|null $campaigns Список идентификаторов кампаний
     * @param Carbon|null $dateFrom Начальная дата периода отчёта
     * @param Carbon|null $dateTo Конечная дата периода отчёта
     * @return mixed
     */
    public function getStatistics(
        array $campaigns = null,
        Carbon $dateFrom = null,
        Carbon $dateTo = null,
        string $groupBy = "NO_GROUP_BY"
    ): mixed {
        $dateFrom = $this->formatDate($dateFrom, self::DT_FORMAT_DATE);
        $dateTo   = $this->formatDate($dateTo, self::DT_FORMAT_DATE);

        $params = $this->getNotNullParams(compact('campaigns', 'dateFrom', 'dateTo', 'groupBy'));

        return (new OzonData($this->postResponse('api/client/statistics', $params, false)))->data;
    }

    /**
     * Cтатистика по внешнему трафику
     *
     * @param Carbon|null $dateFrom Начальная дата периода отчёта
     * @param Carbon|null $dateTo Конечная дата периода отчёта
     * @param string|null $type Тип отчёта
     * @return mixed
     */
    public function getVendorsStatistics(
        Carbon $dateFrom = null,
        Carbon $dateTo = null,
        string $type
    ): mixed {
        $dateFrom = $this->formatDate($dateFrom, self::DT_FORMAT_DATE);
        $dateTo   = $this->formatDate($dateTo, self::DT_FORMAT_DATE);

        $params = $this->getNotNullParams(compact('dateFrom', 'dateTo', 'type'));

        return (new OzonData($this->postResponse('api/client/vendors/statistics', $params)))->data;
    }

    /**
     * Cтатистика по кампаниям в формате json
     *
     * @param array|null $campaigns Список идентификаторов кампаний
     * @param Carbon|null $dateFrom Начальная дата периода отчёта
     * @param Carbon|null $dateTo Конечная дата периода отчёта
     * @return mixed
     */
    public function getStatisticsJson(
        array $campaigns = null,
        Carbon $dateFrom = null,
        Carbon $dateTo = null,
        string $groupBy = "NO_GROUP_BY"
    ): mixed {
        $dateFrom = $this->formatDate($dateFrom, self::DT_FORMAT_DATE);
        $dateTo   = $this->formatDate($dateTo, self::DT_FORMAT_DATE);

        $params = $this->getNotNullParams(compact('campaigns', 'dateFrom', 'dateTo', 'groupBy'));

        return (new OzonData($this->postResponse('api/client/statistics/json', $params, false)))->data;
    }

    /**
     * Статус отчета
     *
     * @param string $report
     * @return mixed
     */
    public function getReportStatus(
        string $report
    ): mixed {
        return (new OzonData($this->getResponse('api/client/statistics/' . $report)))->data;
    }

    /**
     * Статус отчета
     *
     * @param string $report
     * @return mixed
     */
    public function getVendorsReportStatus(
        string $report
    ): mixed {
        return (new OzonData($this->getResponse('api/client/vendors/statistics/' . $report, ['vendor' => true], true)))->data;
    }

    /**
     * Статус отчета
     *
     * @param string $url
     * @param int $quantityOfCampaigns
     * @return mixed
     */
    public function getReport(
        string $url,
        int $quantityOfCampaigns
    ): mixed {
        return $this->getFile($url, $quantityOfCampaigns);
    }

    /**
     * Получить файл отчета по внешнему трафику
     *
     * @param string $url
     * @param int $quantityOfCampaigns
     * @return mixed
     */
    public function getVendorsReport(
        string $url
    ): mixed {
        return $this->getXlsxFile($url);
    }

    /**
     * Статус отчета
     *
     * @param string $url
     * @param int $quantityOfCampaigns
     * @return mixed
     */
    public function getJsonReport(
        string $url
    ): mixed {
        return (new OzonData($this->getResponse($url)))->data;
    }

    /**
     * @throws Exception
     */
    public function getPromoOrders(Carbon $From, Carbon $To): array
    {
        $from = $this->formatDate($From, self::DT_FORMAT_DATE_TIME_TZ);
        $to   = $this->formatDate($To, self::DT_FORMAT_DATE_TIME_TZ);

        $params = $this->getNotNullParams(compact('from', 'to'));
        return $this->postResponseWithJson('api/client/statistics/orders/generate/json', $params);
    }

    public function getPromoOrdersReport(string $url, string $UUID)
    {
        $params = $this->getNotNullParams(compact('UUID'));

        return (new OzonData($this->getResponse('api/client/statistics/report', $params)))->data;
    }

    /**
     * Метод для получения отчёта по заказам на баннеры в формате JSON.
     *
     * @param array<string> $campaigns
     * @param string|null $from
     * @param string|null $to
     * @param Carbon|null $dateFrom
     * @param Carbon|null $dateTo
     * @param string $groupBy ["NO_GROUP_BY", "DATE", "START_OF_WEEK", "START_OF_MONTH"]
     * @return mixed
     */
    public function getStatisticsAttributionJson(
        array $campaigns,
        string $from = null,
        string $to = null,
        Carbon $dateFrom = null,
        Carbon $dateTo = null,
        string $groupBy = "NO_GROUP_BY"
    ): mixed {
        $dateFrom = $this->formatDate($dateFrom, self::DT_FORMAT_DATE);
        $dateTo   = $this->formatDate($dateTo, self::DT_FORMAT_DATE);

        $params = $this->getNotNullParams(compact('campaigns', 'from', 'to', 'dateFrom', 'dateTo', 'groupBy'));

        return (new OzonData($this->postResponse('api/client/statistics/attribution/json', $params)))->data;
    }

    private function getNotNullParams(array $params): array
    {
        $notNullParams = [];
        foreach ($params as $key => $value) {
            if (! empty($value)) {
                $notNullParams[$key] = $value;
            }
        }
        return $notNullParams;
    }
}
