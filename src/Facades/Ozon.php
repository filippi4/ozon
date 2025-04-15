<?php

namespace Filippi4\Ozon\Facades;

use DateTime;

/**
 * Custom config
 * @method static Ozon \Filippi4\Ozon\Ozon config($keys)
 * 
 * Атрибуты и характеристики Ozon
 * @method static mixed getCategoryTree(int $category_id = null, string $language = 'DEFAULT');
 * @method static mixed getCategoryAttribute(array $category_id, string $attribute_type = 'ALL', string $language = 'DEFAULT');
 * @method static mixed getCategoryAttributeValues(int $attribute_id, int $category_id, int $last_value_id = null, int $limit = 5000, string $language = 'DEFAULT');

 * Загрузка и обновление товаров
 * @method static mixed getProductList(array $offer_id = null, array $product_id = null, string $visibility = 'ALL', string $last_id = null, int $limit = 1000);
 * @method static mixed getProductInfo(string $offer_id = null, int $product_id = null, int $sku = null);
 * @method static mixed getProductInfoList(array $offer_id = null, array $product_id = null, array $sku = null);
 * @method static mixed getProductInfoDescription(string $offer_id = null, int $product_id = null);
 * @method static mixed getProductRatingBySku(array $skus));
 * @method static mixed getProductsInfoAttributes(
 *     array $offer_id = null,
 *     array $product_id = null,
 *     string $visibility = 'ALL',
 *     string $last_id = null,
 *     int $limit = 1000,
 *     string $sort_by = null,
 *     string $sort_dir = 'ASC'
 * );
 * @method static mixed getProductsGeoRestrictionsCatalogByFilter(array $names = null, bool $only_visible = true, int $last_order_number = null, int $limit = null));

 * Цены и остатки товаров
 * @method static mixed getProductInfoStocks(array $offer_id = null, array $product_id = null, string $visibility = 'ALL', string $last_id = null, int $limit = 1000));
 * @method static mixed getProductInfoStocksByWarehouseFbs(array $fbs_sku));
 * @method static mixed getProductInfoPrices(array $offer_id = null, array $product_id = null, string $visibility = 'ALL', string $last_id = null, int $limit = 1000));
 * @method static mixed getProductInfoDiscounted(array $discounted_skus));

 * Акции
 * @method static mixed getActions());
 * @method static mixed getActionsCandidates(float $action_id, float $offset = null, ?float $limit = 100));
 * @method static mixed getActionsProducts(float $action_id, string $last_id = "", ?float $limit = 100));
 * @method static mixed getActionsHotSalesList());
 * @method static mixed getActionsHotSalesProducts(float $hotsale_id, float $offset = null, float $limit = 100));

 * Сертификаты брендов
 * @method static mixed getProductCertificateAccordanceTypes());
 * @method static mixed getProductCertificateTypes());
 * @method static mixed getProductCertificationList(int $page = 1, int $page_size = 1000));
 * @method static mixed getBrandCompanyCartificationList(int $page = 1, int $page_size = 1000));
 * @method static mixed getBrandCompanyCartificationList());

 * Склады
 * @method static mixed getWarehouseList());
 * @method static mixed getDeliveryMethodList(int $warehouse_id = null, int $provider_id = null, string $status = null, int $offset = null, int $limit = 50));

 * Схема FBO
 * @method static mixed getPostingFboList(
 *     DateTime $since = null,
 *     DateTime $to = null,
 *     string $status = null,
 *     bool $translit = null,
 *     bool $analytics_data = null,
 *     bool $financial_data = null,
 *     string $dir = 'ASC',
 *     int $offset = null,
 *     int $limit = 1000
 * ));
 * @method static mixed getPostingFboGet(string $posting_number, bool $translit = null, bool $analytics_data = null, bool $financial_data = null));

 * Схемы FBS и rFBS
 * @method static mixed getPostingFbsUnfulfilledList(
 *     DateTime $cutoff_from = null,
 *     DateTime $cutoff_to = null,
 *     DateTime $delivering_date_from = null,
 *     DateTime $delivering_date_to = null,
 *     array $provider_id = null,
 *     string $status = null,
 *     array $warehouse_id = null,
 *     bool $analytics_data = null,
 *     bool $barcodes = null,
 *     bool $financial_data = null,
 *     bool $translit = null,
 *     string $dir = 'ASC',
 *     int $offset = null,
 *     int $limit = 1000
 * );
 * @method static mixed getPostingFbsList(
 *     DateTime $since,
 *     DateTime $to,
 *     string $status = null,
 *     array $delivery_method_id = null,
 *     int $order_id = null,
 *     array $provider_id = null,
 *     array $warehouse_id = null,
 *     bool $analytics_data = null,
 *     bool $barcodes = null,
 *     bool $financial_data = null,
 *     bool $translit = null,
 *     string $dir = 'ASC',
 *     int $offset = null,
 *     int $limit = 1000
 * );
 * @method static mixed getPostingFbsGet(
 *     string $posting_number,
 *     bool $analytics_data = null,
 *     bool $barcodes = null,
 *     bool $financial_data = null,
 *     bool $product_exemplars = null,
 *     bool $related_postings = null,
 *     bool $translit = null
 * );
 * @method static mixed getPostingFbsGetByBarcode(string $barcode);
 * @method static mixed getPostingFbsProductCountryList(string $name_search = null);
 * @method static mixed getPostingFbsRestrictions(string $posting_number);
 * @method static mixed getPostingFbsCancelReasonList());
 * @method static mixed getPostingFbsCancelReason(array $related_posting_numbers);
 * @method static mixed getPostingFbsActList(DateTime $date_from, DateTime $date_to, string $integration_type = null, array $status = null, int $limit = 50);

 * Возвраты товаров
 * @method static mixed getReturnsCompanyFboV3(string $posting_number = null, array $status = null, int $last_id = null, int $limit = 1000);
 * @method static mixed getReturnsCompanyFbo(string $posting_number = null, array $status = null, int $offset = null, int $limit = 1000);
 * @method static mixed getReturnsCompanyFbs(
 *     DateTime $accepted_from_customer_moment_time_from = null,
 *     DateTime $accepted_from_customer_moment_time_to = null,
 *     DateTime $last_free_waiting_day_time_from = null,
 *     DateTime $last_free_waiting_day_time_to = null,
 *     int $order_id = null,
 *     array $posting_number = null,
 *     string $product_name = null,
 *     string $product_offer_id = null,
 *     string $status = null,
 *     int $offset = null,
 *     int $limit = 1000
 * );

 * Отмены заказов
 * @method static mixed getConditionalCancellationList(
 *     array $cancellation_initiator = null,
 *     array $posting_number = null,
 *     array $state = null,
 *     bool $counters = null,
 *     int $offset = null,
 *     int $limit = 1000
 * );

 * Чаты с покупателями
 * @method static mixed getChatListV2(string $chat_status = 'All', bool $unread_only = false, int $offset = null, int $limit = 30);
 * @method static mixed getChatList(array $chat_id_list = null, bool $irst_unead_message_id = null, bool $unread_count = null, int $page = null, int $page_size = 100);
 * @method static mixed getChatHistoryV2(string $chat_id, string $direction = 'Backward', int $from_message_id = null, int $limit = 50);
 * @method static mixed getChatHistory(string $chat_id, string $from_message_id = null, int $limit = 100));

 * Накладные
 * @method static mixed getSupplierOrdersWaybillAcceptanceResults(string $orderId);
 * @method static mixed getSupplierWaybillAcceptanceResults(string $waybillId));

 * Отчеты
 * @method static mixed getReportList(int $page = null, int $page_size = 100, string $report_type = 'ALL'));
 * @method static mixed getReportInfo(string $code);
 * @method static mixed getFinanceCashFlowStatementList(DateTime $from, DateTime $to, int $page = 1, int $page_size = 1000);
 * @method static mixed getReportDiscountedList());
 * @method static mixed getReportDiscountedInfo(string $code);

 * Аналитические отчеты
 * @method static mixed getAnalyticsData(
 *     DateTime $date_from,
 *     DateTime $date_to,
 *     array $metrics,
 *     array $dimension,
 *     array $filters = null,
 *     array $sort = null,
 *     int $offset = null,
 *     int $limit = 1000
 * );
 * @method static mixed getAnalyticsStockOnWarehouses(int $offset = null, int $limit = 100);
 * @method static mixed getAnalyticsStockOnWarehousesV2(int $offset = null, int $limit = 100, string $warehouse_type = 'ALL');
 * @method static mixed getAnalyticsItemTurnover(DateTime $date_from);

 * Финансовые отчёты
 * @method static mixed getFinanceRealization(DateTime $date);
 * @method static mixed getFinanceTransactionList(DateTime $from = null,
 *     DateTime $to,
 *     array $operation_type = null,
 *     string $posting_number = null,
 *     string $transaction_type = null,
 *     int $page = 1,
 *     int $page_size = 1000
 * );
 * @method static mixed getFinanceTransactionTotals(DateTime $from = null, DateTime $to, string $posting_number = null, string $transaction_type = null);

 * Автомобили
 * @method static mixed getAutoBookingsList(string $booking_id_gt_or_eq, string $per_page, string $created_at_gt_or_eq = null, string $created_at_lt_or_eq = null);
 * @method static mixed getAutoBookingsGet(int $booking_id));
 * @method static mixed getAutoCbosList(int $page = 1, int $per_page = 100);
 * @method static mixed getAutoModificationsList(int $modification_id_gt_or_eq = 1, int $per_page = 500);
 * @method static mixed getAutoOffersList(array $offer_ids = null, int $last_id = null, int $limit = 1000);

 * Рейтинг продавца
 * @method static mixed getRatingSummary());
 * @method static mixed getRatingHistory(DateTime $date_from, DateTime $date_to, array $ratings, bool $with_premium_scores = null);
 **/

class Ozon extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ozon';
    }
}
