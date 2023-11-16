<?php

declare(strict_types=1);

namespace Taxcom\Library\Service;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use CIBlockElement;
use Exception;

/**
 * Парсер сохранения перевозки
 */
class ParserCarrier
{
    /** @var array|mixed Массив перевозки */
    private array $carrier;

    /** @var string Строка перевозчика для парсера */
    private string $json;

    /**
     * @param array $msg
     * @param string $json
     */
    public function __construct(array $msg, string $json)
    {
        $this->carrier = $msg;
        $this->json = $json;
    }

    /**
     * Парсим и сохраняем или обновляем перевозку
     *
     * @return void
     * @throws Exception
     */
    public function isParser(): void
    {
        $element = new CIBlockElement;
        $idIblock = self::getIblockId();
        $id = self::getIdCarrier($this->carrier['execution_request_uid']);

        if ($id === null) {
            $id = $element->Add($this->getFields($idIblock, $this->json));
        } else {
            $element->update($id, $this->getFields($idIblock, $this->json));
        }

        $element::SetPropertyValuesEx($id, $idIblock, $this->getPropertyList());

        $this->setLink($id);
    }

    /**
     * Возвращаем ID инфоблока
     *
     * @return int
     */
    public static function getIblockId(): int
    {
        $vitrina = IblockTable::getList([
            'filter' => ['CODE' => 'vitrina'],
            'select' => ['ID'],
        ])->fetch();

        return (int) $vitrina['ID'];
    }

    /**
     * Возвращаем основные части элемента
     *
     * @param int $idIblock
     * @param string $json
     * @return array
     */
    protected function getFields(int $idIblock, string $json): array
    {
        if($this->carrier['root'] === false) {
            return [
                "IBLOCK_ID" => $idIblock,
                "NAME" => $this->carrier['execution_request_uid'],
                "ACTIVE" => "Y",
                "PREVIEW_TEXT" => $json,
            ];
        }

        return [
            "IBLOCK_ID" => $idIblock,
            "NAME" => $this->carrier['execution_request_uid'],
            "ACTIVE" => "Y",
            "DETAIL_TEXT" => $json,
        ];
    }

    /**
     * Возвращаем свойства элемента
     *
     * @return array
     */
    public function getPropertyList(): array
    {
        //Дата погрузки
        $properties['DATE_SHIPMENT'] = $this->carrier['loading_date'];
        //Статус перевозки
        $properties['STATUS_SHIPPING'] = $this->carrier['status'];

        if ($this->carrier['root'] === false) {
            $properties = self::getChecksForwarder();
        } else {
            $properties = self::getChecks();
        }

        foreach ($this->carrier['check_groups'] as $check_group) {
            $countChecks = count($check_group['checks']);
            $checksTrue = $checksFalse = 0;

            foreach ($check_group['checks'] as $checks) {
                if ($checks['status'] === 'passed') {
                    $checksTrue++;
                } else {
                    $checksFalse++;
                }

                if ($this->carrier['root'] === false) {
                    $properties = self::setChecksForwarder($check_group['name'], $checks, $properties, $checksTrue, $countChecks, $check_group['meta']['license_plate']);
                } else {
                    $properties = self::setChecks($check_group['name'], $checks, $properties, $checksTrue, $countChecks, $check_group['meta']['license_plate']);
                }
            }
        }

        if ($this->carrier['root'] === false) {
            $properties['CHECKLIST_FORWARDER'] = false;
            if ($checksTrue === $checksFalse) {
                $properties['CHECKLIST_FORWARDER'] = true;
            }

            $properties['CARRIER'] = $this->carrier['executor']['name'];
            $properties['CARRIER_INN'] = $this->carrier['executor']['inn'];
            $properties['FORWARDER'] = $this->carrier['customer']['name'];
            $properties['FORWARDER_INN'] = $this->carrier['customer']['inn'];
        } else {
            $properties['CARRIER'] = $this->carrier['executor']['name'];
            $properties['CARRIER_INN'] = $this->carrier['executor']['inn'];
            $properties['CARGO_OWNER'] = $this->carrier['customer']['name'];
            $properties['CARGO_OWNER_INN'] = $this->carrier['customer']['inn'];

            $properties['CHECKLIST_CARRIER'] = false;
            if ($checksTrue === $checksFalse) {
                $properties['CHECKLIST_CARRIER'] = true;
            }
        }

        return $properties;
    }

    /**
     * Возвращаем дополнительные свойства
     * статусов, чеклистов для перевозчика
     *
     * @return array
     */
    protected static function getChecks(): array
    {
        return [
            'CONTRACT_EXPEDITION_STATUS' => '',
            'CONTRACT_TRANSPORTATION_STATUS' => '',
            'CONTRACT_ORDER_ONE_TIME_STATUS' => '',
            'DOCUMENTS_EPD_STATUS' => '',
            'DOCUMENTS_EXPEDITOR_STATUS' => '',
            'DOCUMENTS_EXPEDITOR_RECEIPT_STATUS' => '',
            'DOCUMENTS_DRIVER_APPROVALS_STATUS' => '',
            'DOCUMENTS_APPLICATION_TRANSPORTATION_STATUS' => '',
            'AUTOMATIC_PRICES_STATUS' => '',
            'AUTOMATIC_GEO_MONITORING_STATUS' => '',
            'ACCOUNTING_INVOICE_STATUS' => '',
            'ACCOUNTING_ACT_ACCEPTANCE_STATUS' => '',
            'ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS' => '',
            'ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS' => '',
            'ACCOUNTING_TAX_INVOICE_STATUS' => '',
            'ACCOUNTING_UPD_STATUS' => '',
            'DONKEY_STS_STATUS' => '',
            'DONKEY_RENT_AGREEMENT_STATUS' => '',
            'DONKEY_LEASING_COMPANY_STATUS' => '',
            'DONKEY_MARRIAGE_CERTIFICATE_STATUS' => '',
            'DONKEY_FREE_USAGE_STATUS' => '',
            'TRAILER_STS_STATUS' => '',
            'TRAILER_RENT_AGREEMENT_STATUS' => '',
            'TRAILER_LEASING_COMPANY_STATUS' => '',
            'TRAILER_MARRIAGE_CERTIFICATE_STATUS' => '',
            'TRAILER_FREE_USAGE_STATUS' => '',
            'TRAILER_SECONDARY_STS_STATUS' => '',
            'TRAILER_SECONDARY_RENT_AGREEMENT_STATUS' => '',
            'TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_STATUS' => '',
            'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_STATUS' => '',
            'TRAILER_SECONDARY_FREE_USAGE_STATUS' => '',
            'TRUCK_STS_STATUS' => '',
            'TRUCK_RENT_AGREEMENT_STATUS' => '',
            'TRUCK_AGREEMENT_LEASING_COMPANY_STATUS' => '',
            'TRUCK_MARRIAGE_CERTIFICATE_STATUS' => '',
            'TRUCK_FREE_USAGE_STATUS' => '',
        ];
    }

    /**
     * Возвращаем дополнительные свойства
     * статусов, чеклистов для экспедитора
     *
     * @return array
     */
    protected static function getChecksForwarder(): array
    {
        return [
            'CONTRACT_EXPEDITION_FOR_STATUS' => '',
            'CONTRACT_TRANSPORTATION_FOR_STATUS' => '',
            'CONTRACT_ORDER_ONE_TIME_FOR_STATUS' => '',
            'DOCUMENTS_EPD_FOR_STATUS' => '',
            'DOCUMENTS_EXPEDITOR_FOR_STATUS' => '',
            'DOCUMENTS_EXPEDITOR_RECEIPT_FOR_STATUS' => '',
            'DOCUMENTS_DRIVER_APPROVALS_FOR_STATUS' => '',
            'DOCUMENTS_APPLICATION_TRANSPORT_FOR_STATUS' => '',
            'AUTOMATIC_PRICES_FOR_STATUS' => '',
            'AUTOMATIC_GEO_MONITORING_FOR_STATUS' => '',
            'ACCOUNTING_INVOICE_FOR_STATUS' => '',
            'ACCOUNTING_ACT_ACCEPTANCE_FOR_STATUS' => '',
            'ACCOUNTING_ACT_MULTIPLE_TRANSPORT_FOR_STATUS' => '',
            'ACCOUNTING_TRANSPORTATION_REGISTRY_FOR_STATUS' => '',
            'ACCOUNTING_TAX_INVOICE_FOR_STATUS' => '',
            'ACCOUNTING_UPD_FOR_STATUS' => '',
            'DONKEY_STS_FOR_STATUS' => '',
            'DONKEY_RENT_FOR_STATUS' => '',
            'DONKEY_LEASING_COMPANY_FOR_STATUS' => '',
            'DONKEY_MARRIAGE_CERTIFICATE_FOR_STATUS' => '',
            'DONKEY_FREE_USAGE_FOR_STATUS' => '',
            'TRAILER_STS_FOR_STATUS' => '',
            'TRAILER_RENT_AGREEMENT_FOR_STATUS' => '',
            'TRAILER_LEASING_COMPANY_FOR_STATUS' => '',
            'TRAILER_MARRIAGE_CERTIFICATE_FOR_STATUS' => '',
            'TRAILER_FREE_USAGE_FOR_STATUS' => '',
            'TRAILER_SECONDARY_STS_FOR_STATUS' => '',
            'TRAILER_SECONDARY_RENT_AGREEMENT_FOR_STATUS' => '',
            'TRAILER_SECONDARY_LEASING_COMPANY_FOR_STATUS' => '',
            'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_FOR_STATUS' => '',
            'TRAILER_SECONDARY_FREE_USAGE_FOR_STATUS' => '',
            'TRUCK_STS_FOR_STATUS' => '',
            'TRUCK_RENT_AGREEMENT_FOR_STATUS' => '',
            'TRUCK_AGREEMENT_LEASING_COMPANY_FOR_STATUS' => '',
            'TRUCK_MARRIAGE_CERTIFICATE_FOR_STATUS' => '',
            'TRUCK_FREE_USAGE_FOR_STATUS' => '',
        ];
    }

    /**
     * Устанавливаем дополнительные свойства
     * статусов, чеклистов для перевозчика
     *
     * @param string $name
     * @param array $group
     * @param array $properties
     * @param int $checksTrue
     * @param int $countChecks
     * @param string|null $licensePlate
     * @return array
     */
    protected static function setChecks(
        string $name,
        array $group,
        array $properties,
        int $checksTrue,
        int $countChecks,
        ?string $licensePlate = null
    ): array
    {
        switch ($name) {
            case 'contract':
                $properties['CONTRACT_CHECK'] = $checksTrue . '/' .$countChecks;
                if ($group['name'] === 'transport_expedition_contract') {
                    $properties['CONTRACT_EXPEDITION_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'transportation_contract') {
                    $properties['CONTRACT_TRANSPORTATION_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'order_one_time_contract') {
                    $properties['CONTRACT_ORDER_ONE_TIME_STATUS'] = $group['status'];
                }
                break;
            case 'execution_documents':
                $properties['DOCUMENTS_CHECK'] = $checksTrue . '/' .$countChecks;
                if ($group['name'] === 'epd') {
                    $properties['DOCUMENTS_EPD_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'expeditor_order') {
                    $properties['DOCUMENTS_EXPEDITOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'expeditor_agent_receipt') {
                    $properties['DOCUMENTS_EXPEDITOR_RECEIPT_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'driver_approvals') {
                    $properties['DOCUMENTS_DRIVER_APPROVALS_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'application_for_transportation') {
                    $properties['DOCUMENTS_APPLICATION_TRANSPORTATION_STATUS'] = $group['status'];
                }
                break;
            case 'automatic_checks':
                $properties['AUTOMATIC_CHECKS'] = $checksTrue . '/' .$countChecks;
                if ($group['name'] === 'prices') {
                    $properties['AUTOMATIC_PRICES_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'geo_monitoring') {
                    $properties['AUTOMATIC_GEO_MONITORING_STATUS'] = $group['status'];
                }
                break;
            case 'accounting':
                $properties['ACCOUNTING_CHECKS'] = $checksTrue . '/' .$countChecks;
                if ($group['name'] === 'invoice') {
                    $properties['ACCOUNTING_INVOICE_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'act_of_service_acceptance') {
                    $properties['ACCOUNTING_ACT_ACCEPTANCE_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'act_of_service_acceptance_multiple_transportations') {
                    $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'transportation_registry') {
                    $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'tax_invoice') {
                    $properties['ACCOUNTING_TAX_INVOICE_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'universal_transfer_document') {
                    $properties['ACCOUNTING_UPD_STATUS'] = $group['status'];
                }
                break;
            case 'vehicle_donkey':
                $properties['DONKEY_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['DONKEY_LICENSE_PLATE'] = $licensePlate;
                if ($group['name'] === 'sts') {
                    $properties['DONKEY_STS_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['DONKEY_RENT_AGREEMENT_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'agreement_with_leasing_company') {
                    $properties['DONKEY_LEASING_COMPANY_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'marriage_certificate') {
                    $properties['DONKEY_MARRIAGE_CERTIFICATE_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'free_usage_agreement') {
                    $properties['DONKEY_FREE_USAGE_STATUS'] = $group['status'];
                }
                break;
            case 'vehicle_main_trailer':
                $properties['TRAILER_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['TRAILER_LICENSE_PLATE'] = $licensePlate;
                if ($group['name'] === 'sts') {
                    $properties['TRAILER_STS_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRAILER_RENT_AGREEMENT_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'agreement_with_leasing_company') {
                    $properties['TRAILER_LEASING_COMPANY_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'marriage_certificate') {
                    $properties['TRAILER_MARRIAGE_CERTIFICATE_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'free_usage_agreement') {
                    $properties['TRAILER_FREE_USAGE_STATUS'] = $group['status'];
                }
                break;
            case 'vehicle_secondary_trailer':
                $properties['TRAILER_SECONDARY_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['TRAILER_SECONDARY_LICENSE_PLATE'] = $licensePlate;
                if ($group['name'] === 'sts') {
                    $properties['TRAILER_SECONDARY_STS_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRAILER_SECONDARY_RENT_AGREEMENT_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'agreement_with_leasing_company') {
                    $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'marriage_certificate') {
                    $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'free_usage_agreement') {
                    $properties['TRAILER_SECONDARY_FREE_USAGE_STATUS'] = $group['status'];
                }
                break;
            case 'vehicle_truck':
                $properties['TRUCK_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['TRUCK_LICENSE_PLATE'] = $licensePlate;
                if ($group['name'] === 'sts') {
                    $properties['TRUCK_STS_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRUCK_RENT_AGREEMENT_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'agreement_with_leasing_company') {
                    $properties['TRUCK_AGREEMENT_LEASING_COMPANY_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'marriage_certificate') {
                    $properties['TRUCK_MARRIAGE_CERTIFICATE_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'free_usage_agreement') {
                    $properties['TRUCK_FREE_USAGE_STATUS'] = $group['status'];
                }
                break;
        }

        return $properties;
    }

    /**
     * Устанавливаем дополнительные свойства
     * статусов, чеклистов для экспедитора
     *
     * @param string $name
     * @param array $group
     * @param array $properties
     * @param int $checksTrue
     * @param int $countChecks
     * @param string|null $licensePlate
     * @return array
     */
    protected static function setChecksForwarder(
        string $name,
        array $group,
        array $properties,
        int $checksTrue,
        int $countChecks,
        ?string $licensePlate = null
    ): array
    {
        switch ($name) {
            case 'contract':
                $properties['CONTRACT_FOR_CHECK'] = $checksTrue . '/' .$countChecks;
                if ($group['name'] === 'transport_expedition_contract') {
                    $properties['CONTRACT_EXPEDITION_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'transportation_contract') {
                    $properties['CONTRACT_TRANSPORTATION_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'order_one_time_contract') {
                    $properties['CONTRACT_ORDER_ONE_TIME_FOR_STATUS'] = $group['status'];
                }
                break;
            case 'execution_documents':
                $properties['DOCUMENTS_FOR_CHECK'] = $checksTrue . '/' .$countChecks;
                if ($group['name'] === 'epd') {
                    $properties['DOCUMENTS_EPD_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'expeditor_order') {
                    $properties['DOCUMENTS_EXPEDITOR_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'expeditor_agent_receipt') {
                    $properties['DOCUMENTS_EXPEDITOR_RECEIPT_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'driver_approvals') {
                    $properties['DOCUMENTS_DRIVER_APPROVALS_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'application_for_transportation') {
                    $properties['DOCUMENTS_APPLICATION_TRANSPORT_FOR_STATUS'] = $group['status'];
                }
                break;
            case 'automatic_checks':
                $properties['AUTOMATIC_FOR_CHECKS'] = $checksTrue . '/' .$countChecks;
                if ($group['name'] === 'prices') {
                    $properties['AUTOMATIC_PRICES_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'geo_monitoring') {
                    $properties['AUTOMATIC_GEO_MONITORING_FOR_STATUS'] = $group['status'];
                }
                break;
            case 'accounting':
                $properties['ACCOUNTING_FOR_CHECKS'] = $checksTrue . '/' .$countChecks;
                if ($group['name'] === 'invoice') {
                    $properties['ACCOUNTING_INVOICE_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'act_of_service_acceptance') {
                    $properties['ACCOUNTING_ACT_ACCEPTANCE_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'act_of_service_acceptance_multiple_transportations') {
                    $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORT_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'transportation_registry') {
                    $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'tax_invoice') {
                    $properties['ACCOUNTING_TAX_INVOICE_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'universal_transfer_document') {
                    $properties['ACCOUNTING_UPD_FOR_STATUS'] = $group['status'];
                }
                break;
            case 'vehicle_donkey':
                $properties['DONKEY_FOR_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['DONKEY_LICENSE_FOR_PLATE'] = $licensePlate;
                if ($group['name'] === 'sts') {
                    $properties['DONKEY_STS_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['DONKEY_RENT_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'agreement_with_leasing_company') {
                    $properties['DONKEY_LEASING_COMPANY_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'marriage_certificate') {
                    $properties['DONKEY_MARRIAGE_CERTIFICATE_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'free_usage_agreement') {
                    $properties['DONKEY_FREE_USAGE_FOR_STATUS'] = $group['status'];
                }
                break;
            case 'vehicle_main_trailer':
                $properties['TRAILER_FOR_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['TRAILER_LICENSE_FOR_PLATE'] = $licensePlate;
                if ($group['name'] === 'sts') {
                    $properties['TRAILER_STS_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRAILER_RENT_AGREEMENT_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'agreement_with_leasing_company') {
                    $properties['TRAILER_LEASING_COMPANY_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'marriage_certificate') {
                    $properties['TRAILER_MARRIAGE_CERTIFICATE_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'free_usage_agreement') {
                    $properties['TRAILER_FREE_USAGE_FOR_STATUS'] = $group['status'];
                }
                break;
            case 'vehicle_secondary_trailer':
                $properties['TRAILER_SECONDARY_FOR_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['TRAILER_SECONDARY_LICENSE_FOR_PLATE'] = $licensePlate;
                if ($group['name'] === 'sts') {
                    $properties['TRAILER_SECONDARY_STS_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRAILER_SECONDARY_RENT_AGREEMENT_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'agreement_with_leasing_company') {
                    $properties['TRAILER_SECONDARY_LEASING_COMPANY_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'marriage_certificate') {
                    $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'free_usage_agreement') {
                    $properties['TRAILER_SECONDARY_FREE_USAGE_FOR_STATUS'] = $group['status'];
                }
                break;
            case 'vehicle_truck':
                $properties['TRUCK_FOR_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['TRUCK_LICENSE_FOR_PLATE'] = $licensePlate;
                if ($group['name'] === 'sts') {
                    $properties['TRUCK_STS_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRUCK_RENT_AGREEMENT_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'agreement_with_leasing_company') {
                    $properties['TRUCK_AGREEMENT_LEASING_COMPANY_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'marriage_certificate') {
                    $properties['TRUCK_MARRIAGE_CERTIFICATE_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'free_usage_agreement') {
                    $properties['TRUCK_FREE_USAGE_FOR_STATUS'] = $group['status'];
                }
                break;
        }

        return $properties;
    }

    /**
     * Проверяем существование компании
     *
     * @param string $name
     * @return string|null
     */
    protected static function getIdCarrier(string $name): ?string
    {
        $carrier = \Bitrix\Iblock\Elements\ElementVitrinaApiTable::getList([
            'filter' => ['NAME' => $name],
            'select' => ['ID'],
        ])->fetch();

        return $carrier['ID'];
    }

    /**
     * Сохраняем ссылки в справочник
     *
     * @return void
     * @throws Exception
     */
    protected function setLink($idElement): void
    {
        Loader::includeModule("highloadblock");

        if($this->carrier['root'] === false) {
            $nameHLBlock = 'FnsLinkDocumentsForwardes';
        } else {
            $nameHLBlock = 'FnsLinkDocuments';
        }

        $fields = [];
        
        foreach ($this->carrier['check_groups'] as $group) {
            foreach ($group['checks'] as $check) {
                $nameLink = '';
                $link = '';
                $ufAttachments = '';
                $ufEdmAttachments = '';

                foreach ($check['attachments'] as $attachment) {
                    $nameLink = $attachment['name'];
                    $link = $attachment['url'];
                    $ufAttachments = true;
                    $ufEdmAttachments = false;
                }
                foreach ($check['edm_attachments'] as $edm_attachment) {
                    $nameLink = $edm_attachment['name'];
                    $link = $edm_attachment['original_file'];
                    $ufAttachments = false;
                    $ufEdmAttachments = true;
                }

                if ($check['name'] === 'prices') {
                    $nameLink = '';
                    $link = $check['results']['diff_percentage'];
                    $ufAttachments = false;
                    $ufEdmAttachments = false;
                }

                if ($check['name'] === 'geo_monitoring') {
                    $nameLink = '';
                    $link = $check['results']['diff_percentage'];
                    $ufAttachments = false;
                    $ufEdmAttachments = false;
                }

                $fields[] = [
                    'UF_ID_ELEMENT' => $idElement,
                    'UF_ID_GROUP' => $group['name'],
                    'UF_GROUP_NAME' => $check['name'],
                    'UF_NAME_LINK' => $nameLink,
                    'UF_LINK' => $link,
                    'UF_ATTACHMENTS' => $ufAttachments,
                    'UF_EDM_ATTACHMENTS' => $ufEdmAttachments,
                ];
            }
        }

        $hlblockId = HL\HighloadBlockTable::getList([
            'filter' => ['=NAME' => $nameHLBlock]
        ])->fetch();

        $entity_data_class = (HL\HighloadBlockTable::compileEntity($hlblockId))->getDataClass();

        foreach ($fields as $item) {
            $data = $entity_data_class::getList([
                "select" => ["*"],
                "filter" => [
                    "UF_ID_ELEMENT" => $idElement,
                    "UF_ID_GROUP" => $item['UF_ID_GROUP'],
                    "UF_GROUP_NAME" => $item['UF_GROUP_NAME'],
                    "UF_NAME_LINK" => $item['UF_NAME_LINK'],
                ]
            ])->Fetch();

            if ($data) {
                $entity_data_class::update($data['ID'], $item);
            } else {
                $entity_data_class::add($item);
            }
        }
    }
}