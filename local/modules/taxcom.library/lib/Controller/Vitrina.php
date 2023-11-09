<?php

declare(strict_types=1);

namespace Taxcom\Library\Controller;

use Bitrix\Main\Application;
use Bitrix\Main\Engine\AutoWire\Parameter;
use Bitrix\Main\Engine\Response\File;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\IO;
use Bitrix\Main\Web\MimeType;
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
     *
     * @param int $id
     * @return array
     */
    public function getFileArchivAction(int $id)
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
     * Удаляем директорию
     *
     * @param $dir
     * @return void
     */
    protected static function dirDel($dir)
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

    protected static function getProperties($id)
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

        return $properties;
    }
}