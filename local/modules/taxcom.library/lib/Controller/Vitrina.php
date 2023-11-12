<?php

declare(strict_types=1);

namespace Taxcom\Library\Controller;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\AutoWire\Parameter;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\IO;
use CBXArchive;

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
                'DONKEY_RENT_AGREEMENT_STATUS' => $shipping['DONKEY_RENT_AGREEMENT_STATUS_VALUE'],'DONKEY_LEASING_COMPANY_STATUS' => $shipping['DONKEY_LEASING_COMPANY_FOR_STATUS_VALUE'],
                'DONKEY_MARRIAGE_CERTIFICATE_STATUS' => $shipping['DONKEY_LEASING_COMPANY_FOR_STATUS_VALUE'],
                'DONKEY_FREE_USAGE_STATUS' => $shipping['DONKEY_LEASING_COMPANY_FOR_STATUS_VALUE'],
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
                'TRAILER_LEASING_COMPANY_STATUS_FOR' => $shipping['DONKEY_LEASING_COMPANY_FOR_STATUS_VALUE'],
                'TRAILER_MARRIAGE_CERTIFICATE_STATUS_FOR' => $shipping['DONKEY_LEASING_COMPANY_FOR_STATUS_VALUE'],
                'TRAILER_FREE_USAGE_STATUS_FOR' => $shipping['DONKEY_LEASING_COMPANY_FOR_STATUS_VALUE'],
                'TRAILER_LEASING_COMPANY_STATUS' => $shipping['DONKEY_LEASING_COMPANY_FOR_STATUS_VALUE'],
                'TRAILER_MARRIAGE_CERTIFICATE_STATUS' => $shipping['DONKEY_LEASING_COMPANY_FOR_STATUS_VALUE'],
                'TRAILER_FREE_USAGE_STATUS' => $shipping['DONKEY_LEASING_COMPANY_FOR_STATUS_VALUE'],
            ];

            $properties = self::getProperties($item['ID']);

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
                    "!UF_LINK" => '',
                ]
            ])->fetchAll();

            self::dirDel(Application::getDocumentRoot() . "/upload/tmp/");
            $sDirTmpName = randString();
            $sDirTmpPath = Application::getDocumentRoot() . "/upload/tmp/$sDirTmpName/";

            $file = new IO\File(Application::getDocumentRoot() . "/upload/tmp/$sDirTmpName/Не доступные ссылки.txt");
            $arPackFiles[] = $file->getPath();

            foreach ($links as $k => $link) {
                $fp = fopen($link['UF_LINK'], 'rb');
                if (!$fp) {
                    $file->putContents($link['UF_LINK'] . "\n", IO\File::APPEND);
                } else {
                    $arPackFiles[] = $link['UF_LINK'];
                }
            }

            $packarc = CBXArchive::GetArchive($sDirTmpPath . "file.zip");
            $packarc->Pack($arPackFiles);

            return ['URL' => "/upload/tmp/$sDirTmpName/file.zip"];
        } catch (\Exception $e) {
            $this->addError(new Error($e->getMessage(), $e->getCode()));

            return null;
        }
    }

    /**
     * Возвращаем архив файлов
     * по выбранным элементам
     *
     * @return array|null
     */
    public function getArchivAction(): ?array
    {

        die('234');
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
                    "!UF_LINK" => '',
                ]
            ])->fetchAll();

            self::dirDel(Application::getDocumentRoot() . "/upload/tmp/");
            $sDirTmpName = randString();
            $sDirTmpPath = Application::getDocumentRoot() . "/upload/tmp/$sDirTmpName/";

            $file = new IO\File(Application::getDocumentRoot() . "/upload/tmp/$sDirTmpName/Не доступные ссылки.txt");
            $arPackFiles[] = $file->getPath();

            foreach ($links as $k => $link) {
                $fp = fopen($link['UF_LINK'], 'rb');
                if (!$fp) {
                    $file->putContents($link['UF_LINK'] . "\n", IO\File::APPEND);
                } else {
                    $arPackFiles[] = $link['UF_LINK'];
                }
            }

            $packarc = CBXArchive::GetArchive($sDirTmpPath . "file.zip");
            $packarc->Pack($arPackFiles);

            return ['URL' => "/upload/tmp/$sDirTmpName/file.zip"];
        } catch (\Exception $e) {
            $this->addError(new Error($e->getMessage(), $e->getCode()));

            return null;
        }
    }

    /**
     * Удаляем директорию
     *
     * @param $dir
     * @return void
     */
    protected static function dirDel($dir): void
    {
        $d = opendir($dir);
        while (($entry = readdir($d)) !== false) {
            if ($entry != "." && $entry != "..") {
                if (is_dir($dir . "/" . $entry)) {
                    self::dirDel($dir . "/" . $entry);
                } else {
                    unlink($dir . "/" . $entry);
                }
            }
        }
        closedir($d);
        rmdir($dir);
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

    /**
     * Возвращаем свойства
     * ссылок документов
     *
     * @param $id
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    protected static function getProperties($id): array
    {
        $properties = [];

        $hlblockId = HL\HighloadBlockTable::getList([
            'filter' => ['=NAME' => 'FnsLinkDocuments']
        ])->fetch();

        $entity_data_class = (HL\HighloadBlockTable::compileEntity($hlblockId))->getDataClass();

        $links = $entity_data_class::getList([
            "select" => ["*"],
            "filter" => [
                "UF_ID_ELEMENT" => $id,
            ]
        ])->fetchAll();

        foreach ($links as $link) {
            switch ($link['UF_ID_GROUP']) {
                case 'contract':
                    if ($link['UF_GROUP_NAME'] === 'transport_expedition_contract') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['CONTRACT_EXPEDITION_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['CONTRACT_EXPEDITION_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['CONTRACT_EXPEDITION_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['CONTRACT_EXPEDITION_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'transportation_contract') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['CONTRACT_TRANSPORTATION_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['CONTRACT_TRANSPORTATION_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['CONTRACT_TRANSPORTATION_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['CONTRACT_TRANSPORTATION_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'order_one_time_contract') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['CONTRACT_ORDER_ONE_TIME_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['CONTRACT_ORDER_ONE_TIME_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['CONTRACT_ORDER_ONE_TIME_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['CONTRACT_ORDER_ONE_TIME_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    break;
                case 'execution_documents':
                    if ($link['UF_GROUP_NAME'] === 'epd') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['DOCUMENTS_EPD_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DOCUMENTS_EPD_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['DOCUMENTS_EPD_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DOCUMENTS_EPD_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'expeditor_order') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['DOCUMENTS_EXPEDITOR_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DOCUMENTS_EXPEDITOR_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['DOCUMENTS_EXPEDITOR_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DOCUMENTS_EXPEDITOR_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'expeditor_agent_receipt') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['DOCUMENTS_EXPEDITOR_RECEIPT_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DOCUMENTS_EXPEDITOR_RECEIPT_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['DOCUMENTS_EXPEDITOR_RECEIPT_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DOCUMENTS_EXPEDITOR_RECEIPT_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'driver_approvals') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['DOCUMENTS_DRIVER_APPROVALS_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DOCUMENTS_DRIVER_APPROVALS_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['DOCUMENTS_DRIVER_APPROVALS_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DOCUMENTS_DRIVER_APPROVALS_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'application_for_transportation') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['DOCUMENTS_APPLICATION_TRANSPORTATION_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DOCUMENTS_APPLICATION_TRANSPORTATION_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['DOCUMENTS_APPLICATION_TRANSPORTATION_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DOCUMENTS_APPLICATION_TRANSPORTATION_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    break;
                case 'automatic_checks':
                    if ($link['UF_GROUP_NAME'] === 'prices') {
                        $properties['AUTOMATIC_PRICES'] = $link['UF_LINK'];
                    }
                    if ($link['UF_GROUP_NAME'] === 'geo_monitoring') {
                        $properties['AUTOMATIC_GEO_MONITORING'] = $link['UF_LINK'];
                    }
                    break;
                case 'accounting':
                    if ($link['UF_GROUP_NAME'] === 'invoice') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['ACCOUNTING_INVOICE_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['ACCOUNTING_INVOICE_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['ACCOUNTING_INVOICE_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['ACCOUNTING_INVOICE_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'act_of_service_acceptance') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['ACCOUNTING_ACT_ACCEPTANCE_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['ACCOUNTING_ACT_ACCEPTANCE_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['ACCOUNTING_ACT_ACCEPTANCE_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['ACCOUNTING_ACT_ACCEPTANCE_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'act_of_service_acceptance_multiple_transportations') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'transportation_registry') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'tax_invoice') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['ACCOUNTING_TAX_INVOICE_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['ACCOUNTING_TAX_INVOICE_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['ACCOUNTING_TAX_INVOICE_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['ACCOUNTING_TAX_INVOICE_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'universal_transfer_document') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['ACCOUNTING_UPD_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['ACCOUNTING_UPD_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['ACCOUNTING_UPD_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['ACCOUNTING_UPD_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    break;
                case 'vehicle_donkey':
                    if ($link['UF_GROUP_NAME'] === 'sts') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['DONKEY_STS_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DONKEY_STS_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['DONKEY_STS_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DONKEY_STS_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'rent_agreement') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['DONKEY_RENT_AGREEMENT_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DONKEY_RENT_AGREEMENT_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['DONKEY_RENT_AGREEMENT_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DONKEY_RENT_AGREEMENT_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    break;
                case 'vehicle_main_trailer':
                    if ($link['UF_GROUP_NAME'] === 'sts') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRAILER_STS_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_STS_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_STS_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_STS_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'rent_agreement') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRAILER_RENT_AGREEMENT_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_RENT_AGREEMENT_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_RENT_AGREEMENT_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_RENT_AGREEMENT_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    break;
                case 'vehicle_secondary_trailer':
                    if ($link['UF_GROUP_NAME'] === 'sts') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_STS_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_STS_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_STS_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_STS_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'rent_agreement') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_RENT_AGREEMENT_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_RENT_AGREEMENT_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_RENT_AGREEMENT_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_RENT_AGREEMENT_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'agreement_withLeasingCompany') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'marriage_certificate') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'free_usage_agreement') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_FREE_USAGE_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_FREE_USAGE_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_FREE_USAGE_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_FREE_USAGE_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    break;
                case 'vehicle_truck':
                    if ($link['UF_GROUP_NAME'] === 'sts') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRUCK_STS_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRUCK_STS_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRUCK_STS_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRUCK_STS_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'rent_agreement') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRUCK_RENT_AGREEMENT_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRUCK_RENT_AGREEMENT_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRUCK_RENT_AGREEMENT_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRUCK_RENT_AGREEMENT_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'agreement_withLeasingCompany') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRUCK_AGREEMENT_LEASING_COMPANY_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRUCK_AGREEMENT_LEASING_COMPANY_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRUCK_AGREEMENT_LEASING_COMPANY_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRUCK_AGREEMENT_LEASING_COMPANY_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'marriage_certificate') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRUCK_MARRIAGE_CERTIFICATE_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRUCK_MARRIAGE_CERTIFICATE_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRUCK_MARRIAGE_CERTIFICATE_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRUCK_MARRIAGE_CERTIFICATE_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'free_usage_agreement') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRUCK_FREE_USAGE_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRUCK_FREE_USAGE_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRUCK_FREE_USAGE_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRUCK_FREE_USAGE_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    break;
            }
        }

        $hlblockIdFor = HL\HighloadBlockTable::getList([
            'filter' => ['=NAME' => 'FnsLinkDocumentsForwardes']
        ])->fetch();

        $entity_data_class_for = (HL\HighloadBlockTable::compileEntity($hlblockIdFor))->getDataClass();

        $linksFor = $entity_data_class_for::getList([
            "select" => ["*"],
            "filter" => [
                "UF_ID_ELEMENT" => $id,
            ]
        ])->fetchAll();

        foreach ($linksFor as $linkFor) {
            switch ($linkFor['UF_ID_GROUP']) {
                case 'contract':
                    if ($linkFor['UF_GROUP_NAME'] === 'transport_expedition_contract') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['CONTRACT_EXP_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['CONTRACT_EXP_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['CONTRACT_EXP_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['CONTRACT_EXP_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'transportation_contract') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['CONTRACT_TRANSPORT_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['CONTRACT_TRANSPORT_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['CONTRACT_TRANSPORT_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['CONTRACT_TRANSPORT_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'order_one_time_contract') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['CONTRACT_ORDER_ONE_TIME_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['CONTRACT_ORDER_ONE_TIME_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['CONTRACT_ORDER_ONE_TIME_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['CONTRACT_ORDER_ONE_TIME_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    break;
                case 'execution_documents':
                    if ($linkFor['UF_GROUP_NAME'] === 'epd') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['DOCUMENTS_EPD_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DOCUMENTS_EPD_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['DOCUMENTS_EPD_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DOCUMENTS_EPD_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'expeditor_order') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['DOCUMENTS_EXPEDITOR_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DOCUMENTS_EXPEDITOR_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['DOCUMENTS_EXPEDITOR_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DOCUMENTS_EXPEDITOR_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'expeditor_agent_receipt') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['DOCUMENTS_EXPEDITOR_RECEIPT_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DOCUMENTS_EXPEDITOR_RECEIPT_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['DOCUMENTS_EXPEDITOR_RECEIPT_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DOCUMENTS_EXPEDITOR_RECEIPT_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'driver_approvals') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['DOCUMENTS_DRIVER_APPROVALS_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DOCUMENTS_DRIVER_APPROVALS_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['DOCUMENTS_DRIVER_APPROVALS_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DOCUMENTS_DRIVER_APPROVALS_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'application_for_transportation') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['DOCUMENTS_APPLICATION_TRANSPORTATION_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DOCUMENTS_APPLICATION_TRANSPORTATION_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['DOCUMENTS_APPLICATION_TRANSPORTATION_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DOCUMENTS_APPLICATION_TRANSPORTATION_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    break;
                case 'automatic_checks':
                    if ($linkFor['UF_GROUP_NAME'] === 'prices') {
                        $properties['AUTOMATIC_PRICES_FOR'] = $linkFor['UF_LINK'];
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'geo_monitoring') {
                        $properties['AUTOMATIC_GEO_MONITORING_FOR'] = $linkFor['UF_LINK'];
                    }
                    break;
                case 'accounting':
                    if ($linkFor['UF_GROUP_NAME'] === 'invoice') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['ACCOUNTING_INVOICE_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['ACCOUNTING_INVOICE_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['ACCOUNTING_INVOICE_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['ACCOUNTING_INVOICE_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'act_of_service_acceptance') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['ACCOUNTING_ACT_ACCEPTANCE_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['ACCOUNTING_ACT_ACCEPTANCE_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['ACCOUNTING_ACT_ACCEPTANCE_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['ACCOUNTING_ACT_ACCEPTANCE_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'act_of_service_acceptance_multiple_transportations') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'transportation_registry') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'tax_invoice') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['ACCOUNTING_TAX_INVOICE_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['ACCOUNTING_TAX_INVOICE_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['ACCOUNTING_TAX_INVOICE_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['ACCOUNTING_TAX_INVOICE_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'universal_transfer_document') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['ACCOUNTING_UPD_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['ACCOUNTING_UPD_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['ACCOUNTING_UPD_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['ACCOUNTING_UPD_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    break;
                case 'vehicle_donkey':
                    if ($linkFor['UF_GROUP_NAME'] === 'sts') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['DONKEY_STS_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DONKEY_STS_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['DONKEY_STS_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DONKEY_STS_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'rent_agreement') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['DONKEY_RENT_AGREEMENT_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DONKEY_RENT_AGREEMENT_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['DONKEY_RENT_AGREEMENT_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DONKEY_RENT_AGREEMENT_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    break;
                case 'vehicle_main_trailer':
                    if ($linkFor['UF_GROUP_NAME'] === 'sts') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRAILER_STS_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_STS_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_STS_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_STS_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'rent_agreement') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRAILER_RENT_AGREEMENT_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_RENT_AGREEMENT_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_RENT_AGREEMENT_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_RENT_AGREEMENT_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    break;
                case 'vehicle_secondary_trailer':
                    if ($linkFor['UF_GROUP_NAME'] === 'sts') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_STS_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_STS_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_STS_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_STS_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'rent_agreement') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_RENT_AGREEMENT_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_RENT_AGREEMENT_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_RENT_AGREEMENT_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_RENT_AGREEMENT_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'agreement_withLeasingCompany') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'marriage_certificate') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'free_usage_agreement') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_FREE_USAGE_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_FREE_USAGE_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_SECONDARY_FREE_USAGE_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_SECONDARY_FREE_USAGE_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    break;
                case 'vehicle_truck':
                    if ($linkFor['UF_GROUP_NAME'] === 'sts') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRUCK_STS_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRUCK_STS_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRUCK_STS_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRUCK_STS_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'rent_agreement') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRUCK_RENT_AGREEMENT_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRUCK_RENT_AGREEMENT_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRUCK_RENT_AGREEMENT_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRUCK_RENT_AGREEMENT_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'agreement_withLeasingCompany') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRUCK_AGREEMENT_LEASING_COMPANY_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRUCK_AGREEMENT_LEASING_COMPANY_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRUCK_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRUCK_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'marriage_certificate') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRUCK_MARRIAGE_CERTIFICATE_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRUCK_MARRIAGE_CERTIFICATE_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRUCK_MARRIAGE_CERTIFICATE_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRUCK_MARRIAGE_CERTIFICATE_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'free_usage_agreement') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRUCK_FREE_USAGE_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRUCK_FREE_USAGE_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRUCK_FREE_USAGE_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRUCK_FREE_USAGE_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    break;
            }
        }

        return $properties;
    }
}