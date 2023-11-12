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
            $id = $element->Add($this->getFields($this->carrier, $idIblock, $this->json));
        } else {
            $element->update($id, $this->getFields($this->carrier, $idIblock, $this->json));
        }

        $element::SetPropertyValuesEx($id, $idIblock, self::getPropertyList($this->carrier));

        self::setLink($id, $this->carrier['check_groups'], $this->carrier['root']);
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
     * @param array $carrier
     * @param int $idIblock
     * @param string $json
     * @return array
     */
    protected function getFields(array $carrier, int $idIblock, string $json): array
    {
        if($this->carrier['root'] === true) {
            return [
                "IBLOCK_ID" => $idIblock,
                "NAME" => $carrier['execution_request_uid'],
                "ACTIVE" => "Y",
                "PREVIEW_TEXT" => $json,
            ];
        }

        return [
            "IBLOCK_ID" => $idIblock,
            "NAME" => $carrier['execution_request_uid'],
            "ACTIVE" => "Y",
            "DETAIL_TEXT" => $json,
        ];
    }

    /**
     * Возвращаем свойства элемента
     *
     * @param array $carrier
     * @return array
     */
    public static function getPropertyList(array $carrier): array
    {
        //Чек-лист с экспедиторов
        $properties['CHECKLIST_FORWARDER'] = false;
        //Чек-лист с перевозчиком
        $properties['CHECKLIST_CARRIER'] = false;
        $properties['FORWARDER'] = '';
        $properties['FORWARDER_INN'] = '';
        //Дата погрузки
        $properties['DATE_SHIPMENT'] = $carrier['loading_date'];
        //Статус перевозки
        $properties['STATUS_SHIPPING'] = $carrier['status'];
        //Чеклист данных перевозчика
        $properties['CONTRACT_CHECK'] = '';
        $properties['DOCUMENTS_CHECK'] = '';
        $properties['AUTOMATIC_CHECKS'] = '';
        $properties['ACCOUNTING_CHECKS'] = '';
        $properties['DONKEY_CHECKS'] = '';
        $properties['TRAILER_CHECKS'] = '';
        $properties['TRAILER_SECONDARY_CHECKS'] = '';
        $properties['TRUCK_CHECKS'] = '';
        //Чеклист данных экспедитора
        $properties['CONTRACT_FOR_CHECK'] = '';
        $properties['DOCUMENTS_FOR_CHECK'] = '';
        $properties['AUTOMATIC_FOR_CHECKS'] = '';
        $properties['ACCOUNTING_FOR_CHECKS'] = '';
        $properties['DONKEY_FOR_CHECKS'] = '';
        $properties['TRAILER_FOR_CHECKS'] = '';
        $properties['TRAILER_SECONDARY_FOR_CHECKS'] = '';
        $properties['TRUCK_FOR_CHECKS'] = '';
        //Статусы перевозчика
        $properties['CONTRACT_EXPEDITION_STATUS'] = '';
        $properties['CONTRACT_TRANSPORTATION_STATUS'] = '';
        $properties['CONTRACT_ORDER_ONE_TIME_STATUS'] = '';
        $properties['DOCUMENTS_EPD_STATUS'] = '';
        $properties['DOCUMENTS_EXPEDITOR_STATUS'] = '';
        $properties['DOCUMENTS_EXPEDITOR_RECEIPT_STATUS'] = '';
        $properties['DOCUMENTS_DRIVER_APPROVALS_STATUS'] = '';
        $properties['DOCUMENTS_APPLICATION_TRANSPORTATION_STATUS'] = '';
        $properties['ACCOUNTING_INVOICE_STATUS'] = '';
        $properties['ACCOUNTING_ACT_ACCEPTANCE_STATUS'] = '';
        $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS'] = '';
        $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS'] = '';
        $properties['ACCOUNTING_TAX_INVOICE_STATUS'] = '';
        $properties['ACCOUNTING_UPD_STATUS'] = '';
        $properties['DONKEY_STS_STATUS'] = '';
        $properties['DONKEY_RENT_AGREEMENT_STATUS'] = '';
        $properties['TRAILER_STS_STATUS'] = '';
        $properties['TRAILER_RENT_AGREEMENT_STATUS'] = '';
        $properties['TRAILER_SECONDARY_STS_STATUS'] = '';
        $properties['TRAILER_SECONDARY_RENT_AGREEMENT_STATUS'] = '';
        $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_STATUS'] = '';
        $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_STATUS'] = '';
        $properties['TRAILER_SECONDARY_FREE_USAGE_STATUS'] = '';
        $properties['TRUCK_STS_STATUS'] = '';
        $properties['TRUCK_RENT_AGREEMENT_STATUS'] = '';
        $properties['TRUCK_AGREEMENT_LEASING_COMPANY_STATUS'] = '';
        $properties['TRUCK_MARRIAGE_CERTIFICATE_STATUS'] = '';
        $properties['TRUCK_FREE_USAGE_STATUS'] = '';
        //Статусы экспедитора
        $properties['CONTRACT_EXPEDITION_FOR_STATUS'] = '';
        $properties['CONTRACT_TRANSPORTATION_FOR_STATUS'] = '';
        $properties['CONTRACT_ORDER_ONE_TIME_FOR_STATUS'] = '';
        $properties['DOCUMENTS_EPD_FOR_STATUS'] = '';
        $properties['DOCUMENTS_EXPEDITOR_FOR_STATUS'] = '';
        $properties['DOCUMENTS_EXPEDITOR_RECEIPT_FOR_STATUS'] = '';
        $properties['DOCUMENTS_DRIVER_APPROVALS_FOR_STATUS'] = '';
        $properties['DOCUMENTS_APPLICATION_TRANSPORT_FOR_STATUS'] = '';
        $properties['AUTOMATIC_PRICES_FOR_STATUS'] = '';
        $properties['AUTOMATIC_GEO_MONITORING_FOR_STATUS'] = '';
        $properties['ACCOUNTING_INVOICE_FOR_STATUS'] = '';
        $properties['ACCOUNTING_ACT_ACCEPTANCE_FOR_STATUS'] = '';
        $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORT_FOR_STATUS'] = '';
        $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_FOR_STATUS'] = '';
        $properties['ACCOUNTING_TAX_INVOICE_FOR_STATUS'] = '';
        $properties['ACCOUNTING_UPD_FOR_STATUS'] = '';
        $properties['DONKEY_STS_FOR_STATUS'] = '';
        $properties['DONKEY_RENT_FOR_STATUS'] = '';
        $properties['TRAILER_STS_FOR_STATUS'] = '';
        $properties['TRAILER_RENT_AGREEMENT_FOR_STATUS'] = '';
        $properties['TRAILER_SECONDARY_STS_FOR_STATUS'] = '';
        $properties['TRAILER_SECONDARY_RENT_AGREEMENT_FOR_STATUS'] = '';
        $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_FOR_STATUS'] = '';
        $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_FOR_STATUS'] = '';
        $properties['TRAILER_SECONDARY_FREE_USAGE_FOR_STATUS'] = '';
        $properties['TRUCK_STS_FOR_STATUS'] = '';
        $properties['TRUCK_RENT_AGREEMENT_FOR_STATUS'] = '';
        $properties['TRUCK_AGREEMENT_LEASING_COMPANY_FOR_STATUS'] = '';
        $properties['TRUCK_MARRIAGE_CERTIFICATE_FOR_STATUS'] = '';
        $properties['TRUCK_FREE_USAGE_FOR_STATUS'] = '';
        //Номерные знаки перевозчика
        $properties['DONKEY_LICENSE_PLATE'] = '';
        $properties['TRAILER_LICENSE_PLATE'] = '';
        $properties['TRAILER_SECONDARY_LICENSE_PLATE'] = '';
        $properties['TRUCK_LICENSE_PLATE'] = '';
        //Номерные знаки экспедитора
        $properties['DONKEY_LICENSE_FOR_PLATE'] = '';
        $properties['TRAILER_LICENSE_FOR_PLATE'] = '';
        $properties['TRAILER_SECONDARY_LICENSE_FOR_PLATE'] = '';
        $properties['TRUCK_LICENSE_FOR_PLATE'] = '';

        if ($carrier['root'] === true) {
            $properties['CHECKLIST_FORWARDER'] = true;

            $shipping = self::getCarrier($carrier['execution_request_uid']);

            $properties['CARRIER'] = $shipping['CARRIER_VALUE'];
            $properties['CARRIER_INN'] = $shipping['CARRIER_INN_VALUE'];
            $properties['CARGO_OWNER'] = $carrier['executor']['name'];
            $properties['CARGO_OWNER_INN'] = $carrier['executor']['inn'];
            $properties['FORWARDER'] = $carrier['customer']['name'];
            $properties['FORWARDER_INN'] = $carrier['customer']['inn'];
            //Чеклист данных перевозчика
            $properties['CONTRACT_CHECK'] = $shipping['CONTRACT_CHECK_VALUE'];
            $properties['DOCUMENTS_CHECK'] = $shipping['DOCUMENTS_CHECK_VALUE'];
            $properties['AUTOMATIC_CHECKS'] = $shipping['AUTOMATIC_CHECKS_VALUE'];
            $properties['ACCOUNTING_CHECKS'] = $shipping['ACCOUNTING_CHECKS_VALUE'];
            $properties['DONKEY_CHECKS'] = $shipping['DONKEY_CHECKS_VALUE'];
            $properties['TRAILER_CHECKS'] = $shipping['TRAILER_CHECKS_VALUE'];
            $properties['TRAILER_SECONDARY_CHECKS'] = $shipping['TRAILER_SECONDARY_CHECKS_VALUE'];
            $properties['TRUCK_CHECKS'] = $shipping['TRUCK_CHECKS_VALUE'];
            //Статусы перевозчика
            $properties['CONTRACT_EXPEDITION_STATUS'] = $shipping['CONTRACT_EXPEDITION_STATUS_VALUE'];
            $properties['CONTRACT_TRANSPORTATION_STATUS'] = $shipping['CONTRACT_TRANSPORTATION_STATUS_VALUE'];
            $properties['CONTRACT_ORDER_ONE_TIME_STATUS'] = $shipping['CONTRACT_ORDER_ONE_TIME_STATUS_VALUE'];
            $properties['DOCUMENTS_EPD_STATUS'] = $shipping['DOCUMENTS_EPD_STATUS_VALUE'];
            $properties['DOCUMENTS_EXPEDITOR_STATUS'] = $shipping['DOCUMENTS_EXPEDITOR_STATUS_VALUE'];
            $properties['DOCUMENTS_EXPEDITOR_RECEIPT_STATUS'] = $shipping['DOCUMENTS_EXPEDITOR_RECEIPT_STATUS_VALUE'];
            $properties['DOCUMENTS_DRIVER_APPROVALS_STATUS'] = $shipping['DOCUMENTS_DRIVER_APPROVALS_STATUS_VALUE'];
            $properties['DOCUMENTS_APPLICATION_TRANSPORTATION_STATUS'] = $shipping['DOCUMENTS_APPLICATION_TRANSPORTATION_STATUS_VALUE'];
            $properties['ACCOUNTING_INVOICE_STATUS'] = $shipping['ACCOUNTING_INVOICE_STATUS_VALUE'];
            $properties['ACCOUNTING_ACT_ACCEPTANCE_STATUS'] = $shipping['ACCOUNTING_ACT_ACCEPTANCE_STATUS_VALUE'];
            $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS'] = $shipping['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS_VALUE'];
            $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS'] = $shipping['ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS_VALUE'];
            $properties['ACCOUNTING_TAX_INVOICE_STATUS'] = $shipping['ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS_VALUE'];
            $properties['ACCOUNTING_UPD_STATUS'] = $shipping['ACCOUNTING_UPD_STATUS_VALUE'];
            $properties['DONKEY_STS_STATUS'] = $shipping['DONKEY_STS_STATUS_VALUE'];
            $properties['DONKEY_RENT_AGREEMENT_STATUS'] = $shipping['DONKEY_RENT_AGREEMENT_STATUS_VALUE'];
            $properties['TRAILER_STS_STATUS'] = $shipping['TRAILER_STS_STATUS_VALUE'];
            $properties['TRAILER_RENT_AGREEMENT_STATUS'] = $shipping['TRAILER_RENT_AGREEMENT_STATUS_VALUE'];
            $properties['TRAILER_SECONDARY_STS_STATUS'] = $shipping['TRAILER_SECONDARY_STS_STATUS_VALUE'];
            $properties['TRAILER_SECONDARY_RENT_AGREEMENT_STATUS'] = $shipping['TRAILER_SECONDARY_RENT_AGREEMENT_STATUS_VALUE'];
            $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_STATUS'] = $shipping['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_STATUS_VALUE'];
            $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_STATUS'] = $shipping['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_STATUS_VALUE'];
            $properties['TRAILER_SECONDARY_FREE_USAGE_STATUS'] = $shipping['TRAILER_SECONDARY_FREE_USAGE_STATUS_VALUE'];
            $properties['TRUCK_STS_STATUS'] = $shipping['TRUCK_STS_STATUS_VALUE'];
            $properties['TRUCK_RENT_AGREEMENT_STATUS'] = $shipping['TRUCK_RENT_AGREEMENT_STATUS_VALUE'];
            $properties['TRUCK_AGREEMENT_LEASING_COMPANY_STATUS'] = $shipping['TRUCK_AGREEMENT_LEASING_COMPANY_STATUS_VALUE'];
            $properties['TRUCK_MARRIAGE_CERTIFICATE_STATUS'] = $shipping['TRUCK_MARRIAGE_CERTIFICATE_STATUS_VALUE'];
            $properties['TRUCK_FREE_USAGE_STATUS'] = $shipping['TRUCK_FREE_USAGE_STATUS_VALUE'];
            //Номерные знаки перевозчика
            $properties['DONKEY_LICENSE_PLATE'] = $shipping['DONKEY_LICENSE_PLATE_VALUE'];
            $properties['TRAILER_LICENSE_PLATE'] = $shipping['TRAILER_LICENSE_PLATE_VALUE'];
            $properties['TRAILER_SECONDARY_LICENSE_PLATE'] = $shipping['TRAILER_SECONDARY_LICENSE_PLATE_VALUE'];
            $properties['TRUCK_LICENSE_PLATE'] = $shipping['TRUCK_LICENSE_PLATE_VALUE'];
        } else {
            $properties['CARRIER'] = $carrier['executor']['name'];
            $properties['CARRIER_INN'] = $carrier['executor']['inn'];
            $properties['CARGO_OWNER'] = $carrier['customer']['name'];
            $properties['CARGO_OWNER_INN'] = $carrier['customer']['inn'];
        }

        foreach ($carrier['check_groups'] as $check_group) {
            $countChecks = count($check_group['checks']);
            $checksTrue = $checksFalse = 0;

            foreach ($check_group['checks'] as $checks) {
                if ($checks['status'] === 'passed') {
                    $checksTrue++;
                } else {
                    $checksFalse++;
                }

                if ($carrier['root'] === true) {
                    $properties = self::setChecksForwarder($check_group['name'], $checks, $properties, $checksTrue, $countChecks);
                } else {
                    $properties = self::setChecks($check_group['name'], $checks, $properties, $checksTrue, $countChecks);
                }
            }
        }

        if ($checksTrue === $checksFalse) {
            $properties['CHECKLIST_CARRIER'] = true;
        }

        return $properties;
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
     * @return array
     */
    protected static function setChecks(
        string $name,
        array $group,
        array $properties,
        int $checksTrue,
        int $countChecks
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
                $properties['DONKEY_LICENSE_PLATE'] = $group['meta']['license_plate'];
                if ($group['name'] === 'sts') {
                    $properties['DONKEY_STS_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['DONKEY_RENT_AGREEMENT_STATUS'] = $group['status'];
                }
                break;
            case 'vehicle_main_trailer':
                $properties['TRAILER_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['TRAILER_LICENSE_PLATE'] = $group['meta']['license_plate'];
                if ($group['name'] === 'sts') {
                    $properties['TRAILER_STS_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRAILER_RENT_AGREEMENT_STATUS'] = $group['status'];
                }
                break;
            case 'vehicle_secondary_trailer':
                $properties['TRAILER_SECONDARY_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['TRAILER_SECONDARY_LICENSE_PLATE'] = $group['meta']['license_plate'];
                if ($group['name'] === 'sts') {
                    $properties['TRAILER_SECONDARY_STS_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRAILER_SECONDARY_RENT_AGREEMENT_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'agreement_withLeasingCompany') {
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
                $properties['TRUCK_LICENSE_PLATE'] = $group['meta']['license_plate'];
                if ($group['name'] === 'sts') {
                    $properties['TRUCK_STS_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRUCK_RENT_AGREEMENT_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'agreement_withLeasingCompany') {
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

    protected static function setChecksForwarder(
        string $name,
        array $group,
        array $properties,
        int $checksTrue,
        int $countChecks
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
                $properties['DONKEY_LICENSE_FOR_PLATE'] = $group['meta']['license_plate'];
                if ($group['name'] === 'sts') {
                    $properties['DONKEY_STS_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['DONKEY_RENT_FOR_STATUS'] = $group['status'];
                }
                break;
            case 'vehicle_main_trailer':
                $properties['TRAILER_FOR_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['TRAILER_LICENSE_FOR_PLATE'] = $group['meta']['license_plate'];
                if ($group['name'] === 'sts') {
                    $properties['TRAILER_STS_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRAILER_RENT_AGREEMENT_FOR_STATUS'] = $group['status'];
                }
                break;
            case 'vehicle_secondary_trailer':
                $properties['TRAILER_SECONDARY_FOR_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['TRAILER_SECONDARY_LICENSE_FOR_PLATE'] = $group['meta']['license_plate'];
                if ($group['name'] === 'sts') {
                    $properties['TRAILER_SECONDARY_STS_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRAILER_SECONDARY_RENT_AGREEMENT_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'agreement_withLeasingCompany') {
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
                $properties['TRUCK_LICENSE_FOR_PLATE'] = $group['meta']['license_plate'];
                if ($group['name'] === 'sts') {
                    $properties['TRUCK_STS_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRUCK_RENT_AGREEMENT_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'agreement_withLeasingCompany') {
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
    protected static function setLink($idElement, $groups, $root)
    {
        Loader::includeModule("highloadblock");

        if($root === true) {
            $nameHLBlock = 'FnsLinkDocumentsForwardes';
        } else {
            $nameHLBlock = 'FnsLinkDocuments';
        }

        $fields = [];
        
        foreach ($groups as $group) {
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
                foreach ($check['edm_attachments'] as $attachment) {
                    $nameLink = $attachment['name'];
                    $link = $attachment['printed_form'];
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

    /**
     * Возвращаем грузовладельца
     *
     * @param string $name
     * @return array
     */
    protected static function getCarrier(string $name): array
    {
        return \Bitrix\Iblock\Elements\ElementVitrinaApiTable::getList([
            'filter' => ['NAME' => $name],
            'select' => [
                'ID',
                'CARRIER_VALUE' => 'CARRIER.VALUE',
                'CARRIER_INN_VALUE' => 'CARRIER_INN.VALUE',
                'CONTRACT_CHECK_VALUE' => 'CONTRACT_CHECK.VALUE',
                'DOCUMENTS_CHECK_VALUE' => 'DOCUMENTS_CHECK.VALUE',
                'AUTOMATIC_CHECKS_VALUE' => 'AUTOMATIC_CHECKS.VALUE',
                'ACCOUNTING_CHECKS_VALUE' => 'ACCOUNTING_CHECKS.VALUE',
                'DONKEY_CHECKS_VALUE' => 'DONKEY_CHECKS.VALUE',
                'TRAILER_CHECKS_VALUE' => 'TRAILER_CHECKS.VALUE',
                'TRAILER_SECONDARY_CHECKS_VALUE' => 'TRAILER_SECONDARY_CHECKS.VALUE',
                'TRUCK_CHECKS_VALUE' => 'TRUCK_CHECKS.VALUE',

                'CONTRACT_EXPEDITION_STATUS_VALUE' => 'CONTRACT_EXPEDITION_STATUS.VALUE',
                'CONTRACT_TRANSPORTATION_STATUS_VALUE' => 'CONTRACT_TRANSPORTATION_STATUS.VALUE',
                'CONTRACT_ORDER_ONE_TIME_STATUS_VALUE' => 'CONTRACT_ORDER_ONE_TIME_STATUS.VALUE',
                'DOCUMENTS_EPD_STATUS_VALUE' => 'DOCUMENTS_EPD_STATUS.VALUE',
                'DOCUMENTS_EXPEDITOR_STATUS_VALUE' => 'DOCUMENTS_EXPEDITOR_STATUS.VALUE',
                'DOCUMENTS_EXPEDITOR_RECEIPT_STATUS_VALUE' => 'DOCUMENTS_EXPEDITOR_RECEIPT_STATUS.VALUE',
                'DOCUMENTS_DRIVER_APPROVALS_STATUS_VALUE' => 'DOCUMENTS_DRIVER_APPROVALS_STATUS.VALUE',
                'DOCUMENTS_APPLICATION_TRANSPORTATION_STATUS_VALUE' => 'DOCUMENTS_APPLICATION_TRANSPORTATION_STATUS.VALUE',
                'ACCOUNTING_INVOICE_STATUS_VALUE' => 'ACCOUNTING_INVOICE_STATUS.VALUE',
                'ACCOUNTING_ACT_ACCEPTANCE_STATUS_VALUE' => 'ACCOUNTING_ACT_ACCEPTANCE_STATUS.VALUE',
                'ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS_VALUE' => 'ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS.VALUE',
                'ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS_VALUE' => 'ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS.VALUE',
                'ACCOUNTING_TAX_INVOICE_STATUS_VALUE' => 'ACCOUNTING_TAX_INVOICE_STATUS.VALUE',
                'ACCOUNTING_UPD_STATUS_VALUE' => 'ACCOUNTING_UPD_STATUS.VALUE',
                'DONKEY_STS_STATUS_VALUE' => 'DONKEY_STS_STATUS.VALUE',
                'DONKEY_RENT_AGREEMENT_STATUS_VALUE' => 'DONKEY_RENT_AGREEMENT_STATUS.VALUE',
                'TRAILER_STS_STATUS_VALUE' => 'TRAILER_STS_STATUS.VALUE',
                'TRAILER_RENT_AGREEMENT_STATUS_VALUE' => 'TRAILER_RENT_AGREEMENT_STATUS.VALUE',
                'TRAILER_SECONDARY_STS_STATUS_VALUE' => 'TRAILER_SECONDARY_STS_STATUS.VALUE',
                'TRAILER_SECONDARY_RENT_AGREEMENT_STATUS_VALUE' => 'TRAILER_SECONDARY_RENT_AGREEMENT_STATUS.VALUE',
                'TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_STATUS_VALUE' => 'TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_STATUS.VALUE',
                'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_STATUS_VALUE' => 'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_STATUS.VALUE',
                'TRAILER_SECONDARY_FREE_USAGE_STATUS_VALUE' => 'TRAILER_SECONDARY_FREE_USAGE_STATUS.VALUE',
                'TRUCK_STS_STATUS_VALUE' => 'TRUCK_STS_STATUS.VALUE',
                'TRUCK_RENT_AGREEMENT_STATUS_VALUE' => 'TRUCK_RENT_AGREEMENT_STATUS.VALUE',
                'TRUCK_AGREEMENT_LEASING_COMPANY_STATUS_VALUE' => 'TRUCK_AGREEMENT_LEASING_COMPANY_STATUS.VALUE',
                'TRUCK_MARRIAGE_CERTIFICATE_STATUS_VALUE' => 'TRUCK_MARRIAGE_CERTIFICATE_STATUS.VALUE',
                'TRUCK_FREE_USAGE_STATUS_VALUE' => 'TRUCK_FREE_USAGE_STATUS.VALUE',
                'DONKEY_LICENSE_PLATE_VALUE' => 'DONKEY_LICENSE_PLATE.VALUE',
                'TRAILER_LICENSE_PLATE_VALUE' => 'TRAILER_LICENSE_PLATE.VALUE',
                'TRAILER_SECONDARY_LICENSE_PLATE_VALUE' => 'TRAILER_SECONDARY_LICENSE_PLATE.VALUE',
                'TRUCK_LICENSE_PLATE_VALUE' => 'TRUCK_LICENSE_PLATE.VALUE',
            ],
        ])->fetch();
    }
}