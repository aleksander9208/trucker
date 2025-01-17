<?php

declare(strict_types=1);

namespace Taxcom\Library\Service;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use CIBlockElement;
use Exception;
use Taxcom\Library\Helper\Vitrina;

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

        $element::SetPropertyValuesEx($id, $idIblock, $this->getPropertyList((int) $id));

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
     * @param int|null $id
     * @return array
     */
    public function getPropertyList(?int $id = null): array
    {
        if ($this->carrier['root'] === false) {
            $properties = self::getChecksForwarder();
        } else {
            $properties = self::getChecks();
        }

        //Дата погрузки
        $properties['DATE_SHIPMENT'] = $this->carrier['loading_date'];

        $checksTrue = $checksFalse = 0;

        foreach ($this->carrier['check_groups'] as $check_group) {
            if ($check_group['passed'] === true) {
                $checksTrue++;
            } else {
                $checksFalse++;
            }

            foreach ($check_group['checks'] as $checks) {
                if ($this->carrier['root'] === false) {
                    $properties = self::setChecksForwarder($check_group['name'], $checks, $properties, (int) $check_group['passed'], $check_group['meta']['license_plate']);
                } else {
                    $properties = self::setChecks($check_group['name'], $checks, $properties, (int) $check_group['passed'], $check_group['meta']['license_plate']);
                }
            }
        }

        $element = Vitrina::getElement($id);

        if ($element['DETAIL_TEXT']) {
            $jsonTrue = json_decode($element['DETAIL_TEXT'], true);
        }
        if ($element['PREVIEW_TEXT']) {
            $jsonFalse = json_decode($element['PREVIEW_TEXT'], true);
        }

        if ($jsonTrue) {
            $properties['CARRIER'] = $jsonTrue['executor']['name'];
            $properties['CARRIER_INN'] = $jsonTrue['executor']['inn'];
            $properties['CARGO_OWNER'] = $jsonTrue['customer']['name'];
            $properties['CARGO_OWNER_INN'] = $jsonTrue['customer']['inn'];
        }

        if ($jsonFalse) {
            $properties['CARRIER'] = $jsonFalse['executor']['name'];
            $properties['CARRIER_INN'] = $jsonFalse['executor']['inn'];
            $properties['CARGO_OWNER'] = $jsonTrue['customer']['name'];
            $properties['CARGO_OWNER_INN'] = $jsonTrue['customer']['inn'];
            $properties['FORWARDER'] = $jsonFalse['customer']['name'];
            $properties['FORWARDER_INN'] = $jsonFalse['customer']['inn'];
        }

        if ($this->carrier['root'] === false) {
            $properties['CHECKLIST_FORWARDER'] = 0;
            if ($checksTrue > $checksFalse) {
                $properties['CHECKLIST_FORWARDER'] = 1;
            }

            if ($checksFalse > 0) {
                $properties['CHECKLIST_FORWARDER'] = 2;
            }
        } else {
            //Статус перевозки
            $properties['STATUS_SHIPPING'] = $this->carrier['status'];

            $properties['CHECKLIST_CARRIER'] = 0;
            if ($checksTrue > $checksFalse) {
                $properties['CHECKLIST_CARRIER'] = 1;
            }

            if ($checksFalse > 0) {
                $properties['CHECKLIST_CARRIER'] = 2;
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
     * @param int $status
     * @param string|null $licensePlate
     * @return array
     */
    protected static function setChecks(
        string $name,
        array $group,
        array $properties,
        int $status = 0,
        ?string $licensePlate = null
    ): array
    {
        switch ($name) {
            case 'contract':
                $properties['CONTRACT_CHECK'] = $status;
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
                $properties['DOCUMENTS_CHECK'] = $status;
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
                $properties['AUTOMATIC_CHECKS'] = $status;
                if ($group['name'] === 'prices') {
                    $properties['AUTOMATIC_PRICES_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'geo_monitoring') {
                    $properties['AUTOMATIC_GEO_MONITORING_STATUS'] = $group['status'];
                }
                break;
            case 'accounting':
                $properties['ACCOUNTING_CHECKS'] = $status;
                if ($group['name'] === 'invoice' && $group['status'] === 'passed') {
                    $properties['ACCOUNTING_INVOICE_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'act_of_service_acceptance' && $group['status'] === 'passed') {
                    $properties['ACCOUNTING_ACT_ACCEPTANCE_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'act_of_service_acceptance_multiple_transportations' && $group['status'] === 'passed') {
                    $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'transportation_registry' && $group['status'] === 'passed') {
                    $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'tax_invoice' && $group['status'] === 'passed') {
                    $properties['ACCOUNTING_TAX_INVOICE_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'universal_transfer_document' && $group['status'] === 'passed') {
                    $properties['ACCOUNTING_UPD_STATUS'] = $group['status'];
                }
                break;
            case 'vehicle_donkey':
                $properties['DONKEY_CHECKS'] = $status;
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
                $properties['TRAILER_CHECKS'] = $status;
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
                $properties['TRAILER_SECONDARY_CHECKS'] = $status;
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
                $properties['TRUCK_CHECKS'] = $status;
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
     * @param int $status
     * @param string|null $licensePlate
     * @return array
     */
    protected static function setChecksForwarder(
        string $name,
        array $group,
        array $properties,
        int $status = 0,
        ?string $licensePlate = null
    ): array
    {
        switch ($name) {
            case 'contract':
                $properties['CONTRACT_FOR_CHECK'] = $status;
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
                $properties['DOCUMENTS_FOR_CHECK'] = $status;
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
                $properties['AUTOMATIC_FOR_CHECKS'] = $status;
                if ($group['name'] === 'prices') {
                    $properties['AUTOMATIC_PRICES_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'geo_monitoring') {
                    $properties['AUTOMATIC_GEO_MONITORING_FOR_STATUS'] = $group['status'];
                }
                break;
            case 'accounting':
                $properties['ACCOUNTING_FOR_CHECKS'] = $status;
                if ($group['name'] === 'invoice' && $group['status'] === 'passed') {
                    $properties['ACCOUNTING_INVOICE_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'act_of_service_acceptance' && $group['status'] === 'passed') {
                    $properties['ACCOUNTING_ACT_ACCEPTANCE_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'act_of_service_acceptance_multiple_transportations' && $group['status'] === 'passed') {
                    $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORT_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'transportation_registry' && $group['status'] === 'passed') {
                    $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'tax_invoice' && $group['status'] === 'passed') {
                    $properties['ACCOUNTING_TAX_INVOICE_FOR_STATUS'] = $group['status'];
                }
                if ($group['name'] === 'universal_transfer_document' && $group['status'] === 'passed') {
                    $properties['ACCOUNTING_UPD_FOR_STATUS'] = $group['status'];
                }
                break;
            case 'vehicle_donkey':
                $properties['DONKEY_FOR_CHECKS'] = $status;
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
                $properties['TRAILER_FOR_CHECKS'] = $status;
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
                $properties['TRAILER_SECONDARY_FOR_CHECKS'] = $status;
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
                $properties['TRUCK_FOR_CHECKS'] = $status;
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
                    $nameLink .= $attachment['name'] . ',';
                    $link .= $attachment['url'] . ',';
                    $ufAttachments = true;
                    $ufEdmAttachments = false;
                }
                foreach ($check['edm_attachments'] as $edm_attachment) {
                    $nameLink .= $attachment['id'] . ',';
                    $link .= $edm_attachment['printed_form'] . ',';
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

        $data = $entity_data_class::getList([
            "select" => ["ID"],
            "filter" => [
                "UF_ID_ELEMENT" => $idElement,
            ]
        ])->fetchAll();

        foreach ($data as $datum) {
            $entity_data_class::delete($datum['ID']);
        }

        foreach ($fields as $item) {
            $entity_data_class::add($item);
        }
    }
}