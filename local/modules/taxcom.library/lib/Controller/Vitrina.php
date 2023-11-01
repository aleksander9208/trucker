<?php

declare(strict_types=1);

namespace Taxcom\Library\Controller;

use Bitrix\Main\Context;
use Bitrix\Main\Engine\AutoWire\Parameter;
use Bitrix\Main\Error;
use Bitrix\Main\UI\PageNavigation;

class Vitrina extends BaseController
{
    /**
     * @return array|Parameter[]
     */
    public function getAutoWiredParameters(): array
    {
        return array_merge(
            parent::getAutoWiredParameters(),
            [
                new Parameter(
                    PageNavigation::class,
                    static function () {
                        $pageNavigation = new PageNavigation('nav');
                        $pageNavigation
                            ->setPageSizes(range(1, 50))
                            ->setPageSize(20)
                            ->initFromUri();

                        return $pageNavigation;
                    }
                ),
            ],
        );
    }

    /**
     * Возвращаем данные по
     * перевозки
     *
     * @param int $id
     * @return array|null
     */
    public function getAction(int $id): ?array
    {
        try {
            $shipping = \Bitrix\Iblock\Elements\ElementVitrinaApiTable::getList([
                'filter' => ['ID' => $id],
                'select' => [
                    'ID',
                    'NAME',
                    'DATE_SHIPMENT_' => 'DATE_SHIPMENT',
                    'CARGO_OWNER_' => 'CARGO_OWNER',
                    'CARGO_OWNER_INN_' => 'CARGO_OWNER_INN',
                    'FORWARDER_' => 'FORWARDER',
                    'FORWARDER_INN_' => 'FORWARDER_INN',
                    'CARRIER_' => 'CARRIER',
                    'CARRIER_INN_' => 'CARRIER_INN',
                    'DEVIATION_MARKET_PRICE_' => 'DEVIATION_MARKET_PRICE',
                    'CHECKLIST_CARRIER_' => 'CHECKLIST_CARRIER',
                    'CHECKLIST_FORWARDER_' => 'CHECKLIST_FORWARDER',
                    'LINK_DOCUMENT_' => 'LINK_DOCUMENT',
                    'CONTRACT_' => 'CONTRACT',
                    'CONTRACT_CHECK_' => 'CONTRACT_CHECK',
                    'AUTOMATIC_CHECKS_' => 'AUTOMATIC_CHECKS',
                    'DOCUMENTS_' => 'DOCUMENTS',
                    'DOCUMENTS_CHECK_' => 'DOCUMENTS_CHECK',
                    'ACCOUNTING_' => 'ACCOUNTING',
                    'TRUCK_' => 'TRUCK',
                    'TRUCK_CHECK_' => 'TRUCK_CHECK',
                    'DONKEY_' => 'DONKEY',
                    'MAIN_TRAILER_' => 'MAIN_TRAILER',
                    'SECONDARY_TRAILER_' => 'SECONDARY_TRAILER',
                    'ORDER_ONE_TIME_CONTRACT_LINK_' => 'ORDER_ONE_TIME_CONTRACT_LINK',
                    'TRANSPORTATION_CONTRACT_LINK_' => 'TRANSPORTATION_CONTRACT_LINK',
                    'EXPEDITION_CONTRACT_LINK_' => 'EXPEDITION_CONTRACT_LINK',
                    'EPD_LINK_' => 'EPD_LINK',
                    'PRICES_' => 'PRICES',
                    'GEO_MONITORING_' => 'GEO_MONITORING',
                    'INVOICE_LINK_' => 'INVOICE_LINK',
                    'ACT_SERVICE_MULTIPLE_TRANSPORTATIONS_LINK_' => 'ACT_SERVICE_MULTIPLE_TRANSPORTATIONS_LINK',
                    'ACT_SERVICE_ACCEPTANCE_LINK_' => 'ACT_SERVICE_ACCEPTANCE_LINK',
                    'TRANSPORTATION_REGISTRY_LINK_' => 'TRANSPORTATION_REGISTRY_LINK',
                    'TAX_INVOICE_LINK_' => 'TAX_INVOICE_LINK',
                    'UPD_LINK_' => 'UPD_LINK',
                    'STS_LINK_' => 'STS_LINK',
                    'RENT_AGREEMENT_LINK_' => 'RENT_AGREEMENT_LINK',
                    'AGREEMENT_LEASING_COMPANY_LINK_' => 'AGREEMENT_LEASING_COMPANY_LINK',
                    'FREE_USAGE_AGREEMENT_LINK_' => 'FREE_USAGE_AGREEMENT_LINK',
                    'MARRIAGE_CERTIFICATE_LINK_' => 'MARRIAGE_CERTIFICATE_LINK',
                    'DRIVER_APPROVALS_LINK_' => 'DRIVER_APPROVALS_LINK',
                    'APPLICATION_TRANSPORTATION_LINK_' => 'APPLICATION_TRANSPORTATION_LINK',
                    'EXPEDITOR_ORDER_LINK_' => 'EXPEDITOR_ORDER_LINK',
                    'EXPEDITOR_AGENT_RECEIPT_LINK_' => 'EXPEDITOR_AGENT_RECEIPT_LINK',
                ],
            ])->fetch();

//            echo "<pre style='dis4play: none;' alt='arResult'>";
//            print_r($shipping);
//            echo "</pre>";
//
//            die('432');

            return [
                'NAME' => $shipping['NAME'],
                'DATE_VALUE' => $shipping['DATE_SHIPMENT_VALUE'],
                'DEVIATION_MARKET_PRICE_VALUE' => $shipping['DEVIATION_MARKET_PRICE_VALUE'],
                'CARGO_OWNER_VALUE' => $shipping['CARGO_OWNER_VALUE'],
                'CARGO_OWNER_INN_VALUE' => $shipping['CARGO_OWNER_INN_VALUE'],
                'CARRIER_VALUE' => $shipping['CARRIER_VALUE'],
                'CARRIER_INN_VALUE' => $shipping['CARRIER_INN_VALUE'],
                'FORWARDER_VALUE' => $shipping['FORWARDER_VALUE'],
                'FORWARDER_INN_VALUE' => $shipping['FORWARDER_INN_VALUE'],
                'LINK_DOCUMENT' => $shipping['LINK_DOCUMENT_VALUE'],

                'CHECKLIST_FORWARDER' => $shipping['CHECKLIST_FORWARDER_VALUE'],

                'CONTRACT_CHECK' => (bool) $shipping['CONTRACT_CHECK_VALUE'],
                'CONTRACT_VALUE' => $shipping['CONTRACT_VALUE'],
                'CONTRACT_LINK_VALUE' => $shipping['TRANSPORTATION_CONTRACT_LINK_VALUE'],

                'DOCUMENTS_CHECK' => (bool) $shipping['DOCUMENTS_CHECK_VALUE'],
                'DOCUMENTS_VALUE' => $shipping['DOCUMENTS_VALUE'],
                'DOCUMENTS_LINK' => $shipping['APPLICATION_TRANSPORTATION_LINK_VALUE'],

                'EPD_LINK' => $shipping['EPD_LINK_VALUE'],
                'PRICES_LINK' => $shipping['PRICES_VALUE'],
                'GEO_MONITORING_LINK' => $shipping['GEO_MONITORING_VALUE'],
                'DRIVER_APPROVALS_LINK' => $shipping['DRIVER_APPROVALS_LINK_VALUE'],

                'TRUCK_CHECK' => (bool) $shipping['TRUCK_CHECK_VALUE'],
                'TRUCK_VALUE' => $shipping['TRUCK_VALUE'],
                'RENT_AGREEMENT_LINK' => $shipping['RENT_AGREEMENT_LINK_VALUE'],
                'STS_LINK' => $shipping['STS_LINK_VALUE'],
            ];
        } catch (\Exception $e) {
            $this->addError(new Error($e->getMessage(), $e->getCode()));

            return null;
        }
    }
}