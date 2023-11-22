<?php

declare(strict_types=1);

namespace Taxcom\Library\Helper;

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
}