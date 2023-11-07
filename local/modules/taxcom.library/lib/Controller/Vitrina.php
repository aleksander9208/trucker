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
                    'NAME',
                    'DATE_SHIPMENT_VALUE' => 'DATE_SHIPMENT.VALUE',
                    'CARRIER_VALUE' => 'CARRIER.VALUE',
                    'CARRIER_INN_VALUE' => 'CARRIER_INN.VALUE',
                    'CARGO_OWNER_VALUE' => 'CARGO_OWNER.VALUE',
                    'CARGO_OWNER_INN_VALUE' => 'CARGO_OWNER_INN.VALUE',
                    'FORWARDER_VALUE' => 'FORWARDER.VALUE',
                    'FORWARDER_INN_VALUE' => 'FORWARDER_INN.VALUE',
                    'CONTRACT_CHECK_VALUE' => 'CONTRACT_CHECK.VALUE',
                    'CONTRACT_EXPEDITION_LINK_VALUE' => 'CONTRACT_EXPEDITION_LINK.VALUE',
                    'CONTRACT_TRANSPORTATION_LINK_VALUE' => 'CONTRACT_TRANSPORTATION_LINK.VALUE',
                    'CONTRACT_ORDER_ONE_TIME_LINK_VALUE' => 'CONTRACT_ORDER_ONE_TIME_LINK.VALUE',
                    'DOCUMENTS_CHECK_VALUE' => 'DOCUMENTS_CHECK.VALUE',
                    'DOCUMENTS_EPD_LINK_VALUE' => 'DOCUMENTS_EPD_LINK.VALUE',
                    'DOCUMENTS_EXPEDITOR_LINK_VALUE' => 'DOCUMENTS_EXPEDITOR_LINK.VALUE',
                    'DOCUMENTS_EXPEDITOR_RECEIPT_LINK_VALUE' => 'DOCUMENTS_EXPEDITOR_RECEIPT_LINK.VALUE',
                    'DOCUMENTS_DRIVER_APPROVALS_LINK_VALUE' => 'DOCUMENTS_DRIVER_APPROVALS_LINK.VALUE',
                    'DOCUMENTS_APPLICATION_TRANSPORTATION_LINK_VALUE' => 'DOCUMENTS_APPLICATION_TRANSPORTATION_LINK.VALUE',
                    'AUTOMATIC_CHECKS_VALUE' => 'AUTOMATIC_CHECKS.VALUE',
                    'AUTOMATIC_PRICES_VALUE' => 'AUTOMATIC_PRICES.VALUE',
                    'AUTOMATIC_GEO_MONITORING_VALUE' => 'AUTOMATIC_GEO_MONITORING.VALUE',
                    'ACCOUNTING_CHECKS_VALUE' => 'ACCOUNTING_CHECKS.VALUE',
                    'ACCOUNTING_INVOICE_LINK_VALUE' => 'ACCOUNTING_INVOICE_LINK.VALUE',
                    'ACCOUNTING_ACT_ACCEPTANCE_LINK_VALUE' => 'ACCOUNTING_ACT_ACCEPTANCE_LINK.VALUE',
                    'ACCOUNTING_ACT_MULTI_TRANSPORT_LINK_VALUE' => 'ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK.VALUE',
                    'ACCOUNTING_TRANSPORTATION_REGISTRY_LINK_VALUE' => 'ACCOUNTING_TRANSPORTATION_REGISTRY_LINK.VALUE',
                    'ACCOUNTING_TAX_INVOICE_LINK_VALUE' => 'ACCOUNTING_TAX_INVOICE_LINK.VALUE',
                    'ACCOUNTING_UPD_LINK_VALUE' => 'ACCOUNTING_UPD_LINK.VALUE',
                    'DONKEY_CHECKS_VALUE' => 'DONKEY_CHECKS.VALUE',
                    'DONKEY_LICENSE_PLATE_VALUE' => 'DONKEY_LICENSE_PLATE.VALUE',
                    'DONKEY_STS_LINK_VALUE' => 'DONKEY_STS_LINK.VALUE',
                    'TRAILER_CHECKS_VALUE' => 'TRAILER_CHECKS.VALUE',
                    'TRAILER_LICENSE_PLATE_VALUE' => 'TRAILER_LICENSE_PLATE.VALUE',
                    'TRAILER_STS_LINK_VALUE' => 'TRAILER_STS_LINK.VALUE',
                    'TRAILER_RENT_AGREEMENT_LINK_VALUE' => 'TRAILER_RENT_AGREEMENT_LINK.VALUE',
                    'TRAILER_SECONDARY_CHECKS_VALUE' => 'TRAILER_SECONDARY_CHECKS.VALUE',
                    'TRAILER_SECONDARY_LICENSE_PLATE_VALUE' => 'TRAILER_SECONDARY_LICENSE_PLATE.VALUE',
                    'TRAILER_SECONDARY_STS_LINK_VALUE' => 'TRAILER_SECONDARY_STS_LINK.VALUE',
                    'TRAILER_SECONDARY_RENT_AGREEMENT_LINK_VALUE' => 'TRAILER_SECONDARY_RENT_AGREEMENT_LINK.VALUE',
                    'TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK_VALUE' => 'TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK.VALUE',
                    'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK_VALUE' => 'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK.VALUE',
                    'TRAILER_SECONDARY_FREE_USAGE_LINK_VALUE' => 'TRAILER_SECONDARY_FREE_USAGE_LINK.VALUE',
                    'TRUCK_CHECKS_VALUE' => 'TRUCK_CHECKS.VALUE',
                    'TRUCK_LICENSE_PLATE_VALUE' => 'TRUCK_LICENSE_PLATE.VALUE',
                    'TRUCK_STS_LINK_VALUE' => 'TRUCK_STS_LINK.VALUE',
                    'TRUCK_RENT_AGREEMENT_LINK_VALUE' => 'TRUCK_RENT_AGREEMENT_LINK.VALUE',
                    'TRUCK_AGREEMENT_LEASING_COMPANY_LINK_VALUE' => 'TRUCK_AGREEMENT_LEASING_COMPANY_LINK.VALUE',
                    'TRUCK_MARRIAGE_CERTIFICATE_LINK_VALUE' => 'TRUCK_MARRIAGE_CERTIFICATE_LINK.VALUE',
                    'TRUCK_FREE_USAGE_LINK_VALUE' => 'TRUCK_FREE_USAGE_LINK.VALUE',
                    'CHECKLIST_CARRIER_VALUE' => 'CHECKLIST_CARRIER.VALUE',
                    'CHECKLIST_FORWARDER_VALUE' => 'CHECKLIST_FORWARDER.VALUE',
                ],
            ])->fetch();

            $errorCont = false;
//            $contCheck = null;
            $contCheck = $shipping['CONTRACT_CHECK_VALUE'];

//            if (str_contains('/', $shipping['CONTRACT_CHECK_VALUE'])) {
//                $errorCont = true;
//                $contCheck = $shipping['CONTRACT_CHECK_VALUE'];
//            } else {
//                $count = round((int)$shipping['CONTRACT_CHECK_VALUE']);
//                if($count != 0) {
//                    $contCheck = $count . '/' . $count;
//                }
//            }

            return [
                'NAME' => $shipping['NAME'],
                'DATE' => $shipping['DATE_SHIPMENT_VALUE'],
                'CARGO' => $shipping['CARGO_OWNER_VALUE'],
                'CARGO_INN' => $shipping['CARGO_OWNER_INN_VALUE'],
                'CARRIER' => $shipping['CARRIER_VALUE'],
                'CARRIER_INN' => $shipping['CARRIER_INN_VALUE'],
                'FORWARDER' => $shipping['FORWARDER_VALUE'],
                'FORWARDER_INN' => $shipping['FORWARDER_INN_VALUE'],
                'CONT_CHECK' => $shipping['CONTRACT_CHECK_VALUE'],
                'CONT_CHECK_ERROR' => $errorCont,
                'CONT_TRANSPORT_LINK' => $shipping['CONTRACT_TRANSPORTATION_LINK_VALUE'],
                'CONT_EXP_LINK' => $shipping['CONTRACT_EXPEDITION_LINK_VALUE'],
                'CONT_ORDER_ONE_TIME' => $shipping['CONTRACT_ORDER_ONE_TIME_LINK_VALUE'],
                'DOC_CHECK' => $shipping['DOCUMENTS_CHECK_VALUE'],
                'DOC_CHECK_ERROR' => $errorCont,
                'DOC_APP_TRANSPORT_LINK' => $shipping['DOCUMENTS_APPLICATION_TRANSPORTATION_LINK_VALUE'],
                'DOC_EPD_LINK' => $shipping['DOCUMENTS_EPD_LINK_VALUE'],
                'DOC_DRIVER_APP_LINK' => $shipping['DOCUMENTS_DRIVER_APPROVALS_LINK_VALUE'],
                'DOC_EXP_LINK' => $shipping['DOCUMENTS_EXPEDITOR_LINK_VALUE'],
                'DOC_EXP_RECEIPT_LINK' => $shipping['DOCUMENTS_EXPEDITOR_RECEIPT_LINK_VALUE'],
                'AUTO_CHECKS' => $shipping['AUTOMATIC_CHECKS_VALUE'],
                'AUTO_CHECK_ERROR' => $errorCont,
                'AUTO_PRICES' => $shipping['AUTOMATIC_PRICES_VALUE'],
                'AUTO_GEO' => $shipping['AUTOMATIC_GEO_MONITORING_VALUE'],
                'ACC_CHECKS' => $shipping['ACCOUNTING_CHECKS_VALUE'],
                'ACC_CHECKS_ERROR' => $errorCont,
                'ACC_INVOICE_LINK' => $shipping['ACCOUNTING_INVOICE_LINK_VALUE'],
                'ACC_ACT_ACC_LINK' => $shipping['ACCOUNTING_ACT_ACCEPTANCE_LINK_VALUE'],
                'ACC_ACT_MULTI_TRANSPORT_LINK_VALUE' => $shipping['ACCOUNTING_ACT_MULTI_TRANSPORT_LINK_VALUE'],
                'ACC_TRANSPORT_REG_LINK' => $shipping['ACCOUNTING_TRANSPORTATION_REGISTRY_LINK_VALUE'],
                'ACC_TAX_INVOICE_LINK' => $shipping['ACCOUNTING_TAX_INVOICE_LINK_VALUE'],
                'ACC_UPD_LINK' => $shipping['ACCOUNTING_UPD_LINK_VALUE'],
                'DONKEY_CHECKS' => $shipping['DONKEY_CHECKS_VALUE'],
                'DONKEY_CHECKS_ERROR' => $errorCont,
                'DONKEY_LIC_PLATE' => $shipping['DONKEY_LICENSE_PLATE_VALUE'],
                'DONKEY_STS_LINK' => $shipping['DONKEY_STS_LINK_VALUE'],
                'TRAILER_CHECKS' => $shipping['TRAILER_CHECKS_VALUE'],
                'TRAILER_CHECKS_ERROR' => $errorCont,
                'TRAILER_LIC_PLATE' => $shipping['TRAILER_LICENSE_PLATE_VALUE'],
                'TRAILER_STS_LINK' => $shipping['TRAILER_STS_LINK_VALUE'],
                'TRAILER_RENT_AGR_LINK' => $shipping['TRAILER_RENT_AGREEMENT_LINK_VALUE'],
                'TRAILER_SEC_CHECKS' => $shipping['TRAILER_SECONDARY_CHECKS_VALUE'],
                'TRAILER_SEC_CHECKS_ERROR' => $errorCont,
                'TRAILER_SEC_LIC_PLATE' => $shipping['TRAILER_SECONDARY_LICENSE_PLATE_VALUE'],
                'TRAILER_SEC_STS_LINK' => $shipping['TRAILER_SECONDARY_STS_LINK_VALUE'],
                'TRAILER_SEC_RENT_LINK' => $shipping['TRAILER_SECONDARY_RENT_AGREEMENT_LINK_VALUE'],
                'TRAILER_SEC_LEASING_COMPANY_LINK' => $shipping['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK_VALUE'],
                'TRAILER_SEC_CERTIFICATE_LINK' => $shipping['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK_VALUE'],
                'TRAILER_SEC_FREE_USAGE_LINK' => $shipping['TRAILER_SECONDARY_FREE_USAGE_LINK_VALUE'],
                'TRUCK_CHECKS' => $shipping['TRUCK_CHECKS_VALUE'],
                'TRUCK_CHECKS_ERROR' => $errorCont,
                'TRUCK_LIC_PLATE' => $shipping['TRUCK_LICENSE_PLATE_VALUE'],
                'TRUCK_STS_LINK' => $shipping['TRUCK_STS_LINK_VALUE'],
                'TRUCK_RENT_LINK' => $shipping['TRUCK_RENT_AGREEMENT_LINK_VALUE'],
                'TRUCK_LEASING_COMPANY_LINK' => $shipping['TRUCK_AGREEMENT_LEASING_COMPANY_LINK_VALUE'],
                'TRUCK_CERTIFICATE_LINK' => $shipping['TRUCK_MARRIAGE_CERTIFICATE_LINK_VALUE'],
                'TRUCK_FREE_USAGE_LINK' => $shipping['TRUCK_FREE_USAGE_LINK_VALUE'],
                'CHECKLIST_CARRIER' => $shipping['CHECKLIST_CARRIER_VALUE'],
                'CHECKLIST_FORWARDER' => $shipping['CHECKLIST_FORWARDER_VALUE'],
            ];
        } catch (\Exception $e) {
            $this->addError(new Error($e->getMessage(), $e->getCode()));

            return null;
        }
    }

    /**
     * Возвращаем архив документов
     *
     * @param int $id
     * @return void
     */
    public function getFileArchivAction(int $id)
    {

    }
}