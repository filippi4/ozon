<?php
namespace Filippi4\Ozon;

use DateTime;

class Ozon extends OzonClient
{
    private const DT_FORMAT_DATE_TIME = 'Y-m-d\TH:i:s.u\Z';
    private const DT_FORMAT_DATE      = 'Y-m-d';
    private const DT_FORMAT_DATE_YM   = 'Y-m';

    public function config(array $keys): Ozon
    {
        $this->validateKeys($keys);

        $this->config = $keys;

        return $this;
    }

    private function formatDate(?DateTime $dateTime, string $format = self::DT_FORMAT_DATE_TIME): ?string
    {
        return $dateTime ? $dateTime->format($format) : null;
    }

    private function wrapToArrayIfNotNull(mixed $value): ?array
    {
        return $value !== null ? [$value] : null;
    }

    /**
     * Дерево категории товаров

     * Возвращает категории для товаров в виде дерева. Создание товаров доступно
     * только в категориях последнего уровня, сравните именно эти категории с
     * категориями своей площадки. Категории не создаются по запросу пользователя.
     *
     * @param int|null $category_id <int64> Идентификатор категории.
     * @param string $language (Language) Язык в ответе:
     *  - EN — английский,
     *  - RU — русский,
     *  - TR — турецкий.
     * По умолчанию используется русский язык.
     * Default: "DEFAULT"
     * Enum: "DEFAULT" "RU" "EN" "TR"
     * @return mixed
     */
    public function getCategoryTree(int $category_id = null, string $language = 'DEFAULT'): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/description-category/tree',
                    array_merge(compact('language'), array_diff(compact('category_id'), ['']))
                )
            )
        )->data;
    }

    /**
     * Список характеристик категории
     *
     * Получение характеристик для указанной категории товаров.
     * Передавайте не более 20 идентификаторов категорий в списке category_id.
     * Узнать, есть ли у атрибута вложенный справочник, можно по параметру dictionary_id.
     * Если значение 0 — справочника нет. Если значение другое, то справочники есть.
     * Их нужно запрашивать методом /v2/category/attribute/values.
     *
     * @param string $attribute_type (CategoryAttributesRequestAttributeType) Фильтр по характеристикам:
     *  - ALL — все характеристики,
     *  - REQUIRED — обязательные,
     *  - OPTIONAL — дополнительные.
     * Default: "ALL"
     * Enum: "ALL" "REQUIRED" "OPTIONAL"
     * @param array $category_id Array of integers <int64> Идентификатор категории.
     * @param string $language (Language) Язык в ответе:
     *  - EN — английский,
     *  - RU — русский,
     *  - TR — турецкий.
     * По умолчанию используется русский язык.
     * Default: "DEFAULT"
     * Enum: "DEFAULT" "RU" "EN" "TR"
     * @return mixed
     */
    public function getCategoryAttribute(array $category_id, string $attribute_type = 'ALL', string $language = 'DEFAULT'): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v3/category/attribute',
                    compact('attribute_type', 'category_id', 'language')
                )
            )
        )->data;
    }

    /**
     * Справочник значений характеристики
     *
     * Узнать, есть ли у атрибута вложенный справочник, можно через метод /v3/category/attribute.
     * Если справочники есть, их нужно запрашивать этим методом.
     *
     * @param int $attribute_id <int64> Идентификатор характеристики.
     * @param int $category_id <int64> Идентификатор категории.
     * @param int|null $last_value_id <int64> Идентификатор справочника,
     * с которого нужно начать ответ. Если last_value_id — 10,
     * то в ответе будут справочники, начиная с одиннадцатого.
     * @param int $limit <int64> Количество значений в ответе:
     *  - максимум — 5000,
     *  - минимум — 1.
     * @param string $language (Language) Язык в ответе:
     *  - EN — английский,
     *  - RU — русский.
     * Default: "DEFAULT"
     * Enum: "DEFAULT" "RU" "EN"
     * По умолчанию используется русский язык.
     * @return mixed
     */
    public function getCategoryAttributeValues(int $attribute_id, int $category_id, int $last_value_id = null, int $limit = 5000, string $language = 'DEFAULT'): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v2/category/attribute/values',
                    array_diff(compact('attribute_id', 'category_id', 'language', 'last_value_id', 'limit'), [''])
                )
            )
        )->data;
    }

    /**
     * Создать или обновить товар
     *
     * @param array $items
     * @return mixed
     */
    public function productImportV2(array $items): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v2/product/import',
                    compact('items')
                )
            )
        )->data;
    }

    /**
     * Узнать статус добавления товара
     *
     * @param string $taskId
     * @return mixed
     */
    public function productImportInfo(string $task_id): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/product/import/info',
                    compact('task_id')
                )
            )
        )->data;
    }

    /**
     * Список товаров
     *
     * Mетод для получения списка товаров
     * @param array $sku <int64> Количество значений на странице. Минимум — 1, максимум — 1000.
     * @return mixed
     */
    public function getProductList(array $sku = null): mixed
    {

        return (
            new OzonData(
                $this->postResponse(
                    'v3/product/info/list',
                    array_merge(compact('sku'))
                )
            )
        )->data;
    }

    /**
     * Информация о товарах
     *
     * Обязательно должен быть передан один из параметров: offer_id, product_id, sku.
     * @param string|null $offer_id Идентификатор товара в системе продавца — артикул.
     * @param int|null $product_id <int64> Идентификатор товара.
     * @param int|null $sku <int64> Идентификатор товара в системе Ozon — SKU.
     * @return mixed
     */
    public function getProductInfo(string $offer_id = null, int $product_id = null, int $sku = null): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v2/product/info',
                    array_diff(compact('offer_id', 'product_id', 'sku'), [''])
                )
            )
        )->data;
    }

    /**
     * Получить список товаров по идентификаторам
     *
     * Метод для получения массива товаров по их идентификаторам.
     * В теле запроса должен быть массив однотипных идентификаторов, в ответе будет массив items.
     * Для каждого товара внутри массива items поля совпадают с полями из метода /v2/product/info.
     *
     * Обязательно должен быть передан один из параметров: offer_id, product_id, sku.
     * @param array|null $offer_id Array of strings Идентификатор товара в системе продавца — артикул.
     * Максимальное количество товаров — 1000.
     * @param array|null $product_id Array of integers <int64> Идентификатор товара.
     * @param array|null $sku Array of integers <int64> Идентификатор товара в системе Ozon — SKU.
     * @return mixed
     */
    public function getProductInfoList(array $offer_id = null, array $product_id = null, array $sku = null): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v3/product/info/list',
                    compact('offer_id', 'product_id', 'sku')
                )
            )
        )->data;
    }

    /*
     * @param array $discounted_skus
     * @return mixed
     */
    public function getDiscountedSkus(array $discounted_skus): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/product/info/discounted',
                    compact('discounted_skus')
                )
            )
        )->data;
    }

    /**
     * Получить описание товара
     *
     * Обязательно должен быть передан один из параметров: offer_id, product_id.
     * @param string|null $offer_id Идентификатор товара в системе продавца — артикул.
     * @param int|null $product_id <int64> Идентификатор товара.
     * @return mixed
     */
    public function getProductInfoDescription(string $offer_id = null, int $product_id = null): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/product/info/description',
                    array_diff(compact('offer_id', 'product_id'), [''])
                )
            )
        )->data;
    }

    /**
     * Получить контент-рейтинг товаров по SKU
     *
     * Метод для получения контент-рейтинга товаров, а также рекомендаций по его увеличению.
     *
     * @param array $skus Array of strings <int64> Список SKU товаров, для которых нужно вернуть контент-рейтинг.
     * @return mixed
     */
    public function getProductRatingBySku(array $skus): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/product/rating-by-sku',
                    compact('skus')
                )
            )
        )->data;
    }

    /**
     * Получить описание характеристик товара
     *
     * Возвращает описание характеристик товара по его идентификатору.
     * Товар можно искать по offer_id или product_id.
     *
     * filter object (productv3Filter) Фильтр по товарам.
     *      @param array|null $offer_id Array of strings Фильтр по параметру offer_id.
     *      Можно передавать список значений.
     *      @param array|null $product_id Array of strings <int64> Фильтр по параметру product_id.
     *      Можно передавать список значений.
     *      @param string $visibility (productv2GetProductListRequestFilterFilterVisibility)
     *      Фильтр по видимости товара:
     *       - ALL — все товары, кроме архивных.
     *       - VISIBLE — товары, которые видны покупателям.
     *       - INVISIBLE — товары, которые не видны покупателям.
     *       - EMPTY_STOCK — товары, у которых не указано наличие.
     *       - NOT_MODERATED — товары, которые не прошли модерацию.
     *       - MODERATED — товары, которые прошли модерацию.
     *       - DISABLED — товары, которые видны покупателям, но недоступны к покупке.
     *       - STATE_FAILED — товары, создание которых завершилось ошибкой.
     *       - READY_TO_SUPPLY — товары, готовые к поставке.
     *       - VALIDATION_STATE_PENDING — товары, которые проходят проверку валидатором на премодерации.
     *       - VALIDATION_STATE_FAIL — товары, которые не прошли проверку валидатором на премодерации.
     *       - VALIDATION_STATE_SUCCESS — товары, которые прошли проверку валидатором на премодерации.
     *       - TO_SUPPLY — товары, готовые к продаже.
     *       - IN_SALE — товары в продаже.
     *       - REMOVED_FROM_SALE — товары, скрытые от покупателей.
     *       - BANNED — заблокированные товары.
     *       - OVERPRICED — товары с завышенной ценой.
     *       - CRITICALLY_OVERPRICED — товары со слишком завышенной ценой.
     *       - EMPTY_BARCODE — товары без штрихкода.
     *       - BARCODE_EXISTS — товары со штрихкодом.
     *       - QUARANTINE — товары на карантине после изменения цены более чем на 50%.
     *       - ARCHIVED — товары в архиве.
     *       - OVERPRICED_WITH_STOCK — товары в продаже со стоимостью выше, чем у конкурентов.
     *       - PARTIAL_APPROVED — товары в продаже с пустым или неполным описанием.
     *       - IMAGE_ABSENT — товары без изображений.
     *       - MODERATION_BLOCK — товары, для которых заблокирована модерация.
     *      Default: "ALL"
     *      Enum: "ALL" "VISIBLE" "INVISIBLE" "EMPTY_STOCK" "NOT_MODERATED" "MODERATED" "DISABLED" "STATE_FAILED"
     *      "READY_TO_SUPPLY" "VALIDATION_STATE_PENDING" "VALIDATION_STATE_FAIL" "VALIDATION_STATE_SUCCESS" "TO_SUPPLY"
     *      "IN_SALE" "REMOVED_FROM_SALE" "BANNED" "OVERPRICED" "CRITICALLY_OVERPRICED" "EMPTY_BARCODE" "BARCODE_EXISTS"
     *      "QUARANTINE" "ARCHIVED" "OVERPRICED_WITH_STOCK" "PARTIAL_APPROVED" "IMAGE_ABSENT" "MODERATION_BLOCK"
     * @param string|null $last_id Идентификатор последнего значения на странице.
     * Оставьте это поле пустым при выполнении первого запроса.
     * Чтобы получить следующие значения, укажите last_id из ответа предыдущего запроса.
     * @param int $limit <int64> Количество значений на странице. Минимум — 1, максимум — 1000.
     * @param string|null $sort_by Параметр, по которому товары будут отсортированы.
     *               Enum: "id" "title" "offer_id" "spu" "sku" "seller_sku" "created_at" "volume" "price_index"
     * @param string $sort_dir Направление сортировки.
     *               Default: "ASC"
     *               Enum: "ASC" "DESC"
     * @return mixed
     */
    public function getProductsInfoAttributes(
        array $offer_id = null,
        array $product_id = null,
        string $visibility = 'ALL',
        string $last_id = null,
        int $limit = 1000,
        string $sort_by = null,
        string $sort_dir = 'ASC'
    ): mixed {
        $filter = compact(['offer_id', 'product_id', 'visibility']);
        return (
            new OzonData(
                $this->postResponse(
                    'v4/product/info/attributes',
                    array_merge(compact('filter', 'limit', 'sort_dir'), array_diff(compact('last_id', 'sort_by'), ['']))
                )
            )
        )->data;
    }

    /**
     * Получить список геоограничений для услуг
     *
     * filter object (v2GetGeoRestrictionsByFilterRequestFilter) Фильтр.
     * Чтобы посмотреть все геоограничения, оставьте names пустым, а в only_visible передайте true.
     *      @param array|null $names Array of strings Список с названиями городов.
     *      @param bool $only_visible Видимость значения.
     *      Рекомендуем всегда передавать true в этом параметре.
     * @param int|null $last_order_number <int64> Порядок геоограничения, с которого выводим данные в ответе.
     * Если указать 23, то на выходе у первого элемента списка restrictions будет order_number = 24.
     * Если вы хотите получить все геоограничения, укажите 0 в этом параметре.
     * @param int|null $limit <int64> Количество результатов в ответе.
     * @return mixed
     */
    public function getProductsGeoRestrictionsCatalogByFilter(array $names = null, bool $only_visible = true, int $last_order_number = null, int $limit = null): mixed
    {
        $filter = compact('names', 'only_visible');
        return (
            new OzonData(
                $this->postResponse(
                    'v1/products/geo-restrictions-catalog-by-filter',
                    array_merge(compact('filter'), array_diff(compact('last_order_number', 'limit'), ['']))
                )
            )
        )->data;
    }

    /**
     * Информация о количестве товаров
     *
     * Возвращает информацию о ĸоличестве товаров на сĸладах:
     *  - сĸольĸо единиц есть в наличии,
     *  - сĸольĸо зарезервировано поĸупателями.
     *
     * filter object (productv3Filter) Фильтр по товарам.
     *      @param array|null $offer_id Array of strings Фильтр по параметру offer_id.
     *      Можно передавать список значений.
     *      @param array|null $product_id Array of strings <int64> Фильтр по параметру product_id.
     *      Можно передавать список значений.
     *      @param string $visibility (productv2GetProductListRequestFilterFilterVisibility)
     *      Фильтр по видимости товара:
     *       - ALL — все товары, кроме архивных.
     *       - VISIBLE — товары, которые видны покупателям.
     *       - INVISIBLE — товары, которые не видны покупателям.
     *       - EMPTY_STOCK — товары, у которых не указано наличие.
     *       - NOT_MODERATED — товары, которые не прошли модерацию.
     *       - MODERATED — товары, которые прошли модерацию.
     *       - DISABLED — товары, которые видны покупателям, но недоступны к покупке.
     *       - STATE_FAILED — товары, создание которых завершилось ошибкой.
     *       - READY_TO_SUPPLY — товары, готовые к поставке.
     *       - VALIDATION_STATE_PENDING — товары, которые проходят проверку валидатором на премодерации.
     *       - VALIDATION_STATE_FAIL — товары, которые не прошли проверку валидатором на премодерации.
     *       - VALIDATION_STATE_SUCCESS — товары, которые прошли проверку валидатором на премодерации.
     *       - TO_SUPPLY — товары, готовые к продаже.
     *       - IN_SALE — товары в продаже.
     *       - REMOVED_FROM_SALE — товары, скрытые от покупателей.
     *       - BANNED — заблокированные товары.
     *       - OVERPRICED — товары с завышенной ценой.
     *       - CRITICALLY_OVERPRICED — товары со слишком завышенной ценой.
     *       - EMPTY_BARCODE — товары без штрихкода.
     *       - BARCODE_EXISTS — товары со штрихкодом.
     *       - QUARANTINE — товары на карантине после изменения цены более чем на 50%.
     *       - ARCHIVED — товары в архиве.
     *       - OVERPRICED_WITH_STOCK — товары в продаже со стоимостью выше, чем у конкурентов.
     *       - PARTIAL_APPROVED — товары в продаже с пустым или неполным описанием.
     *       - IMAGE_ABSENT — товары без изображений.
     *       - MODERATION_BLOCK — товары, для которых заблокирована модерация.
     *      Default: "ALL"
     *      Enum: "ALL" "VISIBLE" "INVISIBLE" "EMPTY_STOCK" "NOT_MODERATED" "MODERATED" "DISABLED" "STATE_FAILED"
     *      "READY_TO_SUPPLY" "VALIDATION_STATE_PENDING" "VALIDATION_STATE_FAIL" "VALIDATION_STATE_SUCCESS" "TO_SUPPLY"
     *      "IN_SALE" "REMOVED_FROM_SALE" "BANNED" "OVERPRICED" "CRITICALLY_OVERPRICED" "EMPTY_BARCODE" "BARCODE_EXISTS"
     *      "QUARANTINE" "ARCHIVED" "OVERPRICED_WITH_STOCK" "PARTIAL_APPROVED" "IMAGE_ABSENT" "MODERATION_BLOCK"
     * @param string|null $last_id Идентификатор последнего значения на странице.
     * Оставьте это поле пустым при выполнении первого запроса.
     * Чтобы получить следующие значения, укажите last_id из ответа предыдущего запроса.
     * @param int $limit <int64> Количество значений на странице. Минимум — 1, максимум — 1000.
     * @return mixed
     */
    public function getProductInfoStocks(array $offer_id = null, array $product_id = null, string $visibility = 'ALL', string $cursor = null, int $limit = 1000): mixed
    {
        $filter = compact('offer_id', 'product_id', 'visibility');
        return (
            new OzonData(
                $this->postResponse(
                    'v4/product/info/stocks',
                    array_merge(compact('filter', 'limit'), array_diff(compact('cursor'), ['']))
                )
            )
        )->data;
    }

    /**
     * Информация об остатках на складах продавца (FBS и rFBS)
     *
     * @param array $sku Array of strings <int64> SKU товара, который продаётся со склада продавца (схемы FBS и rFBS).
     * Получите sku в ответе методов /v2/product/info и /v3/product/info/list.
     * Максимальное количестов SKU в одном запросе — 500.
     * @return mixed
     */
    public function getStocksByWarehouseFbs(array $sku): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/product/info/stocks-by-warehouse/fbs',
                    compact('sku')
                )
            )
        )->data;
    }

    /**
     * Получить информацию о цене товара
     *
     * filter object (productv3Filter) Фильтр по товарам.
     *      @param array|null $offer_id Array of strings Фильтр по параметру offer_id.
     *      Можно передавать список значений.
     *      @param array|null $product_id Array of strings <int64> Фильтр по параметру product_id.
     *      Можно передавать список значений.
     *      @param string $visibility (productv2GetProductListRequestFilterFilterVisibility)
     *      Фильтр по видимости товара:
     *       - ALL — все товары, кроме архивных.
     *       - VISIBLE — товары, которые видны покупателям.
     *       - INVISIBLE — товары, которые не видны покупателям.
     *       - EMPTY_STOCK — товары, у которых не указано наличие.
     *       - NOT_MODERATED — товары, которые не прошли модерацию.
     *       - MODERATED — товары, которые прошли модерацию.
     *       - DISABLED — товары, которые видны покупателям, но недоступны к покупке.
     *       - STATE_FAILED — товары, создание которых завершилось ошибкой.
     *       - READY_TO_SUPPLY — товары, готовые к поставке.
     *       - VALIDATION_STATE_PENDING — товары, которые проходят проверку валидатором на премодерации.
     *       - VALIDATION_STATE_FAIL — товары, которые не прошли проверку валидатором на премодерации.
     *       - VALIDATION_STATE_SUCCESS — товары, которые прошли проверку валидатором на премодерации.
     *       - TO_SUPPLY — товары, готовые к продаже.
     *       - IN_SALE — товары в продаже.
     *       - REMOVED_FROM_SALE — товары, скрытые от покупателей.
     *       - BANNED — заблокированные товары.
     *       - OVERPRICED — товары с завышенной ценой.
     *       - CRITICALLY_OVERPRICED — товары со слишком завышенной ценой.
     *       - EMPTY_BARCODE — товары без штрихкода.
     *       - BARCODE_EXISTS — товары со штрихкодом.
     *       - QUARANTINE — товары на карантине после изменения цены более чем на 50%.
     *       - ARCHIVED — товары в архиве.
     *       - OVERPRICED_WITH_STOCK — товары в продаже со стоимостью выше, чем у конкурентов.
     *       - PARTIAL_APPROVED — товары в продаже с пустым или неполным описанием.
     *       - IMAGE_ABSENT — товары без изображений.
     *       - MODERATION_BLOCK — товары, для которых заблокирована модерация.
     *      Default: "ALL"
     *      Enum: "ALL" "VISIBLE" "INVISIBLE" "EMPTY_STOCK" "NOT_MODERATED" "MODERATED" "DISABLED" "STATE_FAILED"
     *      "READY_TO_SUPPLY" "VALIDATION_STATE_PENDING" "VALIDATION_STATE_FAIL" "VALIDATION_STATE_SUCCESS" "TO_SUPPLY"
     *      "IN_SALE" "REMOVED_FROM_SALE" "BANNED" "OVERPRICED" "CRITICALLY_OVERPRICED" "EMPTY_BARCODE" "BARCODE_EXISTS"
     *      "QUARANTINE" "ARCHIVED" "OVERPRICED_WITH_STOCK" "PARTIAL_APPROVED" "IMAGE_ABSENT" "MODERATION_BLOCK"
     * @param string|null $last_id Идентификатор последнего значения на странице.
     * Оставьте это поле пустым при выполнении первого запроса.
     * Чтобы получить следующие значения, укажите last_id из ответа предыдущего запроса.
     * @param int $limit <int64> Количество значений на странице. Минимум — 1, максимум — 1000.
     * @return mixed
     */
    public function getProductInfoPrices(array $offer_id = null, array $product_id = null, string $visibility = 'ALL', string $cursor = null, int $limit = 1000): mixed
    {
        $filter = compact('offer_id', 'product_id', 'visibility');
        return (
            new OzonData(
                $this->postResponse(
                    'v5/product/info/prices',
                    array_merge(compact('filter', 'limit'), array_diff(compact('cursor'), ['']))
                )
            )
        )->data;
    }

    /**
     * Узнать информацию об уценке и основном товаре по SKU уценённого товара
     *
     * Метод для получения информации о состоянии и дефектах уценённого товара по его SKU.
     * Также метод возвращает SKU основного товара.
     *
     * @param array $discounted_skus Array of strings <int64> Список SKU уценённых товаров.
     * @return mixed
     */
    public function getProductInfoDiscounted(array $discounted_skus): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/product/info/discounted',
                    compact('discounted_skus')
                )
            )
        )->data;
    }

    /**
     * Mетод для получения списка акций, в которых можно участвовать.
     *
     * @return mixed
     */
    public function getActions(): mixed
    {
        return (
            new OzonData(
                $this->getResponse(
                    'v1/actions'
                )
            )
        )->data;
    }

    /**
     * Список доступных для акции товаров
     *
     * Метод для получения списка товаров, которые могут участвовать в акции, по её идентификатору.
     *
     * @param float action_id <double> Идентификатор акции.
     * @param string|null last_id <double> Идентификатор последнего значения на странице
     * Например, если offset=10, ответ начнётся с 11-го найденного элемента.
     * @param float|null limit <double> Количество ответов на странице. По умолчанию — 100.
     * @return mixed
     */
    public function getActionsCandidates(float $action_id, string | null $last_id, ?float $limit = 100): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/actions/candidates',
                    array_diff(compact('action_id', 'last_id', 'limit'), [''])
                )
            )
        )->data;
    }

    /**
     * Список участвующих в акции товаров
     *
     * Метод для получения списка товаров, участвующих в акции, по её идентификатору.
     *
     * @param float action_id <double> Идентификатор акции.
     * @param float|null offset <double> Количество элементов, которое будет пропущено в ответе.
     * Например, если offset=10, ответ начнётся с 11-го найденного элемента.
     * @param float|null limit <double> Количество ответов на странице. По умолчанию — 100.
     * @return mixed
     */
    public function getActionsProducts(float $action_id, string $last_id = "", ?float $limit = 100): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/actions/products',
                    array_diff(compact('action_id', 'last_id', 'limit'), [''])
                )
            )
        )->data;
    }

    /**
     * Список доступных акций Hot Sale
     *
     * @return mixed
     */
    public function getActionsHotSalesList(): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/actions/hotsales/list'
                )
            )
        )->data;
    }

    /**
     * Список товаров, которые участвуют в акции Hot Sale
     *
     * Метод для получения списка товаров, которые могут участвовать
     * или уже участвуют в акции Hot Sale.
     *
     * @param float hotsale_id <double> Идентификатор акции Hot Sale.
     * @param float|null offset <double> Количество элементов, которое будет пропущено в ответе.
     * Например, если offset = 10, то ответ начнётся с 11-го найденного элемента.
     * @param float limit <double> Количество элементов в ответе. По умолчанию — 100.
     * @return mixed
     */
    public function getActionsHotSalesProducts(float $hotsale_id, float $offset = null, float $limit = 100): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/actions/hotsales/products',
                    array_diff(compact('hotsale_id', 'offset', 'limit'), [''])
                )
            )
        )->data;
    }

    /**
     * Cправочник типов соответствия требованиям
     *
     * @return mixed
     */
    public function getProductCertificateAccordanceTypes(): mixed
    {
        return (
            new OzonData(
                $this->getResponse(
                    'v1/product/certificate/accordance-types'
                )
            )
        )->data;
    }

    /**
     * Справочник типов документов
     *
     * @return mixed
     */
    public function getProductCertificateTypes(): mixed
    {
        return (
            new OzonData(
                $this->getResponse(
                    'v1/product/certificate/types'
                )
            )
        )->data;
    }

    /**
     * Список сертифицируемых категорий
     *
     * @param int $page <int32> Номер страницы, возвращаемой в запросе.
     * @param int $page_size <int32> Количество элементов на странице.
     * @return mixed
     */
    public function getProductCertificationList(int $page = 1, int $page_size = 1000): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/product/certification/list',
                    compact('page', 'page_size')
                )
            )
        )->data;
    }

    /**
     * Список сертифицируемых брендов
     *
     * Метод для получения списка брендов, для которых требуется предоставить сертификат.
     * Ответ содержит список брендов, товары которых есть в вашем личном кабинете.
     * Список брендов может изменяться, если Ozon получит требование от бренда предоставлять сертификат.
     *
     * @param int $page <int32> Номер страницы, возвращаемой в запросе.
     * @param int $page_size <int32> Количество элементов на странице.
     * @return mixed
     */
    public function getBrandCompanyCartificationList(int $page = 1, int $page_size = 1000): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/brand/company-certification/list',
                    compact('page', 'page_size')
                )
            )
        )->data;
    }

    /**
     * Список складов
     *
     * В запросе не нужно указывать параметры. Ваша компания будет определена по Client-ID.
     *
     * @return mixed
     */
    public function getWarehouseList(): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/warehouse/list'
                )
            )
        )->data;
    }

    /**
     * Список методов доставки склада
     *
     * filter object (DeliveryMethodListRequestFilter) Фильтр для поиска методов доставки.
     *      @param int|null $warehouse_id <int64> Идентификатор склада.
     *      @param int|null $provider_id <int64> Идентификатор службы доставки.
     *      @param string|null $status Статус метода доставки:
     *       - NEW — создан,
     *       - EDITED — редактируется,
     *       - ACTIVE — активный,
     *       - DISABLED — неактивный.
     * @param int|null $offset <int64> Количество элементов, которое будет пропущено в ответе.
     * Например, если offset = 10, то ответ начнётся с 11-го найденного элемента.
     * @param int $limit <int64> Количество элементов в ответе. Максимум — 50, минимум — 1.
     * @return mixed
     */
    public function getDeliveryMethodList(int $warehouse_id = null, int $provider_id = null, string $status = null, int $offset = null, int $limit = 50): mixed
    {
        $filter = compact('warehouse_id', 'provider_id', 'status');
        return (
            new OzonData(
                $this->postResponse(
                    'v1/delivery-method/list',
                    array_merge(compact('filter', 'limit'), array_diff(compact('offset'), ['']))
                )
            )
        )->data;
    }

    /**
     * Список отправлений
     *
     * Возвращает список отправлений за указанный период времени.
     * Дополнительно можно отфильтровать отправления по их статусу.
     *
     * filter object (postingGetFboPostingListRequestFilter) Фильтр для поиска отправлений.
     *      @param DateTime|null $since <date-time> Начало периода в формате YYYY-MM-DD.
     *      @param DateTime|null $to <date-time> Конец периода в формате YYYY-MM-DD.
     *      @param string|null $status Статус отправления.
     *       - awaiting_packaging — ожидает упаковки,
     *       - awaiting_deliver — ожидает отгрузки,
     *       - delivering — доставляется,
     *       - delivered — доставлено,
     *       - cancelled — отменено.
     * @param string|null $dir Направление сортировки:
     *  - asc — по возрастанию,
     *  - desc — по убыванию.
     * @param bool|null $translit boolean Если включена транслитерация адреса из кириллицы в латиницу — true.
     * with object (postingFboPostingWithParams) Дополнительные поля, которые нужно добавить в ответ.
     *      @param bool|null $analytics_data Передайте true, чтобы добавить в ответ данные аналитики.
     *      @param bool|null $financial_data Передайте true, чтобы добавить в ответ финансовые данные.
     * @param int|null $offset <int64> Количество элементов, которое будет пропущено в ответе.
     * Например, если offset = 10, то ответ начнётся с 11-го найденного элемента.
     * @param int $limit <int64> Количество значений в ответе:
     *  - максимум — 1000,
     *  - минимум — 1.
     * @return mixed
     */
    public function getPostingFboList(
        DateTime $since = null,
        DateTime $to = null,
        string $status = null,
        bool $translit = null,
        bool $analytics_data = null,
        bool $financial_data = null,
        string $dir = 'ASC',
        int $offset = null,
        int $limit = 1000
    ): mixed {
        $since = $this->formatDate($since);
        $to    = $this->formatDate($to);

        $filter = compact('since', 'status', 'to');
        $with   = compact('analytics_data', 'financial_data');
        return (
            new OzonData(
                $this->postResponse(
                    'v2/posting/fbo/list',
                    array_merge(compact('filter', 'with', 'limit'), array_diff(compact('dir', 'offset', 'translit'), ['']))
                )
            )
        )->data;
    }

    /**
     * Информация об отправлении
     *
     * Возвращает информацию об отправлении по его идентификатору.
     *
     * @param string $posting_number Номер отправления.
     * @param bool|null $translit Если включена транслитерация адреса из кириллицы в латиницу — true.
     * with object (postingFboPostingWithParams) Дополнительные поля, которые нужно добавить в ответ.
     *      @param bool|null $analytics_data Передайте true, чтобы добавить в ответ данные аналитики.
     *      @param bool|null $financial_data Передайте true, чтобы добавить в ответ финансовые данные.
     * @return mixed
     */
    public function getPostingFboGet(string $posting_number, bool $translit = null, bool $analytics_data = null, bool $financial_data = null): mixed
    {
        $with = compact('analytics_data', 'financial_data');
        return (
            new OzonData(
                $this->postResponse(
                    'v2/posting/fbo/get',
                    array_merge(compact('posting_number', 'with'), array_diff(compact('translit'), ['']))
                )
            )
        )->data;
    }

    /**
     * Список необработанных отправлений (версия 3)
     *
     * Возвращает список необработанных отправлений за указанный период времени — он должен не больше одного года.
     * Возможные статусы отправлений:
     *  - awaiting_registration — ожидает регистрации,
     *  - acceptance_in_progress — идёт приёмка,
     *  - awaiting_approve — ожидает подтверждения,
     *  - awaiting_packaging — ожидает упаковки,
     *  - awaiting_deliver — ожидает отгрузки,
     *  - arbitration — арбитраж,
     *  - client_arbitration — клиентский арбитраж доставки,
     *  - delivering — доставляется,
     *  - driver_pickup — у водителя,
     *  - delivered — доставлено,
     *  - cancelled — отменено,
     *  - not_accepted — не принят на сортировочном центре,
     *  - sent_by_seller — отправлено продавцом.
     *
     * Обязательно должна быть передана пара параметров: cutoff_from, cutoff_to или delivering_date_from, delivering_date_to.
     * filter object (postingv3GetFbsPostingUnfulfilledListRequestFilter) Фильтр запроса.
     *      Используйте фильтр либо по времени сборки — cutoff,
     *      либо по дате передачи отправления в доставку — delivering_date.
     *      Если использовать их вместе, в ответе вернётся ошибка.
     *      Чтобы использовать фильтр по времени сборки, заполните поля cutoff_from и cutoff_to.
     *      Чтобы использовать фильтр по дате передачи отправления в доставку,
     *      заполните поля delivering_date_from и delivering_date_to.
     *      @param DateTime|null $cutoff_from <date-time> Фильтр по времени,
     *      до которого продавцу нужно собрать заказ. Начало периода.
     *      Формат: YYYY-MM-DDThh:mm:ss.mcsZ. Пример: 2020-03-18T07:34:50.359Z.
     *      @param DateTime|null $cutoff_to <date-time> Фильтр по времени,
     *      до которого продавцу нужно собрать заказ. Конец периода.
     *      Формат: YYYY-MM-DDThh:mm:ss.mcsZ. Пример: 2020-03-18T07:34:50.359Z.
     *      @param DateTime|null $delivering_date_from <date-time> Минимальная дата передачи отправления в доставку.
     *      @param DateTime|null $delivering_date_to <date-time> Максимальная дата передачи отправления в доставку.
     *      delivery_method_id Array of integers <int64> Идентификатор способа доставки.
     *      @param array|null $provider_id Array of integers <int64> Идентификатор службы доставки.
     *      @param string|null $status Статус отправления:
     *       - acceptance_in_progress — идёт приёмка,
     *       - awaiting_approve — ожидает подтверждения,
     *       - awaiting_packaging — ожидает упаковки,
     *       - awaiting_registration — ожидает регистрации,
     *       - awaiting_deliver — ожидает отгрузки,
     *       - arbitration — арбитраж,
     *       - client_arbitration — клиентский арбитраж доставки,
     *       - delivering — доставляется,
     *       - driver_pickup — у водителя,
     *       - not_accepted — не принят на сортировочном центре.
     *      @param array|null $warehouse_id Array of integers <int64> Идентификатор склада.
     * Например, если offset = 10, то ответ начнётся с 11-го найденного элемента.
     * with object (postingv3FbsPostingWithParams) Дополнительные поля, которые нужно добавить в ответ.
     *      @param bool|null $analytics_data Добавить в ответ данные аналитики.
     *      @param bool|null $barcodes Добавить в ответ штрихкоды отправления.
     *      @param bool|null $financial_data Добавить в ответ финансовые данные.
     *      @param bool|null $translit Выполнить транслитерацию возвращаемых значений.
     * @param string $dir Направление сортировки:
     *  - asc — по возрастанию,
     *  - desc — по убыванию.
     * @param int|null $offset <int64> Количество элементов, которое будет пропущено в ответе.
     * @param int $limit <int64> Количество значений в ответе:
     *  - максимум — 1000,
     *  - минимум — 1.
     * @return mixed
     */
    public function getPostingFbsUnfulfilledList(
        DateTime $cutoff_from = null,
        DateTime $cutoff_to = null,
        DateTime $delivering_date_from = null,
        DateTime $delivering_date_to = null,
        array $provider_id = null,
        string $status = null,
        array $warehouse_id = null,
        bool $analytics_data = null,
        bool $barcodes = null,
        bool $financial_data = null,
        bool $translit = null,
        string $dir = 'ASC',
        int $offset = null,
        int $limit = 1000
    ): mixed {
        $cutoff_from          = $this->formatDate($cutoff_from);
        $cutoff_to            = $this->formatDate($cutoff_to);
        $delivering_date_from = $this->formatDate($delivering_date_from);
        $delivering_date_to   = $this->formatDate($delivering_date_to);

        $filter = compact('cutoff_from', 'cutoff_to', 'delivering_date_from', 'delivering_date_to', 'provider_id', 'status', 'warehouse_id');
        $with   = compact('analytics_data', 'barcodes', 'financial_data', 'translit');
        return (
            new OzonData(
                $this->postResponse(
                    'v3/posting/fbs/unfulfilled/list',
                    array_merge(compact('filter', 'with', 'dir', 'limit'), array_diff(compact('offset'), ['']))
                )
            )
        )->data;
    }

    /**
     * Список отправлений (версия 3)
     *
     * Возвращает список отправлений за указанный период времени — он должен быть не больше одного года.
     * Дополнительно можно отфильтровать отправления по их статусу.
     * has_next = true в ответе может значить, что вернули не весь массив отправлений.
     * Чтобы получить информацию об остальных отправлениях, сделайте новый запрос с другим значением offset.
     *
     * filter object (postingv3GetFbsPostingListRequestFilter) Фильтр.
     *      @param DateTime $since <date-time> Дата начала периода, за который нужно получить список отправлений.
     *      Формат UTC: ГГГГ-ММ-ДДTЧЧ:ММ:ССZ. Пример: 2019-08-24T14:15:22Z.
     *      @param DateTime $to <date-time> Дата конца периода, за который нужно получить список отправлений.
     *      Формат UTC: ГГГГ-ММ-ДДTЧЧ:ММ:ССZ. Пример: 2019-08-24T14:15:22Z.
     *      @param string|null $status Статус отправления:
     *       - awaiting_registration — ожидает регистрации,
     *       - acceptance_in_progress — идёт приёмка,
     *       - awaiting_approve — ожидает подтверждения,
     *       - awaiting_packaging — ожидает упаковки,
     *       - awaiting_deliver — ожидает отгрузки,
     *       - arbitration — арбитраж,
     *       - client_arbitration — клиентский арбитраж доставки,
     *       - delivering — доставляется,
     *       - driver_pickup — у водителя,
     *       - delivered — доставлено,
     *       - cancelled — отменено,
     *       - not_accepted — не принят на сортировочном центре,
     *       - sent_by_seller – отправлено продавцом.
     *      @param array|null $delivery_method_id Array of integers <int64> Идентификатор способа доставки.
     *      @param int|null $order_id <int64> Идентификатор заказа.
     *      @param array|null $provider_id Array of integers <int64> Идентификатор службы доставки.
     *      @param array|null $warehouse_id Array of integers <int64> Идентификатор склада.
     * with object (postingv3FbsPostingWithParams) Дополнительные поля, которые нужно добавить в ответ.
     *      @param bool|null $analytics_data Добавить в ответ данные аналитики.
     *      @param bool|null $barcodes Добавить в ответ штрихкоды отправления.
     *      @param bool|null $financial_data Добавить в ответ финансовые данные.
     *      @param bool|null $translit Выполнить транслитерацию возвращаемых значений.
     * @param string $dir Направление сортировки:
     *  - asc — по возрастанию,
     *  - desc — по убыванию.
     * @param int|null $offset <int64> Количество элементов, которое будет пропущено в ответе.
     * Например, если offset = 10, то ответ начнётся с 11-го найденного элемента.
     * @param int $limit <int64> Количество значений в ответе:
     *  - максимум — 1000,
     *  - минимум — 1.
     * @return mixed
     */
    public function getPostingFbsList(
        DateTime $since,
        DateTime $to,
        string $status = null,
        array $delivery_method_id = null,
        int $order_id = null,
        array $provider_id = null,
        array $warehouse_id = null,
        bool $analytics_data = null,
        bool $barcodes = null,
        bool $financial_data = null,
        bool $translit = null,
        string $dir = 'ASC',
        int $offset = null,
        int $limit = 1000
    ): mixed {
        $since = $this->formatDate($since);
        $to    = $this->formatDate($to);

        $filter = compact('since', 'to', 'status', 'delivery_method_id', 'order_id', 'provider_id', 'warehouse_id');
        $with   = compact('analytics_data', 'barcodes', 'financial_data', 'translit');
        return (
            new OzonData(
                $this->postResponse(
                    'v3/posting/fbs/list',
                    array_merge(compact('filter', 'with', 'dir', 'limit'), array_diff(compact('offset'), ['']))
                )
            )
        )->data;
    }

    /**
     * Получить информацию об отправлении по идентификатору
     *
     * @param string $posting_number string Идентификатор отправления.
     * with object (postingv3FbsPostingWithParamsExamplars) Дополнительные поля, которые нужно добавить в ответ.
     *      @param bool|null $analytics_data Передайте true, чтобы добавить в ответ данные аналитики.
     *      @param bool|null $barcodes Добавить в ответ штрихкоды отправления.
     *      @param bool|null $financial_data Передайте true, чтобы добавить в ответ финансовые данные.
     *      @param bool|null $product_exemplars Добавить в ответ данные о продуктах и их экземплярах.
     *      @param bool|null $related_postings Добавить в ответ номера связанных отправлений.
     *      Связанные отправления — те, на которое было разделено родительское отправление при сборке.
     *      @param bool|null $translit Выполнить транслитерацию возвращаемых значений.
     * @return mixed
     */
    public function getPostingFbsGet(
        string $posting_number,
        bool $analytics_data = null,
        bool $barcodes = null,
        bool $financial_data = null,
        bool $product_exemplars = null,
        bool $related_postings = null,
        bool $translit = null
    ): mixed {
        $with = compact('analytics_data', 'barcodes', 'financial_data', 'product_exemplars', 'related_postings', 'translit');
        return (
            new OzonData(
                $this->postResponse(
                    'v3/posting/fbs/get',
                    compact('posting_number', 'with')
                )
            )
        )->data;
    }

    /**
     * Получить информацию об отправлении по штрихкоду
     *
     * @param string $barcode Штрихкод отправления.
     * @return mixed
     */
    public function getPostingFbsGetByBarcode(string $barcode): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v2/posting/fbs/get-by-barcode',
                    compact('barcode')
                )
            )
        )->data;
    }

    /**
     * Список доступных стран-изготовителей
     *
     * Метод для получения списка доступных стран-изготовителей и их ISO кодов.
     *
     * @param string|null $name_search Фильтрация по строке.
     * @return mixed
     */
    public function getPostingFbsProductCountryList(string $name_search = null): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v2/posting/fbs/product/country/list',
                    array_diff(compact('name_search'), [''])
                )
            )
        )->data;
    }

    /**
     * Получить ограничения пункта приёма
     *
     * Метод для получения габаритных, весовых и прочих ограничений пункта приёма по номеру отправления.
     * Метод применим только для работы по схеме FBS.
     *
     * @param string $posting_number required Номер отправления, для которого нужно определить ограничения.
     * @return mixed
     */
    public function getPostingFbsRestrictions(string $posting_number): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/posting/fbs/restrictions',
                    compact('posting_number')
                )
            )
        )->data;
    }

    /**
     * Причины отмены отправлений
     *
     * Возвращает список причин отмены для всех отправлений.
     *
     * @return mixed
     */
    public function getPostingFbsCancelReasonList(): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v2/posting/fbs/cancel-reason/list'
                )
            )
        )->data;
    }

    /**
     * Причины отмены отправления
     *
     * Возвращает список причин отмены для конкретных отправлений.
     *
     * @param array $related_posting_numbers
     * @return mixed
     */
    public function getPostingFbsCancelReason(array $related_posting_numbers): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/posting/fbs/cancel-reason',
                    compact('related_posting_numbers')
                )
            )
        )->data;
    }

    /**
     * Список актов по отгрузкам
     *
     * Возвращает список актов по отгрузкам с возможностью отфильтровать отгрузки по периоду,
     * статусу и типу интеграции.
     *
     * filter object (v2PostingFBSActListFilter) Параметры фильтра.
     *      @param DateTime $date_from Начальная дата создания отгрузок.
     *      @param DateTime $date_to Конечная дата создания отгрузок.
     *      @param string|null $integration_type Тип интеграции со службой доставки:
     *       - ozon — доставка через Ozon логистику.
     *       - aggregator — доставка внешней службой, Ozon регистрирует заказ.
     *       - 3pl_tracking — доставка внешней службой, продавец регистрирует заказ.
     *       - non_integrated — доставка силами продавца.
     *      @param array|null $status Array of strings Список статусов отгрузок:
     *       - awaiting_registration — ожидает регистрации,
     *       - acceptance_in_progress — идёт приёмка,
     *       - awaiting_approve — ожидает подтверждения,
     *       - awaiting_packaging — ожидает упаковки,
     *       - awaiting_deliver — ожидает отгрузки,
     *       - arbitration — арбитраж,
     *       - client_arbitration — клиентский арбитраж доставки,
     *       - delivering — доставляется,
     *       - driver_pickup — у водителя,
     *       - delivered — доставлено,
     *       - cancelled — отменено,
     *       - not_accepted — не принят на сортировочном центре,
     *       - sent_by_seller – отправлено продавцом.
     * @param int $limit <int64> required Максимальное количество актов в ответе.
     * @return mixed
     */
    public function getPostingFbsActList(DateTime $date_from, DateTime $date_to, string $integration_type = null, array $status = null, int $limit = 50): mixed
    {
        $date_from = $this->formatDate($date_from, self::DT_FORMAT_DATE);
        $date_to   = $this->formatDate($date_to, self::DT_FORMAT_DATE);

        $filter = compact('date_from', 'date_to', 'integration_type', 'status');
        return (
            new OzonData(
                $this->postResponse(
                    'v2/posting/fbs/act/list',
                    compact('filter', 'limit')
                )
            )
        )->data;
    }

    /**
     * Получить информацию о возвратах FB
     * @param string|null $posting_number Номер отправления.
     * @param array|null $status Array of strings
     * @param int|null $last_id <int64> Идентификатор последнего значения на странице.
     * @param int $limit <int64> Количество значений в ответе.
     * @return mixed
     */
    public function getReturnsList(string $posting_number = null, array $status = null, int $last_id = null, int $limit = 1000): mixed
    {
        $filter = compact('posting_number', 'status');
        return (
            new OzonData(
                $this->postResponse(
                    'v1/returns/list',
                    array_merge(compact('filter', 'limit'), array_diff(compact('last_id'), ['']))
                )
            )
        )->data;
    }

    /**
     * Получить информацию о возвратах FBO (версия 3)
     *
     * Метод для получения информации о возвращённых товарах, которые продаются со склада Ozon.
     *
     * filter object (v3ReturnsCompanyFilterFbo) Фильтр.
     *      @param string|null $posting_number Номер отправления.
     *      @param array|null $status Array of strings
     *      Enums: "Created", "ReturnedToOzon", "Cancelled", "CancelledWithCompensation", "Deleted", "TechnicalReturn", "RemovedFromRms"
     * @param int|null $last_id <int64> Идентификатор последнего значения на странице.
     * Оставьте это поле пустым при выполнении первого запроса.
     * Чтобы получить следующие значения, укажите last_id из ответа предыдущего запроса.
     * @param int $limit <int64> Количество значений в ответе.
     *
     * @return mixed
     */
    public function getReturnsCompanyFboV3(string $posting_number = null, array $status = null, int $last_id = null, int $limit = 1000): mixed
    {
        $filter = compact('posting_number', 'status');
        return (
            new OzonData(
                $this->postResponse(
                    'v3/returns/company/fbo',
                    array_merge(compact('filter', 'limit'), array_diff(compact('last_id'), ['']))
                )
            )
        )->data;
    }

    /**
     * Получить информацию о возвратах FBS (версия 3)
     *
     * Метод для получения информации о возвращённых товарах, которые продаются со склада Ozon.
     *
     * filter object (v3ReturnsCompanyFilterFbo) Фильтр.
     *      @param string|null $posting_number Номер отправления.
     *      @param array|null $status Array of strings
     *      Enums: "Created", "ReturnedToOzon", "Cancelled", "CancelledWithCompensation", "Deleted", "TechnicalReturn", "RemovedFromRms"
     * @param int|null $last_id <int64> Идентификатор последнего значения на странице.
     * Оставьте это поле пустым при выполнении первого запроса.
     * Чтобы получить следующие значения, укажите last_id из ответа предыдущего запроса.
     * @param int $limit <int64> Количество значений в ответе.
     *
     * @return mixed
     */
    public function getReturnsCompanyFbsV3(string $posting_number = null, array $status = null, int $last_id = null, int $limit = 1000): mixed
    {
        $filter = compact('posting_number', 'status');
        return (
            new OzonData(
                $this->postResponse(
                    'v3/returns/company/fbs',
                    array_merge(compact('filter', 'limit'), array_diff(compact('last_id'), ['']))
                )
            )
        )->data;
    }

    /**
     * Получить информацию о возвратах FBO
     *
     * Метод для получения информации о возвращённых товарах, которые продаются со склада Ozon.
     *
     * filter object (returnsGetReturnsCompanyFboRequestFilter) Фильтр.
     *      @param string|null $posting_number Идентификатор отправления.
     *      @param array|null $status Array of strings Статус возврата:
     *       - Created — возврат создан,
     *       - ReturnedToOzon — возврат на складе Ozon.
     * @param int|null $offset <int64> Количество элементов, которое будет пропущено в ответе.
     * Например, если offset = 10, то ответ начнётся с 11-го найденного элемента.
     * @param int $limit <int64> Количество значений в ответе:
     *  - максимум — 1000,
     *  - минимум — 1.
     * @return mixed
     */
    public function getReturnsCompanyFbo(string $posting_number = null, array $status = null, int $offset = null, int $limit = 1000): mixed
    {
        $filter = compact('posting_number', 'status');
        return (
            new OzonData(
                $this->postResponse(
                    'v2/returns/company/fbo',
                    array_merge(compact('filter', 'limit'), array_diff(compact('offset'), ['']))
                )
            )
        )->data;
    }

    /**
     * Получить информацию о возвратах FBS
     *
     * Метод для получения информации о возвращённых товарах, которые продаются со склада продавца.
     *
     * filter object (returnsGetReturnsCompanyFBSRequestFilter) Фильтр.
     *      accepted_from_customer_moment Array of objects (FilterTimeRange) Время приёма возврата от поĸупателя.
     *          @param DateTime|null $accepted_from_customer_moment_time_from <date-time> Начало периода.
     *          Формат: YYYY-MM-DDTHH:mm:ss.sssZ. Пример: 2019-11-25T10:43:06.51.
     *          @param DateTime|null $accepted_from_customer_moment_time_to <date-time> Окончание периода.
     *          Формат: YYYY-MM-DDTHH:mm:ss.sssZ. Пример: 2019-11-25T10:43:06.51.
     *      last_free_waiting_day Array of objects (FilterTimeRange) Последний день бесплатного хранения.
     *          @param DateTime|null $last_free_waiting_day_time_from <date-time> Начало периода.
     *          Формат: YYYY-MM-DDTHH:mm:ss.sssZ. Пример: 2019-11-25T10:43:06.51.
     *          @param DateTime|null $last_free_waiting_day_time_to <date-time> Окончание периода.
     *          Формат: YYYY-MM-DDTHH:mm:ss.sssZ. Пример: 2019-11-25T10:43:06.51.
     *      @param int|null $order_id <int64> Идентификатор заказа.
     *      @param array|null $posting_number Array of strings Идентификатор отправления.
     *      @param string|null $product_name Название товара.
     *      @param string|null $product_offer_id Артикул товара.
     *      @param string|null $status Статус возврата:
     *       - returned_to_seller — возвращён продавцу,
     *       - waiting_for_seller — в ожидании продавца,
     *       - accepted_from_customer — принят от покупателя,
     *       - cancelled_with_compensation — отменено с компенсацией,
     *       - ready_for_shipment — готов к отправке.
     * @param int|null $offset <int64> Количество элементов, которое будет пропущено в ответе.
     * Например, если offset = 10, то ответ начнётся с 11-го найденного элемента.
     * @param int $limit <int64> Количество значений в ответе:
     *  - максимум — 1000,
     *  - минимум — 1.
     * @return mixed
     */
    public function getReturnsCompanyFbs(
        DateTime $accepted_from_customer_moment_time_from = null,
        DateTime $accepted_from_customer_moment_time_to = null,
        DateTime $last_free_waiting_day_time_from = null,
        DateTime $last_free_waiting_day_time_to = null,
        int $order_id = null,
        array $posting_number = null,
        string $product_name = null,
        string $product_offer_id = null,
        string $status = null,
        int $offset = null,
        int $limit = 1000
    ): mixed {
        $accepted_from_customer_moment_time_from = $this->formatDate($accepted_from_customer_moment_time_from);
        $accepted_from_customer_moment_time_to   = $this->formatDate($accepted_from_customer_moment_time_to);
        $last_free_waiting_day_time_from         = $this->formatDate($last_free_waiting_day_time_from);
        $last_free_waiting_day_time_to           = $this->formatDate($last_free_waiting_day_time_to);

        $accepted_from_customer_moment = ['time_from' => $accepted_from_customer_moment_time_from, 'time_to' => $accepted_from_customer_moment_time_to];
        $last_free_waiting_day         = ['time_from' => $last_free_waiting_day_time_from, 'time_to' => $last_free_waiting_day_time_to];
        $filter                        = compact('accepted_from_customer_moment', 'last_free_waiting_day', 'order_id', 'posting_number', 'product_name', 'product_offer_id', 'status');
        return (
            new OzonData(
                $this->postResponse(
                    'v2/returns/company/fbs',
                    array_merge(compact('filter', 'limit'), array_diff(compact('offset'), ['']))
                )
            )
        )->data;
    }

    /**
     * Получить список заявок на отмену rFBS
     *
     * Метод для получения списка заявок на отмену rFBS-заказов.
     *
     * filter object (GetConditionalCancellationListRequestFilters) Фильтры.
     *      @param array|null $cancellation_initiator Array of strings Фильтр по инициатору отмены:
     *       - OZON — Ozon,
     *       - SELLER — продавец,
     *       - CLIENT — покупатель,
     *       - SYSTEM — система,
     *       - DELIVERY — служба доставки.
     *      Необязательный параметр. Можно передавать несколько значений.
     *      Items Enum: "OZON" "SELLER" "CLIENT" "SYSTEM" "DELIVER"
     *      @param array|null $posting_number Фильтр по номеру отправления.
     *      Необязательный параметр. Можно передавать несколько значений.
     *      @param array|null $state Фильтр по статусу заявки на отмену:
     *       - ALL — заявки в любом статусе,
     *       - ON_APPROVAL — заявки на рассмотрении,
     *       - APPROVED — подтверждённые заявки,
     *       - REJECTED — отклонённые заявки.
     *      Enum: "ALL" "ON_APPROVAL" "APPROVED" "REJECTED"
     * Например, если offset=10, ответ начнётся с 11-го найденного элемента.
     * with object (GetConditionalCancellationListRequestWith) Дополнительная информация.
     *      @param bool|null $counters Признак, что в ответе нужно вывести счётчик заявок в разных статусах.
     * @param int|null $offset <int32> Количество элементов, которое будет пропущено в ответе.
     * @param int $limit <int32> required Количество заявок в ответе.
     * @return mixed
     */
    public function getConditionalCancellationList(
        array $cancellation_initiator = null,
        array $posting_number = null,
        array $state = null,
        bool $counters = null,
        int $offset = null,
        int $limit = 1000
    ): mixed {
        $filter = compact('cancellation_initiator', 'posting_number', 'state');
        $with   = compact('counters');
        return (
            new OzonData(
                $this->postResponse(
                    'v1/conditional-cancellation/list',
                    array_merge(compact('filter', 'with', 'limit'), array_diff(compact('offset'), ['']))
                )
            )
        )->data;
    }

    /**
     * Список чатов (версия 2)
     *
     * Возвращает информацию о чатах по указанным фильтрам.
     *
     * filter object (ChatListRequestFilter) Фильтр по чатам.
     *      @param string $chat_status Фильтр по статусу чата:
     *       - All — все чаты.
     *       - Opened — открытые чаты.
     *       - Closed — закрытые чаты.
     *      Значение по умолчанию: All.
     *      @param bool $unread_only Фильтр по чатам с непрочитанными сообщениями.
     * @param int $offset <int64> Количество элементов, которое будет пропущено в ответе.
     * Например, если offset=10, ответ начнётся с 11-го найденного элемента.
     * @param int $limit <int64> Количество значений в ответе. Значение по умолчанию — 30.
     *  - максимум — 4294967295 (uint32),
     *  - минимум — 1.
     * @return mixed
     */
    public function getChatListV2(string $chat_status = 'All', bool $unread_only = false, int $offset = null, int $limit = 30): mixed
    {
        $filter = compact('chat_status', 'unread_only');
        return (
            new OzonData(
                $this->postResponse(
                    'v2/chat/list',
                    array_merge(compact('filter', 'limit'), array_diff(compact('offset'), ['']))
                )
            )
        )->data;
    }

    /**
     * Список чатов
     *
     * Возвращает информацию о чатах с указанными идентификаторами.
     * В ответе метода могут быть чаты с last_message_id = 0 и без сообщений.
     * Это происходит, когда покупатель открыл чат с продавцом, но ничего не написал.
     *
     * @param array|null $chat_id_list Array of strings Массив с идентификаторами чатов, для которых нужно вывести информацию.
     * with object (ChatListRequestWith) Дополнительные поля, которые нужно добавить в ответ.
     *      @param bool|null $irst_unread_message_id Атрибут выдачи параметра first_unread_message_id в ответе.
     *      Если true, в ответе вы получите идентификатор первого непрочитанного сообщения в чате.
     *      @param bool|null $unread_count Атрибут выдачи параметра unread_count в ответе.
     *      Если true, в ответе вы получите количество непрочитанных сообщений в чате.
     * @param int|null $page <int32> Количество страниц в ответе.
     * @param int $page_size <int32> Количество чатов на странице.
     * Значение по умолчанию — 100. Максимальное значение — 1000.
     * @return mixed
     */
    public function getChatList(array $chat_id_list = null, bool $irst_unead_message_id = null, bool $unread_count = null, int $page = null, int $page_size = 100): mixed
    {
        $with = compact('irst_unead_message_id', 'unread_count');
        return (
            new OzonData(
                $this->postResponse(
                    'v1/chat/list',
                    array_merge(compact('chat_id_list', 'with', 'page_size'), array_diff(compact('page'), ['']))
                )
            )
        )->data;
    }

    /**
     * История чата (версия 2)
     *
     * @param string $chat_id required Идентификатор чата.
     * @param string $direction Направление сортировки сообщений:
     *  - Forward — от старых к новым.
     *  - Backward — от новых к старым.
     * Значение по умолчанию — Backward. Количество сообщений можно установить в параметре limit.
     * @param int|null $from_message_id <uint64> Идентификатор сообщения, с которого начать вывод истории чата.
     * По умолчанию — последнее видимое сообщение.
     * @param int $limit <int64> Количество сообщений в ответе. По умолчанию — 50.
     * @return mixed
     */
    public function getChatHistoryV2(string $chat_id, string $direction = 'Backward', int $from_message_id = null, int $limit = 50): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v2/chat/history',
                    array_merge(compact('chat_id', 'direction', 'limit'), array_diff(compact('from_message_id'), ['']))
                )
            )
        )->data;
    }

    /**
     * История чата
     *
     * Возвращает историю сообщений в чате. По умолчанию сообщения показываются от старого к новому.
     * Чтобы получить историю сообщений от самого нового сообщения до самого старого, используйте метод /v1/chat/updates.
     * У методов /v1/chat/history и /v1/chat/updates одинаковая структура запроса и ответа.
     *
     * @param string $chat_id Идентификатор чата.
     * @param string|null $from_message_id <uint64> Идентификатор сообщения, с которого начать вывод истории чата.
     * @param int $limit <int64> Количество сообщений в ответе.
     * @return mixed
     */
    public function getChatHistory(string $chat_id, string $from_message_id = null, int $limit = 100): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/chat/history',
                    array_merge(compact('chat_id', 'limit'), array_diff(compact('from_message_id'), ['']))
                )
            )
        )->data;
    }

    /**
     * Список накладных
     *
     * Метод для получения списка накладных по номеру заявки.
     * Пример URL с orderId = 354679434: /v1/supplier/orders/354679434/waybill_acceptance_results
     *
     * @param string $orderId <int64> required Номер заявки.
     * @return mixed
     */
    public function getSupplierOrdersWaybillAcceptanceResults(string $orderId): mixed
    {
        return (
            new OzonData(
                $this->getResponse(
                    'v1/supplier/orders/' . $orderId . '/waybill_acceptance_results',
                )
            )
        )->data;
    }

    /**
     * Результаты приёмки
     *
     * Метод для получения результатов приёмки по номеру накладной.
     * Пример URL с waybillId = 0: /v1/supplier/waybill_acceptance_results/0
     *
     * @param string $waybillId <int64> required Номер накладной.
     * @return mixed
     */
    public function getSupplierWaybillAcceptanceResults(string $waybillId): mixed
    {
        return (
            new OzonData(
                $this->getResponse(
                    'v1/supplier/waybill_acceptance_results/' . $waybillId,
                )
            )
        )->data;
    }

    /**
     * Список отчётов
     *
     * Возвращает список отчётов, которые были сформированы раньше.
     *
     * @param int|null $page <int32> Номер страницы.
     * @param int $page_size <int32> Количество значений на странице:
     *  - по умолчанию — 100,
     *  - маĸсимальное значение — 1000.
     * @param string $report_type (ReportListRequestReportType) Тип отчёта:
     *  - ALL — все отчёты,
     *  - SELLER_PRODUCTS — отчёт по товарам,
     *  - SELLER_TRANSACTIONS — отчёт по транзакциям,
     *  - SELLER_PRODUCT_PRICES — отчёт по ценам товаров,
     *  - SELLER_STOCK — отчёт об остатках товаров,
     *  - SELLER_PRODUCT_MOVEMENT — отчёт о перемещении товаров,
     *  - SELLER_RETURNS — отчёт о возвратах,
     *  - SELLER_POSTINGS — отчёт об отправлениях,
     *  - SELLER_FINANCE — отчёт о финансах.
     * Default: "ALL"
     * @return mixed
     */
    public function getReportList(int $page = null, int $page_size = 100, string $report_type = 'ALL'): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/report/list',
                    array_merge(compact('page_size', 'report_type'), array_diff(compact('page'), ['']))
                )
            )
        )->data;
    }

    /**
     * Информация об отчёте
     *
     * Возвращает информацию о созданном ранее отчёте по его идентификатору.
     *
     * @param string $code Уникальный идентификатор отчёта.
     * @return mixed
     */
    public function getReportInfo(string $code): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/report/info',
                    compact('code')
                )
            )
        )->data;
    }

    /**
     * Финансовый отчёт
     *
     * date object (financev3Period) required Период формирования отчёта.
     *      @param DateTime $from <date-time> required Дата, с ĸоторой рассчитывается отчёт.
     *      @param DateTime $to <date-time> required Дата, по ĸоторую рассчитывается отчёт.
     * @param int $page <int32> required Номер страницы, возвращаемой в запросе.
     * @param int $page_size <int32> required Количество элементов на странице.
     * @return mixed
     */
    public function getFinanceCashFlowStatementList(DateTime $from, DateTime $to, int $page = 1, int $page_size = 1000): mixed
    {
        $from = $this->formatDate($from);
        $to   = $this->formatDate($to);

        $date = compact('from', 'to');
        return (
            new OzonData(
                $this->postResponse(
                    'v1/finance/cash-flow-statement/list',
                    compact('date', 'page', 'page_size')
                )
            )
        )->data;
    }

    /**
     * Список отчётов об уценённых товарах
     *
     * @return mixed
     */
    public function getReportDiscountedList(): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/report/discounted/list'
                )
            )
        )->data;
    }

    /**
     *  Отчёт об уценённых товарах
     *
     * @return mixed
     */
    public function getReportDiscounted(): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/report/discounted/create',
                )
            )
        )->data;
    }

    /**
     * Отчёт об уценённых товарах
     *
     * Возвращает информацию о созданном ранее отчёте по его идентификатору.
     *
     * @param string $code Уникальный идентификатор отчёта.
     * @return mixed
     */
    public function getReportDiscountedInfo(string $code): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/report/discounted/info',
                    compact('code')
                )
            )
        )->data;
    }

    /**
     * Данные аналитики
     *
     * Уĸажите период и метриĸи, ĸоторые нужно посчитать — в ответе будет аналитиĸа, сгруппированная по параметру dimensions.
     *
     * @param DateTime $date_from Дата, с которой будут данные в отчёте.
     * @param DateTime $date_to Дата, по которую будут данные в отчёте.
     * @param array $metrics Array of strings (analyticsMetric) Укажите до 14 метрик.
     * Если их будет больше, вы получите ошибку с кодом InvalidArgument.
     * Список метриĸ, по ĸоторым будет сформирован отчёт:
     *  - unknown_metric — неизвестная метрика,
     *  - hits_view_search — показы в поиске и в категории,
     *  - hits_view_pdp — показы на карточке товара,
     *  - hits_view — всего показов,
     *  - hits_tocart_search — в корзину из поиска или категории,
     *  - hits_tocart_pdp — в корзину из карточки товара,
     *  - hits_tocart — всего добавлено в корзину,
     *  - session_view_search — сессии с показом в поиске или в категории,
     *  - session_view_pdp — сессии с показом на карточке товара,
     *  - session_view — всего сессий,
     *  - conv_tocart_search — конверсия в корзину из поиска или категории,
     *  - conv_tocart_pdp — конверсия в корзину из карточки товара,
     *  - conv_tocart — общая конверсия в корзину,
     *  - revenue — заказано на сумму,
     *  - returns — возвращено товаров,
     *  - cancellations — отменено товаров,
     *  - ordered_units — заказано товаров,
     *  - delivered_units — доставлено товаров,
     *  - adv_view_pdp — показы на карточке товара, спонсорские товары,
     *  - adv_view_search_category — показы в поиске и в категории, спонсорские товары,
     *  - adv_view_all — показы всего, спонсорские товары,
     *  - adv_sum_all — всего расходов на рекламу,
     *  - position_category — позиция в поиске и категории,
     *  - postings — отправления,
     *  - postings_premium — отправления с подпиской Premium.
     * @param array $dimension Array of strings (seller_serviceanalyticsDimension) Группировка данных в отчёте:
     *  - unknownDimension — неизвестное измерение,
     *  - sku — идентификатор товара,
     *  - spu — идентификатор товара,
     *  - day — день,
     *  - week — неделя,
     *  - month — месяц,
     *  - year — год,
     *  - category1 — категория первого уровня,
     *  - category2 — категория второго уровня,
     *  - category3 — категория третьего уровня,
     *  - category4 — категория четвертого уровня,
     *  - brand — бренд,
     *  - modelID — модель.
     * @param array|null $filters Array of objects (analyticsFilter) Фильтры.
     *      key string Параметр сортировки.
     *      Можно передать любой атрибут из параметров dimension и metric, кроме атрибута brand.
     *      op string (FilterOp) Операция сравнения:
     *       - EQ — равно,
     *       - GT — больше,
     *       - GTE — больше или равно,
     *       - LT — меньше,
     *       - LTE — меньше или равно.
     *      Default: "EQ"
     *      value string Значение для сравнения.
     * @param array|null $sort Array of objects (analyticsSorting) Настройки сортировки отчёта.
     *      key string Метрика, по которой будет отсортирован результат запроса.
     *      order string (SortingOrder) Вид сортировки:
     *       - ASC — по возрастанию,
     *       - DESC — по убыванию.
     *      Default: "ASC"
     * @param int|null $offset <int64> Количество элементов, которое будет пропущено в ответе.
     * Например, если offset = 10, то ответ начнётся с 11-го найденного элемента.
     * @param int $limit <int64> Количество значений в ответе:
     *  - максимум — 1000,
     *  - минимум — 1.
     * @return mixed
     */
    public function getAnalyticsData(
        DateTime $date_from,
        DateTime $date_to,
        array $metrics,
        array $dimension,
        array $filters = null,
        array $sort = null,
        int $offset = null,
        int $limit = 1000
    ): mixed {
        $date_from = $this->formatDate($date_from, self::DT_FORMAT_DATE);
        $date_to   = $this->formatDate($date_to, self::DT_FORMAT_DATE);
        $filters   = $this->wrapToArrayIfNotNull($filters);
        $sort      = $this->wrapToArrayIfNotNull($sort);

        return (
            new OzonData(
                $this->postResponse(
                    'v1/analytics/data',
                    array_merge(compact('date_from', 'date_to', 'metrics', 'dimension', 'filters', 'sort', 'limit'), array_diff(compact('offset'), ['']))
                )
            )
        )->data;
    }

    /**
     * Получить аналитику по остаткам
     *
     * Метод для поулчения аналитики по остаткам с помощью skus
     *
     * @param array $cluster_ids <array> Фильтр по идентификаторам кластеров. Получить идентификаторы можно через метод /v1/cluster/list.
     * @param array $item_tags
     * Array of strings
     * Items Enum: "ITEM_ATTRIBUTE_NONE" "ECONOM" "NOVEL" "DISCOUNT" "FBS_RETURN" "SUPER"
     * Фильтр по тегам товара:
     *
     * ITEM_ATTRIBUTE_NONE — без тега;
     * ECONOM — эконом-товар;
     * NOVEL — новинка;
     * DISCOUNT — уценённый товар;
     * FBS_RETURN — товар из возврата FBS;
     * SUPER — Super-товар.
     * @param array $skus Array of strings <int64> <= 100
     * Фильтр по идентификаторам товаров в системе Ozon — SKU.
     * @param array $turnover_grades
     * Array of strings
     * Items Enum: "TURNOVER_GRADE_NONE" "DEFICIT" "POPULAR" "ACTUAL" "SURPLUS" "NO_SALES" "WAS_NO_SALES" "RESTRICTED_NO_SALES" "COLLECTING_DATA" "WAITING_FOR_SUPPLY" "WAS_DEFICIT" "WAS_POPULAR" "WAS_ACTUAL" "WAS_SURPLUS"
     * Фильтр по статусу ликвидности товаров:
     *
     * TURNOVER_GRADE_NONE — нет статуса ликвидности.
     * DEFICIT — дефицитный. Остатков товара хватит до 28 дней.
     * POPULAR — очень популярный. Остатков товара хватит на 28–56 дней.
     * ACTUAL — популярный. Остатков товара хватит на 56–120 дней.
     * SURPLUS — избыточный. Товар продаётся медленно, остатков хватит более чем на 120 дней.
     * NO_SALES — без продаж. У товара нет продаж последние 28 дней.
     * WAS_NO_SALES — был без продаж. У товара не было продаж и остатков последние 28 дней.
     * RESTRICTED_NO_SALES — без продаж, ограничен. У товара не было продаж более 120 дней. Такой товар нельзя добавить в поставку.
     * COLLECTING_DATA — сбор данных. Для расчёта ликвидности нового товара собираем данные в течение 60 дней после поставки.
     * WAITING_FOR_SUPPLY — ожидаем поставки. На складе нет остатков, доступных к продаже. Сделайте поставку для начала сбора данных.
     * WAS_DEFICIT — был дефицитным. Товар был дефицитным последние 56 дней. Сейчас у него нет остатков.
     * WAS_POPULAR — был очень популярным. Товар был очень популярным последние 56 дней. Сейчас у него нет остатков.
     * WAS_ACTUAL — был популярным. Товар был популярным последние 56 дней. Сейчас у него нет остатков.
     * WAS_SURPLUS — был избыточным. Товар был избыточным последние 56 дней. Сейчас у него нет остатков.
     * @param array $warehouse_ids Array of strings <int64> Фильтр по идентификаторам складов. Получить идентификаторы можно через метод /v1/warehouse/list.
     * @return mixed
     **/
    public function getAnalyticsStocks(
        array $skus,
        array $cluster_ids = null,
        array $item_tags = null,
        array $turnover_grades = null,
        array $warehouse_ids = null,
    ): mixed {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/analytics/stocks',
                    compact('cluster_ids', 'item_tags', 'skus', 'turnover_grades', 'warehouse_ids')
                )
            )
        )->data;
    }


    /**
     * Получить аналитику по остаткам
     *
     * Метод для поулчения аналитики по остаткам с помощью skus
     *
     * @param array $cluster_ids <array> Фильтр по идентификаторам кластеров. Получить идентификаторы можно через метод /v1/cluster/list.
     * @param array $item_tags
     * Array of strings
     * Items Enum: "ITEM_ATTRIBUTE_NONE" "ECONOM" "NOVEL" "DISCOUNT" "FBS_RETURN" "SUPER"
     * Фильтр по тегам товара:
     *
     * ITEM_ATTRIBUTE_NONE — без тега;
     * ECONOM — эконом-товар;
     * NOVEL — новинка;
     * DISCOUNT — уценённый товар;
     * FBS_RETURN — товар из возврата FBS;
     * SUPER — Super-товар.
     * @param array $skus Array of strings <int64> <= 100
     * Фильтр по идентификаторам товаров в системе Ozon — SKU.
     * @param array $turnover_grades
     * Array of strings
     * Items Enum: "TURNOVER_GRADE_NONE" "DEFICIT" "POPULAR" "ACTUAL" "SURPLUS" "NO_SALES" "WAS_NO_SALES" "RESTRICTED_NO_SALES" "COLLECTING_DATA" "WAITING_FOR_SUPPLY" "WAS_DEFICIT" "WAS_POPULAR" "WAS_ACTUAL" "WAS_SURPLUS"
     * Фильтр по статусу ликвидности товаров:
     *
     * TURNOVER_GRADE_NONE — нет статуса ликвидности.
     * DEFICIT — дефицитный. Остатков товара хватит до 28 дней.
     * POPULAR — очень популярный. Остатков товара хватит на 28–56 дней.
     * ACTUAL — популярный. Остатков товара хватит на 56–120 дней.
     * SURPLUS — избыточный. Товар продаётся медленно, остатков хватит более чем на 120 дней.
     * NO_SALES — без продаж. У товара нет продаж последние 28 дней.
     * WAS_NO_SALES — был без продаж. У товара не было продаж и остатков последние 28 дней.
     * RESTRICTED_NO_SALES — без продаж, ограничен. У товара не было продаж более 120 дней. Такой товар нельзя добавить в поставку.
     * COLLECTING_DATA — сбор данных. Для расчёта ликвидности нового товара собираем данные в течение 60 дней после поставки.
     * WAITING_FOR_SUPPLY — ожидаем поставки. На складе нет остатков, доступных к продаже. Сделайте поставку для начала сбора данных.
     * WAS_DEFICIT — был дефицитным. Товар был дефицитным последние 56 дней. Сейчас у него нет остатков.
     * WAS_POPULAR — был очень популярным. Товар был очень популярным последние 56 дней. Сейчас у него нет остатков.
     * WAS_ACTUAL — был популярным. Товар был популярным последние 56 дней. Сейчас у него нет остатков.
     * WAS_SURPLUS — был избыточным. Товар был избыточным последние 56 дней. Сейчас у него нет остатков.
     * @param array $warehouse_ids Array of strings <int64> Фильтр по идентификаторам складов. Получить идентификаторы можно через метод /v1/warehouse/list.
     * @return mixed
     **/
    public function getAnalyticsStocks(
        array $skus,
        array $cluster_ids = null,
        array $item_tags = null,
        array $turnover_grades = null,
        array $warehouse_ids = null,
    ): mixed {
        return (
        new OzonData(
            $this->postResponse(
                'v1/analytics/stocks',
                compact('cluster_ids', 'item_tags', 'skus', 'turnover_grades', 'warehouse_ids')
            )
        )
        )->data;
    }

    /**
     * Отчёт по остаткам и товарам
     *
     * Отчёт по остаткам и товарам в перемещении по складам Ozon.
     *
     * @param int|null $offset <int64> Количество элементов, которое будет пропущено в ответе.
     * Например, если offset = 10, то ответ начнётся с 11-го найденного элемента.
     * @param int $limit <int64> Количество ответов на странице. По умолчанию — 100.
     * @return mixed
     */
    public function getAnalyticsStockOnWarehouses(int $offset = null, int $limit = 100): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/analytics/stock_on_warehouses',
                    array_merge(compact('limit'), array_diff(compact('offset'), ['']))
                )
            )
        )->data;
    }

    /**
     * Отчёт по остаткам и товарам (версия 2)
     *
     * Отчёт по остаткам и товарам в перемещении по складам Ozon.
     *
     * @param int|null $offset <int64> Количество элементов, которое будет пропущено в ответе.
     * Например, если offset = 10, то ответ начнётся с 11-го найденного элемента.
     * @param int $limit <int64> Количество ответов на странице. По умолчанию — 100.
     * @param string $warehouse_type Фильтр по типу склада
     * @return mixed
     */
    public function getAnalyticsStockOnWarehousesV2(int $offset = null, int $limit = 100, string $warehouse_type = 'ALL'): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v2/analytics/stock_on_warehouses',
                    array_merge(compact('limit', 'warehouse_type'), array_diff(compact('offset'), ['']))
                )
            )
        )->data;
    }

    /**
     * Отчёт по оборачиваемости (FBO)
     *
     * Метод для получения отчёта по оборачиваемости (FBO) по категориям за 15 дней.
     *
     * @param DateTime $date_from Дата. 1-е или 15-е число месяца в формате: 2021-05-01.
     * 1-е число месяца вводится для получения отчёта за первую половину месяца.
     * 15-е число вводится для получения отчёта за вторую половину месяца.
     * @return mixed
     */
    public function getAnalyticsItemTurnover(DateTime $date_from): mixed
    {
        $date_from = $this->formatDate($date_from, self::DT_FORMAT_DATE);

        return (
            new OzonData(
                $this->postResponse(
                    'v1/analytics/item_turnover',
                    compact('date_from')
                )
            )
        )->data;
    }

    /**
     * Отчёт о реализации товаров
     *
     * Отчёт о реализации доставленных и возвращённых товаров за месяц. Отмены и невыкупы не включаются.
     * Отчёт придёт не позднее 5-го числа следующего месяца.
     *
     * @param DateTime $date Отчётный период в формате YYYY-MM.
     * @return mixed
     */
    public function getFinanceRealization(DateTime $date): mixed
    {
        $date = $this->formatDate($date, self::DT_FORMAT_DATE_YM);

        return (
            new OzonData(
                $this->postResponse(
                    'v1/finance/realization',
                    compact('date')
                )
            )
        )->data;
    }

    /**
     * Получить финансовый отчет
     *
     * @return mixed
     * @param integer $year
     * @param integer $month
     */
    public function getRealizationV2($year, $month): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v2/finance/realization',
                    compact('year', 'month')
                )
            )
        )->data;
    }

    /**
     * Отчёт о взаимном расчете
     *
     * @param DateTime $date Отчётный период в формате YYYY-MM.
     * @return mixed
     */
    public function getSettlementReports(DateTime $date): mixed
    {
        $date = $this->formatDate($date, self::DT_FORMAT_DATE_YM);

        return (
            new OzonData(
                $this->postResponse(
                    'v1/finance/mutual-settlement',
                    compact('date')
                )
            )
        )->data;
    }

    /**
     * Список транзакций (версия 3)
     *
     * Возвращает подробную информацию по всем начислениям.
     * Максимальный период, за который можно получить информацию в одном запросе — 3 месяца.
     * Если в запросе не указывать posting_number,
     * то в ответе будут все отправления за указанный период или отправления определённого типа.
     *
     * filter object (FinanceTransactionListV3RequestFilter) Фильтр.
     *      date object (FilterPeriod) Фильтр по дате.
     *          @param DateTime|null $from <date-time> Начало периода.
     *          Формат: YYYY-MM-DDTHH:mm:ss.sssZ. Пример: 2019-11-25T10:43:06.51.
     *          @param DateTime string $to <date-time> Конец периода.
     *          Формат: YYYY-MM-DDTHH:mm:ss.sssZ. Пример: 2019-11-25T10:43:06.51.
     *      @param array|null $operation_type Array of strings Тип операции:
     *       - ClientReturnAgentOperation — получение возврата, отмены, невыкупа от покупателя;
     *       - MarketplaceMarketingActionCostOperation — услуги продвижения товаров;
     *       - MarketplaceSaleReviewsOperation — приобретение отзывов на платформе;
     *       - MarketplaceSellerCompensationOperation — прочие компенсации;
     *       - OperationAgentDeliveredToCustomer — доставка покупателю;
     *       - OperationAgentDeliveredToCustomerCanceled — доставка покупателю — исправленное начисление;
     *       - OperationAgentStornoDeliveredToCustomer — доставка покупателю — отмена начисления;
     *       - OperationClaim — начисление по претензии;
     *       - OperationCorrectionSeller — инвентаризация взаиморасчетов;
     *       - OperationDefectiveWriteOff — компенсация за повреждённый на складе товар;
     *       - OperationItemReturn — доставка и обработка возврата, отмены, невыкупа;
     *       - OperationLackWriteOff — компенсация за утерянный на складе товар;
     *       - OperationMarketplaceCrossDockServiceWriteOff — доставка товаров на склад Ozon (кросс-докинг);
     *       - OperationMarketplaceServiceStorage — услуга размещения товаров на складе;
     *       - OperationSetOff — взаимозачёт с другими договорами контрагента;
     *       - MarketplaceSellerReexposureDeliveryReturnOperation — перечисление за доставку от покупателя;
     *       - OperationReturnGoodsFBSofRMS — доставка и обработка возврата, отмены, невыкупа;
     *       - ReturnAgentOperationRFBS — возврат перечисления за доставку покупателю;
     *       - MarketplaceSellerShippingCompensationReturnOperation — компенсация перечисления за доставку;
     *       - OperationMarketplaceServicePremiumCashback — услуга продвижения Premium.
     *      @param string|null $posting_number Номер отправления.
     *      @param string|null $transaction_type Тип начисления:
     *       - all — все,
     *       - orders — заказы,
     *       - returns — возвраты и отмены,
     *       - services — сервисные сборы,
     *       - compensation — компенсация,
     *       - transferDelivery — стоимость доставки,
     *       - other — прочее.
     *      Некоторые операции могут быть разделены во времени.
     *      Например, при приёме возврата от покупателя списывается стоимость товара и возвращается комиссия,
     *      а когда товар возвращается на склад, взимается стоимость услуга по обработке возврата.
     * @param int $page <int64> Номер страницы, возвращаемой в запросе.
     * @param int $page_size <int64> Количество элементов на странице.
     * @return mixed
     */
    public function getFinanceTransactionList(
        DateTime $from,
        DateTime $to,
        array $operation_type = null,
        string $posting_number = null,
        string $transaction_type = null,
        int $page = 1,
        int $page_size = 1000
    ): mixed {
        $from = $this->formatDate($from);
        $to   = $this->formatDate($to);

        $filter = array_merge(['date' => compact('from', 'to')], compact('operation_type', 'posting_number', 'transaction_type'));
        return (
            new OzonData(
                $this->postResponse(
                    'v3/finance/transaction/list',
                    compact('filter', 'page', 'page_size')
                )
            )
        )->data;
    }

    /**
     * Суммы транзакций
     *
     * Возвращает итоговые суммы по транзакциям за указанный период.
     *
     * date object (FinanceTransactionTotalsV3RequestDate) Фильтр по дате.
     *      @param DateTime|null $from <date-time> Начало периода.
     *      Формат: YYYY-MM-DDTHH:mm:ss.sssZ. Пример: 2019-11-25T10:43:06.51.
     *      @param DateTime $to <date-time> Конец периода.
     *      Формат: YYYY-MM-DDTHH:mm:ss.sssZ. Пример: 2019-11-25T10:43:06.51.
     * @param string|null $posting_number Номер отправления.
     * @param string|null $transaction_type Тип операции:
     *  - all — все,
     *  - orders — заказы,
     *  - returns — возвраты и отмены,
     *  - services — сервисные сборы,
     *  - compensation — компенсация,
     *  - transferDelivery — стоимость доставки,
     *  - other — прочее.
     * @return mixed
     */
    public function getFinanceTransactionTotals(DateTime $from = null, DateTime $to, string $posting_number = null, string $transaction_type = null): mixed
    {
        $from = $this->formatDate($from);
        $to   = $this->formatDate($to);

        $date = compact('from', 'to');
        return (
            new OzonData(
                $this->postResponse(
                    'v3/finance/transaction/totals',
                    array_merge(compact('date'), array_diff(compact('posting_number', 'transaction_type'), ['']))
                )
            )
        )->data;
    }

    /**
     * Получить список бронирований
     *
     * Метод для получения списка бронирований.
     * Бронирования сортируются по идентификатору бронирования в порядке возрастания.
     * Для получения первой страницы списка передайте 1 в парметре booking_id_gt_or_eq.
     * Для получения следующей страницы добавьте к максимальному идентификатору бронирования текущего ответа единицу
     * и передайте это число в следующем запросе в параметре booking_id_gt_or_eq.
     *
     * @param string $booking_id_gt_or_eq <int64> required Идентификатор бронирования, с которого начинается список.
     * Минимальное значение — 1.
     * @param string $per_page <int64> required Количество бронирований на странице.
     * Минимальное значение — 1, максимальное — 100.
     * @param DateTime|null $created_at_gt_or_eq <date-time> Фильтр по времени создания бронирования — начало периода.
     * @param DateTime|null $created_at_lt_or_eq <date-time> Фильтр по времени создания бронирования — конец периода.
     * @return mixed
     */
    public function getAutoBookingsList(string $booking_id_gt_or_eq, string $per_page, DateTime $created_at_gt_or_eq = null, DateTime $created_at_lt_or_eq = null): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/auto/bookings/list',
                    array_merge(compact('booking_id_gt_or_eq', 'per_page'), array_diff(compact('created_at_gt_or_eq', 'created_at_lt_or_eq'), ['']))
                )
            )
        )->data;
    }

    /**
     * Получить информацию о бронировании
     *
     * Метод для получения информации о бронировании по его идентификатору.
     * @param int $booking_id <int64> required Идентификатор бронирования.
     * @return mixed
     */
    public function getAutoBookingsGet(int $booking_id): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/auto/bookings/get',
                    compact('booking_id')
                )
            )
        )->data;
    }

    /**
     * Получить список автосалонов
     *
     * Метод для получения списка автосалонов.
     *
     * @param int $page <int64> required Номер страницы. Минимальное значение — 1.
     * @param int $per_page <int64> required Количество автосалонов на странице.
     * Минимальное значение — 1, максимальное — 100.
     * @return mixed
     */
    public function getAutoCbosList(int $page = 1, int $per_page = 100): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/auto/cbos/list',
                    compact('page', 'per_page')
                )
            )
        )->data;
    }

    /**
     * Получить список модификаций
     *
     * Метод для получения списка модификаций. Модификации сортируются по идентификатору модификации в порядке возрастания.
     * Для получения первой страницы списка передайте 1 в парметре modification_id_gt_or_eq.
     * Для получения следующей страницы добавьте к максимальному идентификатору бронирования текущего ответа единицу
     * и передайте это число в следующем запросе в параметре modification_id_gt_or_eq.
     *
     * @param int $modification_id_gt_or_eq <int64> required Идентификатор модификации, с которого начинается список.
     * Минимальное значение — 1.
     * @param int $per_page <int64> required Количество модификаций на странице.
     * Минимальное значение — 1, максимальное — 500.
     * @return mixed
     */
    public function getAutoModificationsList(int $modification_id_gt_or_eq = 1, int $per_page = 500): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/auto/modifications/list',
                    compact('modification_id_gt_or_eq', 'per_page')
                )
            )
        )->data;
    }

    /**
     * Получить список товаров
     *
     * @param int|null $last_id
     * @param int $limit
     * @return mixed
     */
    public function getProducts(array $offer_ids = null, int $last_id = null, int $limit = 1000): mixed
    {
        $filter = compact('offer_ids');
        return (
            new OzonData(
                $this->postResponse(
                    'v3/product/list',
                    array_merge(compact('filter', 'limit'), array_diff(compact('last_id'), ['']))
                )
            )
        )->data;
    }
    /**
     * Получить список предложений
     *
     * Метод для получения списка предложений о продаже автомобилей.
     *
     * filter object (AutoOffersListV1RequestFilter) Фильтр.
     *      @param array|null $offer_ids Array of strings Идентификаторы предложений в системе автодилера.
     *      Максимальное количество идентификаторов в одном запросе — 1000.
     * @param int|null $last_id required Идентификатор последнего значения на странице.
     * Оставьте это поле пустым при выполнении первого запроса.
     * Чтобы получить следующие значения, укажите last_id из ответа предыдущего запроса.
     * @param int $limit <int64> required Количество предложений на странице.
     * Минимальное значение — 1, максимальное — 1000.
     * @return mixed
     */
    public function getAutoOffersList(array $offer_ids = null, int $last_id = null, int $limit = 1000): mixed
    {
        $filter = compact('offer_ids');
        return (
            new OzonData(
                $this->postResponse(
                    'v1/auto/offers/list',
                    array_merge(compact('filter', 'limit'), array_diff(compact('last_id'), ['']))
                )
            )
        )->data;
    }

    /**
     * Получить информацию о текущих рейтингах продавца
     *
     * @return mixed
     */
    public function getRatingSummary(): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/rating/summary'
                )
            )
        )->data;
    }

    /**
     * Получить информацию о рейтингах продавца за период
     *
     * @param DateTime $date_from <date-time> Начало периода.
     * @param DateTime $date_to <date-time> Конец периода.
     * @param array $ratings Array of strings required Фильтр по рейтингу.
     * Рейтинги, по которым нужно получить значение за период:
     *  - rating_on_time — процент заказов, выполненных вовремя за последние 30 дней.
     *  - rating_review_avg_score_total — средняя оценка всех товаров.
     *  - rating_price — индекс цен: отношение стоимости ваших товаров к самой низкой цене на такой же товар на других площадках.
     *  - rating_order_cancellation — процент отмен FBS-отправлений по вашей вине за последние 7 дней. Текущий и предыдущий день не учитываются.
     *  - rating_shipment_delay — процент FBS-отправлений, которые за последние 7 дней не были переданы в доставку по вашей вине. Текущий и предыдущий день не учитываются.
     *  - rating_ssl — оценка работы по FBO. Учитывает rating_on_time_supply_delivery, rating_on_time_supply_cancellation и rating_order_accuracy.
     *  - rating_on_time_supply_delivery — процент поставок, которые вы привезли на склад в выбранный временной интервал за последние 60 дней.
     *  - rating_order_accuracy — процент поставок без излишков, недостач, пересорта и брака за последние 60 дней.
     *  - rating_on_time_supply_cancellation — процент заявок на поставку, которые завершились или были отменены без опоздания за последние 60 дней.
     *  - rating_reaction_time — время, в течение которого покупатели в среднем ждали ответа на своё первое сообщение в чате за последние 30 дней.
     *  - rating_average_response_time — время, в течение которого покупатели в среднем ждали вашего ответа за последние 30 дней.
     *  - rating_replied_dialogs_ratio — доля диалогов хотя бы с одним вашим ответом в течение 24 часов за последние 30 дней.
     * Если вы хотите получить информацию по начисленным штрафным баллам для рейтингов rating_on_time и rating_review_avg_score_total,
     * передайте значения нужных рейтингов в этом параметре и with_premium_scores=true.
     * @param bool|null $with_premium_scores Признак, что в ответе нужно вернуть информацию о штрафных баллах в Premium-программе.
     * @return mixed
     */
    public function getRatingHistory(DateTime $date_from, DateTime $date_to, array $ratings, bool $with_premium_scores = null): mixed
    {
        $date_from = $this->formatDate($date_from);
        $date_to   = $this->formatDate($date_to);

        return (
            new OzonData(
                $this->postResponse(
                    'v1/rating/history',
                    array_merge(compact('date_from', 'date_to', 'ratings'), array_diff(compact('with_premium_scores'), ['']))
                )
            )
        )->data;
    }

    /**
     * Получить информацию о текущих рейтингах продавца
     *
     * @return mixed
     * @param int $month
     * @param int $year
     */
    public function getRealization($month, $year): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v2/finance/realization',
                    compact('month', 'year')
                )
            )
        )->data;

    }

    /**
     * Получить связанные sku
     *
     * @return mixed
     * @param array $sku
     */
    public function getSkuList(array $sku): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/product/related-sku/get',
                    compact('sku')
                )
            )
        )->data;
    }

    /**
     * Получить список заявок на поставку
     *
     * @param int $from_supply_order_id
     * @param int $limit
     * @param object|null $filter
     * @return mixed
     */
    public function getSupplyOrdersList(int $from_supply_order_id, int $limit, object $filter = null): mixed
    {
        $paging = compact('from_supply_order_id', 'limit');
        return (
            new OzonData(
                $this->postResponse(
                    'v2/supply-order/list',
                    compact('paging', 'filter')
                )
            )
        )->data;
    }

    /**
     * Получить связанные sku
     *
     * @return mixed
     * @param int $page
     * @param int $page_size
     * @param int $supply_order_id
     */
    public function getSupplyOrdersItems(int $page, int $page_size, int $supply_order_id): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/supply-order/items',
                    compact('page', 'page_size', 'supply_order_id')
                )
            )
        )->data;
    }

    /**
     * Получить информацию о заявках на поставку
     *
     * @return mixed
     * @param array $order_ids
     */
    public function getSupplyOrders(array $order_ids): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v2/supply-order/get',
                    compact('order_ids')
                )
            )
        )->data;
    }

    /**
     * Получить информацию о заявках на поставку
     *
     * @return mixed
     * @param array $order_ids
     * @param int $limit
     * @param string $last_id
     */
    public function getSupplyOrdersBundle(array $bundle_ids, int $limit, string $last_id): mixed
    {

        return (
            new OzonData(
                $this->postResponse(
                    'v1/supply-order/bundle',
                    compact('bundle_ids', 'limit', 'last_id')
                )
            )
        )->data;
    }

    /**
     * Получить кластеры
     *
     * @return mixed
     * @param string $cluster_type
     */
    public function getClusters(string $cluster_type): mixed
    {

        return (
            new OzonData(
                $this->postResponse(
                    'v1/cluster/list',
                    compact('cluster_type')
                )
            )
        )->data;
    }

    /**
     * Получить отзывы
     *
     * @return mixed
     * @param int $limit
     * @param string $last_id
     * @param string $status
     * @param string $sort_dir

     */
    public function getFeedbacks(int $limit, string $last_id = '', string $status = 'ALL', string $sort_dir = 'DESC'): mixed
    {

        return (
            new OzonData(
                $this->postResponse(
                    'v1/review/list',
                    compact('limit', 'last_id', 'status', 'sort_dir')
                )
            )
        )->data;
    }

    /**
     * Отчёт о реализации доставленных и возвращённых товаров с детализацией по каждому заказу
     *
     * @param integer $month
     * @param integer $year
     * @return mixed
     */
    public function getFinanceRealizationPosting(int $month, int $year): mixed
    {
        return (
        new OzonData(
            $this->postResponse(
                'v1/finance/realization/posting',
                compact('month', 'year')
            )
        )
        )->data;
    }

        /**
     * Отчёт о среднем времени доставки
     *
     * @param integer $cluster_id
     * @param integer $limit
     * @param integer $offset
     * @return mixed
     */
    public function getAverageDeliveryTime(int $cluster_id, int $offset = 0, int $limit = 100): mixed
    {
        return (
            new OzonData(
                $this->postResponse(
                    'v1/analytics/average-delivery-time/details',
                    compact('cluster_id', 'limit', 'offset')
                )
            )
        )->data;
    }
}
