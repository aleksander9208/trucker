<?php

declare(strict_types=1);

namespace Taxcom\Library\Helper;

use Bitrix\Main\Data\Cache;
use Taxcom\Library\HLBlock\HLBlock;

/**
 * Хелпер для работы с
 * инфоблоком витрины
 */
class Vitrina
{
    /**
     * Возвращаем количество элементов
     * для топа по фильтру
     *
     * @param array $filter
     * @return int
     */
    public static function getCountElement(array $filter): int
    {
        $vitrina = \Bitrix\Iblock\Elements\ElementVitrinaApiTable::getList([
            'filter' => $filter,
            'select' => [
                'ID',
                'NAME',
                'DATE_SHIPMENT_VALUE' => 'DATE_SHIPMENT.VALUE',
                'STATUS_SHIPPING_VALUE' => 'STATUS_SHIPPING.VALUE',
                'CARRIER_VALUE' => 'CARRIER.VALUE',
                'CARRIER_INN_VALUE' => 'CARRIER_INN.VALUE',
                'CARGO_OWNER_VALUE' => 'CARGO_OWNER.VALUE',
                'CARGO_OWNER_INN_VALUE' => 'CARGO_OWNER_INN.VALUE',
                'FORWARDER_VALUE' => 'FORWARDER.VALUE',
                'FORWARDER_INN_VALUE' => 'FORWARDER_INN.VALUE',
                'CHECKLIST_CARRIER_VALUE' => 'CHECKLIST_CARRIER.VALUE',
                'CHECKLIST_FORWARDER_VALUE' => 'CHECKLIST_FORWARDER.VALUE',
                'AUTOMATIC_PRICES_STATUS_VALUE' => 'AUTOMATIC_PRICES_STATUS.VALUE',
                'AUTOMATIC_GEO_MONITORING_STATUS_VALUE' => 'AUTOMATIC_GEO_MONITORING_STATUS.VALUE',
                'AUTOMATIC_PRICES_FOR_STATUS_VALUE' => 'AUTOMATIC_PRICES_FOR_STATUS.VALUE',
                'AUTOMATIC_GEO_MONITORING_FOR_STATUS_VALUE' => 'AUTOMATIC_GEO_MONITORING_FOR_STATUS.VALUE',
            ],
            "order" => ['ID' => 'ASC'],
            "count_total" => true,
        ]);

        return $vitrina->getCount();
    }

    /**
     * Возвращаем данные элемента
     *
     * @param int $id
     * @return array
     */
    public static function getElement(int $id): array
    {
        $vitrina = \Bitrix\Iblock\Elements\ElementVitrinaApiTable::getList([
            'filter' => ['ID' => $id],
            'select' => [
                'NAME',
                'PREVIEW_TEXT',
                'DETAIL_TEXT',
            ],
        ]);

        return $vitrina->fetch();
    }

    /**
     * Расчет статистики
     *
     * @return void
     * @throws LoaderException
     */
    public static function getPercent(): array
    {
        $cache = Cache::createInstance(); // Служба кеширования

        if ($cache->initCache(86400, 'percent_statistics', 'percent'))
        {
            $result = $cache->getVars();
            $cache->output();
        } elseif ($cache->startDataCache()) {
            $vitrina = \Bitrix\Iblock\Elements\ElementVitrinaApiTable::getList([
                'filter' => ['!STATUS_SHIPPING.VALUE' => 'archived'],
                'select' => [
                    'ID',
                    'NAME',
                    'STATUS_SHIPPING_VALUE' => 'STATUS_SHIPPING.VALUE',
                    'CHECKLIST_CARRIER_VALUE' => 'CHECKLIST_CARRIER.VALUE',
                    'CHECKLIST_FORWARDER_VALUE' => 'CHECKLIST_FORWARDER.VALUE',
                    'AUTOMATIC_PRICES_STATUS_VALUE' => 'AUTOMATIC_PRICES_STATUS.VALUE',
                    'AUTOMATIC_GEO_MONITORING_STATUS_VALUE' => 'AUTOMATIC_GEO_MONITORING_STATUS.VALUE',
                    'AUTOMATIC_PRICES_FOR_STATUS_VALUE' => 'AUTOMATIC_PRICES_FOR_STATUS.VALUE',
                    'AUTOMATIC_GEO_MONITORING_FOR_STATUS_VALUE' => 'AUTOMATIC_GEO_MONITORING_FOR_STATUS.VALUE',
                ],
                "order" => ['ID' => 'ASC'],
                "count_total" => true,
            ]);

            $result['COUNT'] = $vitrina->getCount();

            $error = $good = $geo = $price = $doc = 0;
            foreach ($vitrina->fetchAll() as $item) {
                if (($item['CHECKLIST_CARRIER_VALUE'] === '1' &&
                        $item['CHECKLIST_FORWARDER_VALUE'] === '1') ||
                    ($item['CHECKLIST_CARRIER_VALUE'] === '1' &&
                        $item['CHECKLIST_FORWARDER_VALUE'] == '') ||
                    ($item['CHECKLIST_CARRIER_VALUE'] === '' &&
                        $item['CHECKLIST_FORWARDER_VALUE'] == '1')
                ) {
                    $good++;
                }

                if ($item['CHECKLIST_CARRIER_VALUE'] !== '1' &&
                    $item['CHECKLIST_FORWARDER_VALUE'] !== '1'
                ) {
                    $error++;
                }

                if (HLBlock::isDocument((int) $item['ID'])) {
                    $doc++;
                }

                if ($item['AUTOMATIC_GEO_MONITORING_STATUS_VALUE'] === 'failed' ||
                    $item['AUTOMATIC_GEO_MONITORING_FOR_STATUS_VALUE'] === 'failed' ||
                    $item['AUTOMATIC_GEO_MONITORING_STATUS_VALUE'] === 'in_progress' ||
                    $item['AUTOMATIC_GEO_MONITORING_FOR_STATUS_VALUE'] === 'in_progress'
                ) {
                    $geo++;
                }

                if ($item['AUTOMATIC_PRICES_STATUS_VALUE'] === 'failed' ||
                    $item['AUTOMATIC_PRICES_FOR_STATUS_VALUE'] === 'failed' ||
                    $item['AUTOMATIC_PRICES_STATUS_VALUE'] === 'in_progress' ||
                    $item['AUTOMATIC_PRICES_FOR_STATUS_VALUE'] === 'in_progress'
                ) {
                    $price++;
                }
            }

            $result['COUNT_ERROR'] = $error;
            $result['COUNT_GOOD'] = $good;
            $result['COUNT_ERROR_DOC'] = $doc;
            $result['COUNT_ERROR_GEO'] = $geo;
            $result['COUNT_ERROR_PRICE'] = $price;

            if ($result['COUNT'] > 0) {
                $result['COUNT_GOOD_PERCENT'] =  round($result['COUNT_GOOD']/$result['COUNT'] * 100, 2);
                $result['COUNT_ERROR_PERCENT'] = round($result['COUNT_ERROR']/$result['COUNT'] * 100, 2);
            }

            $result['rand'] = random_int(0, 9999);

            $cacheInvalid = false;
            if ($cacheInvalid) {
                $cache->abortDataCache();
            }

            $cache->endDataCache($result);
        }

        return $result;
    }
}