<?php

declare(strict_types=1);

namespace Taxcom\Library\HLBlock;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Highloadblock as HL;

/**
 * Метод для работы с HL блоками
 */
class HLBlock
{
    /**
     * Возвращаем отклонение от
     * рыночной стоимости
     *
     * @param int $id
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getPrice(int $id): string
    {
        $hlblockId = HL\HighloadBlockTable::getList([
            'filter' => ['=NAME' => 'FnsLinkDocuments']
        ])->fetch();

        $entity_data_class = (HL\HighloadBlockTable::compileEntity($hlblockId))->getDataClass();

        $price = $entity_data_class::getList([
            "select" => ["UF_LINK"],
            "filter" => [
                "UF_ID_ELEMENT" => $id,
                "UF_GROUP_NAME" => 'prices',
            ]
        ])->fetch();

        $hlblockIdFor = HL\HighloadBlockTable::getList([
            'filter' => ['=NAME' => 'FnsLinkDocumentsForwardes']
        ])->fetch();

        $entity_data_class_for = (HL\HighloadBlockTable::compileEntity($hlblockIdFor))->getDataClass();

        $priceFor = $entity_data_class_for::getList([
            "select" => ["UF_LINK"],
            "filter" => [
                "UF_ID_ELEMENT" => $id,
                "UF_GROUP_NAME" => 'prices',
            ]
        ])->fetch();

        if (floatval($price['UF_LINK']) > 0 && floatval($priceFor['UF_LINK']) > 0) {
            return floatval($price['UF_LINK']) * 100 . "/" . floatval($priceFor['UF_LINK']) * 100;
        }

        if (floatval($price['UF_LINK']) > 0) {
            return floatval($price['UF_LINK']) * 100 . '';
        }

        if (floatval($priceFor['UF_LINK']) > 0) {
            return floatval($priceFor['UF_LINK']) * 100 . '';
        }

        return '';
    }

    /**
     * Проверяем пустые ссылки в
     * перевозчики
     *
     * @param int $id
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function isDocument(int $id): bool
    {
        $hlblockId = HL\HighloadBlockTable::getList([
            'filter' => ['=NAME' => 'FnsLinkDocuments']
        ])->fetch();

        $entity_data_class = (HL\HighloadBlockTable::compileEntity($hlblockId))->getDataClass();

        $doc = $entity_data_class::getList([
            "select" => ["UF_ID_ELEMENT", "UF_ID_GROUP", "UF_LINK"],
            "filter" => [
                "UF_ID_ELEMENT" => $id,
                "!UF_ID_GROUP" => 'automatic_checks',
                "UF_LINK" => '',
            ]
        ])->fetchAll();

        $hlblockIdFor = HL\HighloadBlockTable::getList([
            'filter' => ['=NAME' => 'FnsLinkDocumentsForwardes']
        ])->fetch();

        $entity_data_class_for = (HL\HighloadBlockTable::compileEntity($hlblockIdFor))->getDataClass();

        $docFor = $entity_data_class_for::getList([
            "select" => ["UF_ID_ELEMENT", "UF_ID_GROUP", "UF_LINK"],
            "filter" => [
                "UF_ID_ELEMENT" => $id,
                "!UF_ID_GROUP" => 'automatic_checks',
                "UF_LINK" => '',
            ]
        ])->fetchAll();

        $doc = array_merge($doc, $docFor);

        foreach ($doc as $link) {
            if ($link['UF_LINK'] === '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Возвращаем ID элементов
     * с пустыми отсутствующими документами
     *
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getIdNoDocument(): array
    {
        $hlblockId = HL\HighloadBlockTable::getList([
            'filter' => ['=NAME' => 'FnsLinkDocuments']
        ])->fetch();

        $entity_data_class = (HL\HighloadBlockTable::compileEntity($hlblockId))->getDataClass();

        $elements = $entity_data_class::getList([
            "select" => ["UF_ID_ELEMENT", "UF_ID_GROUP", "UF_LINK"],
            "filter" => [
                "!UF_ID_GROUP" => 'automatic_checks',
                "UF_LINK" => '',
            ]
        ])->fetchAll();

        $hlblockIdFor = HL\HighloadBlockTable::getList([
            'filter' => ['=NAME' => 'FnsLinkDocumentsForwardes']
        ])->fetch();

        $entity_data_class_for = (HL\HighloadBlockTable::compileEntity($hlblockIdFor))->getDataClass();

        $elementsFor = $entity_data_class_for::getList([
            "select" => ["UF_ID_ELEMENT", "UF_ID_GROUP", "UF_LINK"],
            "filter" => [
                "!UF_ID_GROUP" => 'automatic_checks',
                "UF_LINK" => '',
            ]
        ])->fetchAll();

        $elements = array_merge($elements, $elementsFor);
        $id = [];

        foreach ($elements as $element) {
            $id[] = $element['UF_ID_ELEMENT'];
        }

        return array_unique($id);
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
    public static function getProperties($id): array
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
                    if ($link['UF_GROUP_NAME'] === 'agreement_with_leasing_company') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['DONKEY_AGREEMENT_LEASING_COMPANY_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DONKEY_AGREEMENT_LEASING_COMPANY_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['DONKEY_AGREEMENT_LEASING_COMPANY_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DONKEY_AGREEMENT_LEASING_COMPANY_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'marriage_certificate') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['DONKEY_MARRIAGE_CERTIFICATE_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DONKEY_MARRIAGE_CERTIFICATE_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['DONKEY_MARRIAGE_CERTIFICATE_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DONKEY_MARRIAGE_CERTIFICATE_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'free_usage_agreement') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['DONKEY_FREE_USAGE_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DONKEY_FREE_USAGE_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['DONKEY_FREE_USAGE_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['DONKEY_FREE_USAGE_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
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
                    if ($link['UF_GROUP_NAME'] === 'agreement_with_leasing_company') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRAILER_AGR_LEASING_COMPANY_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_AGR_LEASING_COMPANY_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_AGR_LEASING_COMPANY_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_AGR_LEASING_COMPANY_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'marriage_certificate') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRAILER_MARRIAGE_CERTIFICATE_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_MARRIAGE_CERTIFICATE_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_MARRIAGE_CERTIFICATE_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_MARRIAGE_CERTIFICATE_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($link['UF_GROUP_NAME'] === 'free_usage_agreement') {
                        if ($link['UF_ATTACHMENTS']) {
                            $properties['TRAILER_FREE_USAGE_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_FREE_USAGE_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
                        }

                        if ($link['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_FREE_USAGE_EDM_LINK']['VALUE'] .= $link['UF_LINK'] . ',';
                            $properties['TRAILER_FREE_USAGE_EDM_LINK']['DESCRIPTION'] .= $link['UF_NAME_LINK'] . ',';
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
                    if ($link['UF_GROUP_NAME'] === 'agreement_with_leasing_company') {
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
                    if ($link['UF_GROUP_NAME'] === 'agreement_with_leasing_company') {
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
                    if ($linkFor['UF_GROUP_NAME'] === 'agreement_with_leasing_company') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['DONKEY_AGREEMENT_LEASING_COMPANY_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DONKEY_AGREEMENT_LEASING_COMPANY_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['DONKEY_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DONKEY_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'marriage_certificate') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['DONKEY_MARRIAGE_CERTIFICATE_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DONKEY_MARRIAGE_CERTIFICATE_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['DONKEY_MARRIAGE_CERTIFICATE_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DONKEY_MARRIAGE_CERTIFICATE_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'free_usage_agreement') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['DONKEY_FREE_USAGE_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DONKEY_FREE_USAGE_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['DONKEY_FREE_USAGE_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['DONKEY_FREE_USAGE_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
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
                    if ($linkFor['UF_GROUP_NAME'] === 'agreement_with_leasing_company') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRAILER_AGR_LEASING_COMPANY_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_AGR_LEASING_COMPANY_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_AGR_LEASING_COMPANY_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_AGR_LEASING_COMPANY_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'marriage_certificate') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRAILER_MARRIAGE_CERTIFICATE_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_MARRIAGE_CERTIFICATE_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_MARRIAGE_CERTIFICATE_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_MARRIAGE_CERTIFICATE_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }
                    }
                    if ($linkFor['UF_GROUP_NAME'] === 'free_usage_agreement') {
                        if ($linkFor['UF_ATTACHMENTS']) {
                            $properties['TRAILER_FREE_USAGE_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_FREE_USAGE_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
                        }

                        if ($linkFor['UF_EDM_ATTACHMENTS']) {
                            $properties['TRAILER_FREE_USAGE_EDM_LINK_FOR']['VALUE'] .= $linkFor['UF_LINK'] . ',';
                            $properties['TRAILER_FREE_USAGE_EDM_LINK_FOR']['DESCRIPTION'] .= $linkFor['UF_NAME_LINK'] . ',';
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
                    if ($linkFor['UF_GROUP_NAME'] === 'agreement_with_leasing_company') {
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
                    if ($linkFor['UF_GROUP_NAME'] === 'agreement_with_leasing_company') {
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

    /**
     * Возвращаем информацию по документу
     * по его ID
     *
     * @param string $id
     * @return array
     */
    public static function getInfoDocument(string $id): array
    {
        Loader::includeModule('highloadblock');

        $hlblockId = HL\HighloadBlockTable::getList([
            'filter' => ['=NAME' => 'FnsLinkDocuments']
        ])->fetch();

        $entity_data_class = (HL\HighloadBlockTable::compileEntity($hlblockId))->getDataClass();

        $links = $entity_data_class::getList([
            "select" => ["*"],
            "filter" => [
                "UF_LINK" => '%' . $id . '%',
            ]
        ])->fetch();

        $hlblockIdFor = HL\HighloadBlockTable::getList([
            'filter' => ['=NAME' => 'FnsLinkDocumentsForwardes']
        ])->fetch();

        $entity_data_class_for = (HL\HighloadBlockTable::compileEntity($hlblockIdFor))->getDataClass();

        $linksFor = $entity_data_class_for::getList([
            "select" => ["*"],
            "filter" => [
                "UF_LINK" => '%' . $id . '%',
            ]
        ])->fetch();

        return $links ?: $linksFor;
    }
}