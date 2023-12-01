<?php

declare(strict_types=1);

namespace Taxcom\Library\Controller;

use Bitrix\Main\Application;
use Bitrix\Main\Engine\AutoWire\Parameter;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\IO;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\MimeType;
use CBXArchive;
use Taxcom\Library\HLBlock\HLBlock;

/**
 * Контроллер для работы с витриной
 */
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
                    'STATUS_SHIPPING_VALUE' => 'STATUS_SHIPPING.VALUE',
                    'CARRIER_VALUE' => 'CARRIER.VALUE',
                    'CARRIER_INN_VALUE' => 'CARRIER_INN.VALUE',
                    'CARGO_OWNER_VALUE' => 'CARGO_OWNER.VALUE',
                    'CARGO_OWNER_INN_VALUE' => 'CARGO_OWNER_INN.VALUE',
                    'FORWARDER_VALUE' => 'FORWARDER.VALUE',
                    'FORWARDER_INN_VALUE' => 'FORWARDER_INN.VALUE',
                    'CHECKLIST_CARRIER_VALUE' => 'CHECKLIST_CARRIER.VALUE',
                    'CHECKLIST_FORWARDER_VALUE' => 'CHECKLIST_FORWARDER.VALUE',
                    'CONTRACT_CHECK_VALUE' => 'CONTRACT_CHECK.VALUE',
                    'CONTRACT_EXPEDITION_STATUS_VALUE' => 'CONTRACT_EXPEDITION_STATUS.VALUE',
                    'CONTRACT_TRANSPORTATION_STATUS_VALUE' => 'CONTRACT_TRANSPORTATION_STATUS.VALUE',
                    'CONTRACT_ORDER_ONE_TIME_STATUS_VALUE' => 'CONTRACT_ORDER_ONE_TIME_STATUS.VALUE',
                    'DOCUMENTS_CHECK_VALUE' => 'DOCUMENTS_CHECK.VALUE',
                    'DOCUMENTS_EPD_STATUS_VALUE' => 'DOCUMENTS_EPD_STATUS.VALUE',
                    'DOCUMENTS_EXPEDITOR_STATUS_VALUE' => 'DOCUMENTS_EXPEDITOR_STATUS.VALUE',
                    'DOCUMENTS_EXPEDITOR_RECEIPT_STATUS_VALUE' => 'DOCUMENTS_EXPEDITOR_RECEIPT_STATUS.VALUE',
                    'DOCUMENTS_DRIVER_APPROVALS_STATUS_VALUE' => 'DOCUMENTS_DRIVER_APPROVALS_STATUS.VALUE',
                    'DOCUMENTS_APPLICATION_TRANSPORTATION_STATUS_VALUE' => 'DOCUMENTS_APPLICATION_TRANSPORTATION_STATUS.VALUE',
                    'AUTOMATIC_CHECKS_VALUE' => 'AUTOMATIC_CHECKS.VALUE',
                    'AUTOMATIC_PRICES_STATUS_VALUE' => 'AUTOMATIC_PRICES_STATUS.VALUE',
                    'AUTOMATIC_GEO_MONITORING_STATUS_VALUE' => 'AUTOMATIC_GEO_MONITORING_STATUS.VALUE',
                    'ACCOUNTING_CHECKS_VALUE' => 'ACCOUNTING_CHECKS.VALUE',
                    'ACCOUNTING_INVOICE_STATUS_VALUE' => 'ACCOUNTING_INVOICE_STATUS.VALUE',
                    'ACCOUNTING_ACT_ACCEPTANCE_STATUS_VALUE' => 'ACCOUNTING_ACT_ACCEPTANCE_STATUS.VALUE',
                    'ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS_VALUE' => 'ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS.VALUE',
                    'ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS_VALUE' => 'ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS.VALUE',
                    'ACCOUNTING_TAX_INVOICE_STATUS_VALUE' => 'ACCOUNTING_TAX_INVOICE_STATUS.VALUE',
                    'ACCOUNTING_UPD_STATUS_VALUE' => 'ACCOUNTING_UPD_STATUS.VALUE',
                    'DONKEY_CHECKS_VALUE' => 'DONKEY_CHECKS.VALUE',
                    'DONKEY_LICENSE_PLATE_VALUE' => 'DONKEY_LICENSE_PLATE.VALUE',
                    'DONKEY_STS_STATUS_VALUE' => 'DONKEY_STS_STATUS.VALUE',
                    'DONKEY_RENT_AGREEMENT_STATUS_VALUE' => 'DONKEY_RENT_AGREEMENT_STATUS.VALUE',
                    'TRAILER_CHECKS_VALUE' => 'TRAILER_CHECKS.VALUE',
                    'TRAILER_LICENSE_PLATE_VALUE' => 'TRAILER_LICENSE_PLATE.VALUE',
                    'TRAILER_STS_STATUS_VALUE' => 'TRAILER_STS_STATUS.VALUE',
                    'TRAILER_RENT_AGREEMENT_STATUS_VALUE' => 'TRAILER_RENT_AGREEMENT_STATUS.VALUE',
                    'TRAILER_SECONDARY_CHECKS_VALUE' => 'TRAILER_SECONDARY_CHECKS.VALUE',
                    'TRAILER_SECONDARY_LICENSE_PLATE_VALUE' => 'TRAILER_SECONDARY_LICENSE_PLATE.VALUE',
                    'TRAILER_SECONDARY_STS_STATUS_VALUE' => 'TRAILER_SECONDARY_STS_STATUS.VALUE',
                    'TRAILER_SECONDARY_RENT_AGREEMENT_STATUS_VALUE' => 'TRAILER_SECONDARY_RENT_AGREEMENT_STATUS.VALUE',
                    'TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_STATUS_VALUE' => 'TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_STATUS.VALUE',
                    'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_STATUS_VALUE' => 'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_STATUS.VALUE',
                    'TRAILER_SECONDARY_FREE_USAGE_STATUS_VALUE' => 'TRAILER_SECONDARY_FREE_USAGE_STATUS.VALUE',
                    'TRUCK_CHECKS_VALUE' => 'TRUCK_CHECKS.VALUE',
                    'TRUCK_LICENSE_PLATE_VALUE' => 'TRUCK_LICENSE_PLATE.VALUE',
                    'TRUCK_STS_STATUS_VALUE' => 'TRUCK_STS_STATUS.VALUE',
                    'TRUCK_RENT_AGREEMENT_STATUS_VALUE' => 'TRUCK_RENT_AGREEMENT_STATUS.VALUE',
                    'TRUCK_AGREEMENT_LEASING_COMPANY_STATUS_VALUE' => 'TRUCK_AGREEMENT_LEASING_COMPANY_STATUS.VALUE',
                    'TRUCK_MARRIAGE_CERTIFICATE_STATUS_VALUE' => 'TRUCK_MARRIAGE_CERTIFICATE_STATUS.VALUE',
                    'TRUCK_FREE_USAGE_STATUS_VALUE' => 'TRUCK_FREE_USAGE_STATUS.VALUE',
                    'CONTRACT_FOR_CHECK_VALUE' => 'CONTRACT_FOR_CHECK.VALUE',
                    'DOCUMENTS_FOR_CHECK_VALUE' => 'DOCUMENTS_FOR_CHECK.VALUE',
                    'AUTOMATIC_FOR_CHECKS_VALUE' => 'AUTOMATIC_FOR_CHECKS.VALUE',
                    'ACCOUNTING_FOR_CHECKS_VALUE' => 'ACCOUNTING_FOR_CHECKS.VALUE',
                    'DONKEY_FOR_CHECKS_VALUE' => 'DONKEY_FOR_CHECKS.VALUE',
                    'TRAILER_FOR_CHECKS_VALUE' => 'TRAILER_FOR_CHECKS.VALUE',
                    'TRAILER_SECONDARY_FOR_CHECKS_VALUE' => 'TRAILER_SECONDARY_FOR_CHECKS.VALUE',
                    'TRUCK_FOR_CHECKS_VALUE' => 'TRUCK_FOR_CHECKS.VALUE',
                    'CONTRACT_EXPEDITION_FOR_STATUS_VALUE' => 'CONTRACT_EXPEDITION_FOR_STATUS.VALUE',
                    'CONTRACT_TRANSPORTATION_FOR_STATUS_VALUE' => 'CONTRACT_TRANSPORTATION_FOR_STATUS.VALUE',
                    'CONTRACT_ORDER_ONE_TIME_FOR_STATUS_VALUE' => 'CONTRACT_ORDER_ONE_TIME_FOR_STATUS.VALUE',
                    'DOCUMENTS_EPD_FOR_STATUS_VALUE' => 'DOCUMENTS_EPD_FOR_STATUS.VALUE',
                    'DOCUMENTS_EXPEDITOR_FOR_STATUS_VALUE' => 'DOCUMENTS_EXPEDITOR_FOR_STATUS.VALUE',
                    'DOCUMENTS_EXPEDITOR_RECEIPT_FOR_STATUS_VALUE' => 'DOCUMENTS_EXPEDITOR_RECEIPT_FOR_STATUS.VALUE',
                    'DOCUMENTS_DRIVER_APPROVALS_FOR_STATUS_VALUE' => 'DOCUMENTS_DRIVER_APPROVALS_FOR_STATUS.VALUE',
                    'DOCUMENTS_APPLICATION_TRANSPORT_FOR_STATUS_VALUE' => 'DOCUMENTS_APPLICATION_TRANSPORT_FOR_STATUS.VALUE',
                    'AUTOMATIC_PRICES_FOR_STATUS_VALUE' => 'AUTOMATIC_PRICES_FOR_STATUS.VALUE',
                    'AUTOMATIC_GEO_MONITORING_FOR_STATUS_VALUE' => 'AUTOMATIC_GEO_MONITORING_FOR_STATUS.VALUE',
                    'ACCOUNTING_INVOICE_FOR_STATUS_VALUE' => 'ACCOUNTING_INVOICE_FOR_STATUS.VALUE',
                    'ACCOUNTING_ACT_ACCEPTANCE_FOR_STATUS_VALUE' => 'ACCOUNTING_ACT_ACCEPTANCE_FOR_STATUS.VALUE',
                    'ACCOUNTING_ACT_MULTIPLE_TRANSPORT_FOR_STATUS_VALUE' => 'ACCOUNTING_ACT_MULTIPLE_TRANSPORT_FOR_STATUS.VALUE',
                    'ACCOUNTING_TRANSPORTATION_REGISTRY_FOR_STATUS_VALUE' => 'ACCOUNTING_TRANSPORTATION_REGISTRY_FOR_STATUS.VALUE',
                    'ACCOUNTING_TAX_INVOICE_FOR_STATUS_VALUE' => 'ACCOUNTING_TAX_INVOICE_FOR_STATUS.VALUE',
                    'ACCOUNTING_UPD_FOR_STATUS_VALUE' => 'ACCOUNTING_UPD_FOR_STATUS.VALUE',
                    'DONKEY_STS_FOR_STATUS_VALUE' => 'DONKEY_STS_FOR_STATUS.VALUE',
                    'DONKEY_RENT_FOR_STATUS_VALUE' => 'DONKEY_RENT_FOR_STATUS.VALUE',
                    'TRAILER_STS_FOR_STATUS_VALUE' => 'TRAILER_STS_FOR_STATUS.VALUE',
                    'TRAILER_RENT_AGREEMENT_FOR_STATUS_VALUE' => 'TRAILER_RENT_AGREEMENT_FOR_STATUS.VALUE',
                    'TRAILER_SECONDARY_STS_FOR_STATUS_VALUE' => 'TRAILER_SECONDARY_STS_FOR_STATUS.VALUE',
                    'TRAILER_SECONDARY_RENT_AGREEMENT_FOR_STATUS_VALUE' => 'TRAILER_SECONDARY_RENT_AGREEMENT_FOR_STATUS.VALUE',
                    'TRAILER_SECONDARY_LEASING_COMPANY_FOR_STATUS_VALUE' => 'TRAILER_SECONDARY_LEASING_COMPANY_FOR_STATUS.VALUE',
                    'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_FOR_STATUS_VALUE' => 'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_FOR_STATUS.VALUE',
                    'TRAILER_SECONDARY_FREE_USAGE_FOR_STATUS_VALUE' => 'TRAILER_SECONDARY_FREE_USAGE_FOR_STATUS.VALUE',
                    'TRUCK_STS_FOR_STATUS_VALUE' => 'TRUCK_STS_FOR_STATUS.VALUE',
                    'TRUCK_RENT_AGREEMENT_FOR_STATUS_VALUE' => 'TRUCK_RENT_AGREEMENT_FOR_STATUS.VALUE',
                    'TRUCK_AGREEMENT_LEASING_COMPANY_FOR_STATUS_VALUE' => 'TRUCK_AGREEMENT_LEASING_COMPANY_FOR_STATUS.VALUE',
                    'TRUCK_MARRIAGE_CERTIFICATE_FOR_STATUS_VALUE' => 'TRUCK_MARRIAGE_CERTIFICATE_FOR_STATUS.VALUE',
                    'TRUCK_FREE_USAGE_FOR_STATUS_VALUE' => 'TRUCK_FREE_USAGE_FOR_STATUS.VALUE',
                    'DONKEY_LICENSE_FOR_PLATE_VALUE' => 'DONKEY_LICENSE_FOR_PLATE.VALUE',
                    'TRAILER_LICENSE_FOR_PLATE_VALUE' => 'TRAILER_LICENSE_FOR_PLATE.VALUE',
                    'TRAILER_SECONDARY_LICENSE_FOR_PLATE_VALUE' => 'TRAILER_SECONDARY_LICENSE_FOR_PLATE.VALUE',
                    'TRUCK_LICENSE_FOR_PLATE_VALUE' => 'TRUCK_LICENSE_FOR_PLATE.VALUE',
                    'DONKEY_LEASING_COMPANY_FOR_STATUS_VALUE' => 'DONKEY_LEASING_COMPANY_FOR_STATUS.VALUE',
                    'DONKEY_MARRIAGE_CERTIFICATE_FOR_STATUS_VALUE' => 'DONKEY_MARRIAGE_CERTIFICATE_FOR_STATUS.VALUE',
                    'DONKEY_FREE_USAGE_FOR_STATUS_VALUE' => 'DONKEY_FREE_USAGE_FOR_STATUS.VALUE',
                    'TRAILER_LEASING_COMPANY_FOR_STATUS_VALUE' => 'TRAILER_LEASING_COMPANY_FOR_STATUS.VALUE',
                    'TRAILER_MARRIAGE_CERTIFICATE_FOR_STATUS_VALUE' => 'TRAILER_MARRIAGE_CERTIFICATE_FOR_STATUS.VALUE',
                    'TRAILER_FREE_USAGE_FOR_STATUS_VALUE' => 'TRAILER_FREE_USAGE_FOR_STATUS.VALUE',
                    'DONKEY_LEASING_COMPANY_STATUS_VALUE' => 'DONKEY_LEASING_COMPANY_STATUS.VALUE',
                    'DONKEY_MARRIAGE_CERTIFICATE_STATUS_VALUE' => 'DONKEY_MARRIAGE_CERTIFICATE_STATUS.VALUE',
                    'DONKEY_FREE_USAGE_STATUS_VALUE' => 'DONKEY_FREE_USAGE_STATUS.VALUE',
                    'TRAILER_LEASING_COMPANY_STATUS_VALUE' => 'TRAILER_LEASING_COMPANY_STATUS.VALUE',
                    'TRAILER_MARRIAGE_CERTIFICATE_STATUS_VALUE' => 'TRAILER_MARRIAGE_CERTIFICATE_STATUS.VALUE',
                    'TRAILER_FREE_USAGE_STATUS_VALUE' => 'TRAILER_FREE_USAGE_STATUS.VALUE',
                ],
            ])->fetch();

            $item = [
                'ID' => $id,
                'NAME' => $shipping['NAME'],
                'DATE' => $shipping['DATE_SHIPMENT_VALUE'],
                'CARGO' => $shipping['CARGO_OWNER_VALUE'],
                'CARGO_INN' => $shipping['CARGO_OWNER_INN_VALUE'],
                'CARRIER' => $shipping['CARRIER_VALUE'],
                'CARRIER_INN' => $shipping['CARRIER_INN_VALUE'],
                'FORWARDER' => $shipping['FORWARDER_VALUE'],
                'FORWARDER_INN' => $shipping['FORWARDER_INN_VALUE'],
                'CHECKLIST_CARRIER' => $shipping['CHECKLIST_CARRIER_VALUE'],
                'CHECKLIST_FORWARDER' => $shipping['CHECKLIST_FORWARDER_VALUE'],
                'CONTRACT_CHECK' => $shipping['CONTRACT_CHECK_VALUE'],
                'CONTRACT_CHECK_ERROR' => self::isError($shipping['CONTRACT_CHECK_VALUE']),
                'CONTRACT_EXP_STATUS' => $shipping['CONTRACT_EXPEDITION_STATUS_VALUE'],
                'CONTRACT_TRANSPORT_STATUS' => $shipping['CONTRACT_TRANSPORTATION_STATUS_VALUE'],
                'CONTRACT_ORDER_ONE_TIME_STATUS' => $shipping['CONTRACT_ORDER_ONE_TIME_STATUS_VALUE'],
                'DOCUMENTS_CHECK' => $shipping['DOCUMENTS_CHECK_VALUE'],
                'DOCUMENTS_CHECK_ERROR' => self::isError($shipping['DOCUMENTS_CHECK_VALUE']),
                'DOCUMENTS_EPD_STATUS' => $shipping['DOCUMENTS_EPD_STATUS_VALUE'],
                'DOCUMENTS_EXPEDITOR_STATUS' => $shipping['DOCUMENTS_EXPEDITOR_STATUS_VALUE'],
                'DOCUMENTS_EXPEDITOR_RECEIPT_STATUS' => $shipping['DOCUMENTS_EXPEDITOR_RECEIPT_STATUS_VALUE'],
                'DOCUMENTS_DRIVER_STATUS' => $shipping['DOCUMENTS_DRIVER_APPROVALS_STATUS_VALUE'],
                'DOCUMENTS_TRANSPORT_STATUS' => $shipping['DOCUMENTS_APPLICATION_TRANSPORTATION_STATUS_VALUE'],
                'AUTOMATIC_CHECKS' => $shipping['AUTOMATIC_CHECKS_VALUE'],
                'AUTOMATIC_CHECK_ERROR' => self::isError($shipping['AUTOMATIC_CHECKS_VALUE']),
                'AUTOMATIC_PRICES_STATUS' => $shipping['AUTOMATIC_PRICES_STATUS_VALUE'],
                'AUTOMATIC_GEO_MONITORING_STATUS' => $shipping['AUTOMATIC_GEO_MONITORING_STATUS_VALUE'],
                'ACCOUNTING_CHECKS' => $shipping['ACCOUNTING_CHECKS_VALUE'],
                'ACCOUNTING_CHECKS_ERROR' => self::isError($shipping['ACCOUNTING_CHECKS_VALUE']),
                'ACCOUNTING_INVOICE_STATUS' => $shipping['ACCOUNTING_INVOICE_STATUS_VALUE'],
                'ACCOUNTING_ACT_ACCEPTANCE_STATUS' => $shipping['ACCOUNTING_ACT_ACCEPTANCE_STATUS_VALUE'],
                'ACCOUNTING_ACT_MULTIPLE_TRANSPORT_STATUS' => $shipping['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS_VALUE'],
                'ACCOUNTING_TRANSPORT_REGISTRY_STATUS' => $shipping['ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS_VALUE'],
                'ACCOUNTING_TAX_INVOICE_STATUS' => $shipping['ACCOUNTING_TAX_INVOICE_STATUS_VALUE'],
                'ACCOUNTING_UPD_STATUS' => $shipping['ACCOUNTING_UPD_STATUS_VALUE'],
                'DONKEY_CHECKS' => $shipping['DONKEY_CHECKS_VALUE'],
                'DONKEY_CHECKS_ERROR' => self::isError($shipping['DONKEY_CHECKS_VALUE']),
                'DONKEY_LICENSE_PLATE' => $shipping['DONKEY_LICENSE_PLATE_VALUE'],
                'DONKEY_STS_STATUS' => $shipping['DONKEY_STS_STATUS_VALUE'],
                'DONKEY_RENT_AGREEMENT_STATUS' => $shipping['DONKEY_RENT_AGREEMENT_STATUS_VALUE'],
                'DONKEY_LEASING_COMPANY_STATUS' => $shipping['DONKEY_LEASING_COMPANY_STATUS_VALUE'],
                'DONKEY_MARRIAGE_CERTIFICATE_STATUS' => $shipping['DONKEY_MARRIAGE_CERTIFICATE_STATUS_VALUE'],
                'DONKEY_FREE_USAGE_STATUS' => $shipping['DONKEY_FREE_USAGE_STATUS_VALUE'],
                'DONKEY_FOR_CHECKS' => $shipping['DONKEY_FOR_CHECKS_VALUE'],
                'DONKEY_FOR_CHECKS_ERROR' => self::isError($shipping['DONKEY_FOR_CHECKS_VALUE']),
                'DONKEY_LEASING_COMPANY_STATUS_FOR' => $shipping['DONKEY_LEASING_COMPANY_FOR_STATUS_VALUE'],
                'DONKEY_MARRIAGE_CERTIFICATE_STATUS_FOR' => $shipping['DONKEY_LEASING_COMPANY_FOR_STATUS_VALUE'],
                'DONKEY_FREE_USAGE_STATUS_FOR' => $shipping['DONKEY_LEASING_COMPANY_FOR_STATUS_VALUE'],
                'DONKEY_LICENSE_PLATE_FOR' => $shipping['DONKEY_LICENSE_FOR_PLATE_VALUE'],
                'DONKEY_STS_STATUS_FOR' => $shipping['DONKEY_STS_FOR_STATUS_VALUE'],
                'DONKEY_RENT_STATUS_FOR' => $shipping['DONKEY_RENT_FOR_STATUS_VALUE'],
                'TRAILER_CHECKS' => $shipping['TRAILER_CHECKS_VALUE'],
                'TRAILER_CHECKS_ERROR' => self::isError($shipping['TRAILER_CHECKS_VALUE']),
                'TRAILER_LICENSE_PLATE' => $shipping['TRAILER_LICENSE_PLATE_VALUE'],
                'TRAILER_STS_STATUS' => $shipping['TRAILER_STS_STATUS_VALUE'],
                'TRAILER_RENT_AGREEMENT_STATUS' => $shipping['TRAILER_RENT_AGREEMENT_STATUS_VALUE'],
                'TRAILER_SECONDARY_CHECKS' => $shipping['TRAILER_SECONDARY_CHECKS_VALUE'],
                'TRAILER_SECONDARY_CHECKS_ERROR' => self::isError($shipping['TRAILER_SECONDARY_CHECKS_VALUE']),
                'TRAILER_SECONDARY_LICENSE_PLATE' => $shipping['TRAILER_SECONDARY_LICENSE_PLATE_VALUE'],
                'TRAILER_SECONDARY_STS_STATUS' => $shipping['TRAILER_SECONDARY_STS_STATUS_VALUE'],
                'TRAILER_SECONDARY_RENT_AGREEMENT_STATUS' => $shipping['TRAILER_SECONDARY_RENT_AGREEMENT_STATUS_VALUE'],
                'TRAILER_SECONDARY_LEASING_COMPANY_STATUS' => $shipping['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_STATUS_VALUE'],
                'TRAILER_SECONDARY_CERTIFICATE_STATUS' => $shipping['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_STATUS_VALUE'],
                'TRAILER_SECONDARY_FREE_USAGE_STATUS' => $shipping['TRAILER_SECONDARY_FREE_USAGE_STATUS_VALUE'],
                'TRUCK_CHECKS' => $shipping['TRUCK_CHECKS_VALUE'],
                'TRUCK_CHECKS_ERROR' => self::isError($shipping['TRUCK_CHECKS_VALUE']),
                'TRUCK_LICENSE_PLATE' => $shipping['TRUCK_LICENSE_PLATE_VALUE'],
                'TRUCK_STS_STATUS' => $shipping['TRUCK_STS_STATUS_VALUE'],
                'TRUCK_RENT_AGREEMENT_STATUS' => $shipping['TRUCK_RENT_AGREEMENT_STATUS_VALUE'],
                'TRUCK_LEASING_COMPANY_STATUS' => $shipping['TRUCK_AGREEMENT_LEASING_COMPANY_STATUS_VALUE'],
                'TRUCK_CERTIFICATE_STATUS' => $shipping['TRUCK_MARRIAGE_CERTIFICATE_STATUS_VALUE'],
                'TRUCK_FREE_USAGE_STATUS' => $shipping['TRUCK_FREE_USAGE_STATUS_VALUE'],
                'CONTRACT_FOR_CHECK' => $shipping['CONTRACT_FOR_CHECK_VALUE'],
                'CONTRACT_FOR_CHECK_ERROR' => self::isError($shipping['CONTRACT_FOR_CHECK_VALUE']),
                'DOCUMENTS_FOR_CHECK' => $shipping['DOCUMENTS_FOR_CHECK_VALUE'],
                'DOCUMENTS_FOR_CHECK_ERROR' => self::isError($shipping['DOCUMENTS_FOR_CHECK_VALUE']),
                'AUTOMATIC_FOR_CHECKS' => $shipping['AUTOMATIC_FOR_CHECKS_VALUE'],
                'AUTOMATIC_FOR_CHECKS_ERROR' => self::isError($shipping['AUTOMATIC_FOR_CHECKS_VALUE']),
                'ACCOUNTING_FOR_CHECKS' => $shipping['ACCOUNTING_FOR_CHECKS_VALUE'],
                'ACCOUNTING_FOR_CHECKS_ERROR' => self::isError($shipping['ACCOUNTING_FOR_CHECKS_VALUE']),
                'TRAILER_FOR_CHECKS' => $shipping['TRAILER_FOR_CHECKS_VALUE'],
                'TRAILER_FOR_CHECKS_ERROR' => self::isError($shipping['TRAILER_FOR_CHECKS_VALUE']),
                'TRAILER_SECONDARY_FOR_CHECKS' => $shipping['TRAILER_SECONDARY_FOR_CHECKS_VALUE'],
                'TRAILER_SECONDARY_FOR_CHECKS_ERROR' => self::isError($shipping['TRAILER_SECONDARY_FOR_CHECKS_VALUE']),
                'TRUCK_FOR_CHECKS' => $shipping['TRUCK_FOR_CHECKS_VALUE'],
                'TRUCK_FOR_CHECKS_ERROR' => self::isError($shipping['TRUCK_FOR_CHECKS_VALUE']),
                'CONTRACT_EXP_STATUS_FOR' => $shipping['CONTRACT_EXPEDITION_FOR_STATUS_VALUE'],
                'CONTRACT_TRANSPORT_STATUS_FOR' => $shipping['CONTRACT_TRANSPORTATION_FOR_STATUS_VALUE'],
                'CONTRACT_ORDER_ONE_TIME_STATUS_FOR' => $shipping['CONTRACT_ORDER_ONE_TIME_FOR_STATUS_VALUE'],
                'DOCUMENTS_EPD_STATUS_FOR' => $shipping['DOCUMENTS_EPD_FOR_STATUS_VALUE'],
                'DOCUMENTS_EXPEDITOR_STATUS_FOR' => $shipping['DOCUMENTS_EXPEDITOR_FOR_STATUS_VALUE'],
                'DOCUMENTS_EXPEDITOR_RECEIPT_STATUS_FOR' => $shipping['DOCUMENTS_EXPEDITOR_RECEIPT_FOR_STATUS_VALUE'],
                'DOCUMENTS_DRIVER_STATUS_FOR' => $shipping['DOCUMENTS_DRIVER_APPROVALS_FOR_STATUS_VALUE'],
                'DOCUMENTS_TRANSPORT_STATUS_FOR' => $shipping['DOCUMENTS_APPLICATION_TRANSPORT_FOR_STATUS_VALUE'],
                'AUTOMATIC_PRICES_STATUS_FOR' => $shipping['AUTOMATIC_PRICES_FOR_STATUS_VALUE'],
                'AUTOMATIC_GEO_MONITORING_STATUS_FOR' => $shipping['AUTOMATIC_GEO_MONITORING_FOR_STATUS_VALUE'],
                'ACCOUNTING_INVOICE_STATUS_FOR' => $shipping['ACCOUNTING_INVOICE_FOR_STATUS_VALUE'],
                'ACCOUNTING_ACT_ACCEPTANCE_STATUS_FOR' => $shipping['ACCOUNTING_ACT_ACCEPTANCE_FOR_STATUS_VALUE'],
                'ACCOUNTING_ACT_MULTIPLE_TRANSPORT_STATUS_FOR' => $shipping['ACCOUNTING_ACT_MULTIPLE_TRANSPORT_FOR_STATUS_VALUE'],
                'ACCOUNTING_TRANSPORT_REGISTRY_STATUS_FOR' => $shipping['ACCOUNTING_TRANSPORTATION_REGISTRY_FOR_STATUS_VALUE'],
                'ACCOUNTING_TAX_INVOICE_STATUS_FOR' => $shipping['ACCOUNTING_TAX_INVOICE_FOR_STATUS_VALUE'],
                'ACCOUNTING_UPD_STATUS_FOR' => $shipping['ACCOUNTING_UPD_FOR_STATUS_VALUE'],
                'TRAILER_STS_STATUS_FOR' => $shipping['TRAILER_STS_FOR_STATUS_VALUE'],
                'TRAILER_RENT_AGREEMENT_STATUS_FOR' => $shipping['TRAILER_RENT_AGREEMENT_FOR_STATUS_VALUE'],
                'TRAILER_SECONDARY_STS_STATUS_FOR' => $shipping['TRAILER_SECONDARY_STS_FOR_STATUS_VALUE'],
                'TRAILER_SECONDARY_RENT_AGR_STATUS_FOR' => $shipping['TRAILER_SECONDARY_RENT_AGREEMENT_FOR_STATUS_VALUE'],
                'TRAILER_SECONDARY_LEASING_COMPANY_STATUS_FOR' => $shipping['TRAILER_SECONDARY_LEASING_COMPANY_FOR_STATUS_VALUE'],
                'TRAILER_SECONDARY_CERTIFICATE_STATUS_FOR' => $shipping['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_FOR_STATUS_VALUE'],
                'TRAILER_SECONDARY_FREE_USAGE_STATUS_FOR' => $shipping['TRAILER_SECONDARY_FREE_USAGE_FOR_STATUS_VALUE'],
                'TRUCK_STS_STATUS_FOR' => $shipping['TRUCK_STS_FOR_STATUS_VALUE'],
                'TRUCK_RENT_AGR_STATUS_FOR' => $shipping['TRUCK_RENT_AGREEMENT_FOR_STATUS_VALUE'],
                'TRUCK_AGR_LEASING_COMPANY_STATUS_FOR' => $shipping['TRUCK_AGREEMENT_LEASING_COMPANY_FOR_STATUS_VALUE'],
                'TRUCK_CERTIFICATE_STATUS_FOR' => $shipping['TRUCK_MARRIAGE_CERTIFICATE_FOR_STATUS_VALUE'],
                'TRUCK_FREE_USAGE_STATUS_FOR' => $shipping['TRUCK_FREE_USAGE_FOR_STATUS_VALUE'],
                'TRAILER_LICENSE_PLATE_FOR' => $shipping['TRAILER_LICENSE_FOR_PLATE_VALUE'],
                'TRAILER_SECONDARY_LICENSE_PLATE_FOR' => $shipping['TRAILER_SECONDARY_LICENSE_FOR_PLATE_VALUE'],
                'TRUCK_LICENSE_PLATE_FOR' => $shipping['TRUCK_LICENSE_FOR_PLATE_VALUE'],
                'STATUS_SHIPPING' => $shipping['STATUS_SHIPPING_VALUE'],
                'TRAILER_LEASING_COMPANY_STATUS_FOR' => $shipping['TRAILER_LEASING_COMPANY_FOR_STATUS_VALUE'],
                'TRAILER_MARRIAGE_CERTIFICATE_STATUS_FOR' => $shipping['TRAILER_MARRIAGE_CERTIFICATE_FOR_STATUS_VALUE'],
                'TRAILER_FREE_USAGE_STATUS_FOR' => $shipping['TRAILER_FREE_USAGE_FOR_STATUS_VALUE'],
                'TRAILER_LEASING_COMPANY_STATUS' => $shipping['TRAILER_LEASING_COMPANY_STATUS_VALUE'],
                'TRAILER_MARRIAGE_CERTIFICATE_STATUS' => $shipping['TRAILER_MARRIAGE_CERTIFICATE_STATUS_VALUE'],
                'TRAILER_FREE_USAGE_STATUS' => $shipping['TRAILER_FREE_USAGE_STATUS_VALUE'],
                'AUTO_PRICES' => HLBlock::getPrice($id),
            ];

            $properties = HLBlock::getProperties($item['ID']);

            return array_merge($item, $properties);
        } catch (\Exception $e) {
            $this->addError(new Error($e->getMessage(), $e->getCode()));

            return null;
        }
    }

    /**
     * Возвращаем архив документов
     * элемента
     * @param int $id
     * @return array|null
     */
    public function getFileArchivAction(int $id): ?array
    {
        try {
            Loader::IncludeModule("highloadblock");

            $arPackFiles = [];
            $hlblockId = HL\HighloadBlockTable::getList([
                'filter' => ['=NAME' => 'FnsLinkDocuments']
            ])->fetch();

            $entity_data_class = (HL\HighloadBlockTable::compileEntity($hlblockId))->getDataClass();

            $links = $entity_data_class::getList([
                "select" => ["UF_NAME_LINK", "UF_LINK"],
                "filter" => [
                    "UF_ID_ELEMENT" => $id,
                    "!UF_ID_GROUP" => ['automatic_checks'],
                    "!UF_LINK" => '',
                ]
            ])->fetchAll();

            $hlblockIdFor = HL\HighloadBlockTable::getList([
                'filter' => ['=NAME' => 'FnsLinkDocumentsForwardes']
            ])->fetch();

            $entity_data_class_for = (HL\HighloadBlockTable::compileEntity($hlblockIdFor))->getDataClass();

            $linksFor = $entity_data_class_for::getList([
                "select" => ["UF_NAME_LINK", "UF_LINK"],
                "filter" => [
                    "UF_ID_ELEMENT" => $id,
                    "!UF_ID_GROUP" => ['automatic_checks'],
                    "!UF_LINK" => '',
                ]
            ])->fetchAll();

            $links = array_merge($links, $linksFor);

            $fileLink = [];

            foreach ($links as $link) {
                $idLink = array_diff(explode(',', $link['UF_LINK']), ['']);
                $idName = array_diff(explode(',', $link['UF_NAME_LINK']), ['']);

                foreach ($idLink as $key => $item) {
                    $fileLink[] = [
                        'ID' => $idName[$key],
                        'NAME' => $item,
                    ];
                }
            }

            $elementName = \Taxcom\Library\Helper\Vitrina::getElement($id);

            $client = new HttpClient();
            $client->setHeader('Authorization', 'Token QwYT6BDYarKxkCRpWmb3I0t1mLRZHUWxS2IVTLwS97Ul1pRi9pOQ8H7xhMwUsdyH');
            foreach ($fileLink as $key => $file) {
                $name = $file['ID'] ?: 'Файл ' . $key .'.pdf';
                $isFile = new IO\File(Application::getDocumentRoot() . '/upload/tmp/'. $elementName['NAME'] . '/' . $name);

                $client->download(
                    $file['NAME'],
                    $isFile->getPath()
                );

                $arPackFiles[] = $isFile->getPath();
            }

            $packarc = CBXArchive::GetArchive(Application::getDocumentRoot() . "/upload/tmp/". $elementName['NAME'] .".zip");
            $packarc->SetOptions(Array(
                "REMOVE_PATH" => Application::getDocumentRoot() . "/upload/tmp/". $elementName['NAME'] . "/",
            ));
            $packarc->Pack($arPackFiles);

            return ['URL' => "/upload/tmp/". $elementName['NAME'] .".zip"];
        } catch (\Exception $e) {
            $this->addError(new Error($e->getMessage(), $e->getCode()));

            return null;
        }
    }

    /**
     * Возвращаем архив файлов
     * по выбранным элементам
     *
     * @param array $fields
     * @return array|null
     */
    public function getArchiveAction(array $fields): ?array
    {
        try {
            Loader::IncludeModule("highloadblock");

            $arPackFiles = [];
            $hlblockId = HL\HighloadBlockTable::getList([
                'filter' => ['=NAME' => 'FnsLinkDocuments']
            ])->fetch();

            $entity_data_class = (HL\HighloadBlockTable::compileEntity($hlblockId))->getDataClass();

            $links = $entity_data_class::getList([
                "select" => ["UF_NAME_LINK", "UF_LINK", "UF_ID_ELEMENT"],
                "filter" => [
                    "UF_ID_ELEMENT" => $fields['ID'],
                    "!UF_ID_GROUP" => ['automatic_checks'],
                    "!UF_LINK" => '',
                ]
            ])->fetchAll();

            $hlblockIdFor = HL\HighloadBlockTable::getList([
                'filter' => ['=NAME' => 'FnsLinkDocumentsForwardes']
            ])->fetch();

            $entity_data_class_for = (HL\HighloadBlockTable::compileEntity($hlblockIdFor))->getDataClass();

            $linksFor = $entity_data_class_for::getList([
                "select" => ["UF_NAME_LINK", "UF_LINK", "UF_ID_ELEMENT"],
                "filter" => [
                    "UF_ID_ELEMENT" =>  $fields['ID'],
                    "!UF_ID_GROUP" => ['automatic_checks'],
                    "!UF_LINK" => '',
                ]
            ])->fetchAll();

            $links = array_merge($links, $linksFor);

            $fileLink = [];

            foreach ($links as $link) {
                $idLink = array_diff(explode(',', $link['UF_LINK']), ['']);
                $idName = array_diff(explode(',', $link['UF_NAME_LINK']), ['']);
                $elementName = \Taxcom\Library\Helper\Vitrina::getElement((int) $link['UF_ID_ELEMENT']);

                foreach ($idLink as $key => $item) {
                    $fileLink[] = [
                        'ID' => $idName[$key],
                        'NAME' => $item,
                        'ID_ELEMENT' => $elementName['NAME'],
                    ];
                }
            }

            $client = new HttpClient();
            $client->setHeader('Authorization', 'Token QwYT6BDYarKxkCRpWmb3I0t1mLRZHUWxS2IVTLwS97Ul1pRi9pOQ8H7xhMwUsdyH');
            foreach ($fileLink as $key => $file) {
                $name = $file['ID'] ?: 'Файл ' . $key . '.pdf';
                $isFile = new IO\File(Application::getDocumentRoot() . '/upload/tmp/archive/'. $file['ID_ELEMENT'] . '/' . $name);

                $client->download(
                    $file['NAME'],
                    $isFile->getPath()
                );

                $arPackFiles[] = $isFile->getPath();
            }

            $packarc = CBXArchive::GetArchive(Application::getDocumentRoot() . "/upload/tmp/archive/file_archive.zip");
            $packarc->SetOptions(Array(
                "REMOVE_PATH" => Application::getDocumentRoot() . "/upload/tmp/archive/",
            ));
            $packarc->Pack($arPackFiles);

            return ['URL' => "/upload/tmp/archive/file_archive.zip"];
        } catch (\Exception $e) {
            $this->addError(new Error($e->getMessage(), $e->getCode()));

            return null;
        }
    }

    /**
     * Возвращаем ссылку на файл
     *
     * @param array $fields
     * @return array|null
     */
    public function getFileAction(array $fields): ?array
    {
        try {
            $client = new HttpClient();
            $client->setHeader('Authorization', 'Token QwYT6BDYarKxkCRpWmb3I0t1mLRZHUWxS2IVTLwS97Ul1pRi9pOQ8H7xhMwUsdyH');
            $client->get($fields['LINK']);

            if (stripos($fields['NAME'], 'Файл') !== false) {
                $type = $client->getContentType();
                foreach (MimeType::getMimeTypeList() as $key => $item) {
                    if($type === $item) {
                        $fields['NAME'] .= '.' .$key;
                        break;
                    }
                }
            }

            $client->download(
                $fields['LINK'],
                Application::getDocumentRoot() . '/upload/file/'. $fields['NAME']
            );

            $url = '/upload/file/'. $fields['NAME'];

            return ['URL' => $url];
        } catch (\Exception $e) {
            $this->addError(new Error($e->getMessage(), $e->getCode()));

            return null;
        }
    }

    /**
     * Проверяем на количество не
     * выполненных шагов
     *
     * @param string|null $checks
     * @return bool|void
     */
    protected static function isError(?string $checks)
    {
        if ($checks) {
            $step = explode("/", $checks);

            return $step[0] === $step[1];
        }
    }
}