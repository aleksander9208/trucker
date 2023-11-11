<?php

declare(strict_types=1);

namespace Taxcom\Library\Service;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use CIBlockElement;

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
     */
    public function isParser(): void
    {
        $element = new CIBlockElement;
        $idIblock = self::getIblockId();
        $id = self::getIdCarrier($this->carrier['execution_request_uid']);

        if ($id === null) {
            $id = $element->Add(self::getFields($this->carrier, $idIblock, $this->json));
        } else {
            $element->update($id, self::getFields($this->carrier, $idIblock, $this->json));
        }
        $element::SetPropertyValuesEx($id, $idIblock, self::getPropertyList($this->carrier));

        self::setLink($id, $this->carrier['check_groups']);
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
    public static function getFields(array $carrier, int $idIblock, string $json): array
    {
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
        //Дата погрузки
        $properties['DATE_SHIPMENT'] = $carrier['loading_date'];
        //Перевозчик
        $properties['CARRIER'] = $carrier['executor']['name'];
        $properties['CARRIER_INN'] = $carrier['executor']['inn'];
        //Грузовладелец
        $properties['CARGO_OWNER'] = $carrier['customer']['name'];
        $properties['CARGO_OWNER_INN'] = $carrier['customer']['inn'];
        //Чек-лист с перевозчиком
        $properties['CHECKLIST_CARRIER'] = false;
        //Чек-лист с экспедиторов
        $properties['CHECKLIST_FORWARDER'] = false;
        $properties['CONTRACT_CHECK'] = '';
        $properties['CONTRACT_EXPEDITION_STATUS'] = '';
        $properties['CONTRACT_TRANSPORTATION_STATUS'] = '';
        $properties['CONTRACT_ORDER_ONE_TIME_STATUS'] = '';
        $properties['DOCUMENTS_CHECK'] = '';
        $properties['DOCUMENTS_EPD_STATUS'] = '';
        $properties['DOCUMENTS_EXPEDITOR_STATUS'] = '';
        $properties['DOCUMENTS_EXPEDITOR_RECEIPT_STATUS'] = '';
        $properties['DOCUMENTS_DRIVER_APPROVALS_STATUS'] = '';
        $properties['DOCUMENTS_APPLICATION_TRANSPORTATION_STATUS'] = '';
        $properties['AUTOMATIC_CHECKS'] = '';
        $properties['ACCOUNTING_CHECKS'] = '';
        $properties['ACCOUNTING_INVOICE_STATUS'] = '';
        $properties['ACCOUNTING_ACT_ACCEPTANCE_STATUS'] = '';
        $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS'] = '';
        $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS'] = '';
        $properties['ACCOUNTING_TAX_INVOICE_STATUS'] = '';
        $properties['ACCOUNTING_UPD_STATUS'] = '';
        $properties['DONKEY_CHECKS'] = '';
        $properties['DONKEY_LICENSE_PLATE'] = '';
        $properties['DONKEY_STS_STATUS'] = '';
        $properties['DONKEY_RENT_AGREEMENT_STATUS'] = '';
        $properties['TRAILER_CHECKS'] = '';
        $properties['TRAILER_LICENSE_PLATE'] = '';
        $properties['TRAILER_STS_STATUS'] = '';
        $properties['TRAILER_RENT_AGREEMENT_STATUS'] = '';
        $properties['TRAILER_SECONDARY_CHECKS'] = '';
        $properties['TRAILER_SECONDARY_LICENSE_PLATE'] = '';
        $properties['TRAILER_SECONDARY_STS_STATUS'] = '';
        $properties['TRAILER_SECONDARY_RENT_AGREEMENT_STATUS'] = '';
        $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_STATUS'] = '';
        $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_STATUS'] = '';
        $properties['TRAILER_SECONDARY_FREE_USAGE_STATUS'] = '';
        $properties['TRUCK_CHECKS'] = '';
        $properties['TRUCK_LICENSE_PLATE'] = '';
        $properties['TRUCK_STS_STATUS'] = '';
        $properties['TRUCK_RENT_AGREEMENT_STATUS'] = '';
        $properties['TRUCK_AGREEMENT_LEASING_COMPANY_STATUS'] = '';
        $properties['TRUCK_MARRIAGE_CERTIFICATE_STATUS'] = '';
        $properties['TRUCK_FREE_USAGE_STATUS'] = '';

        if ($carrier['root']) {
            $properties['CHECKLIST_FORWARDER'] = true;
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

                $properties = self::setChecks($check_group['name'], $checks, $properties, $checksTrue, $countChecks);
            }
        }

        if ($checksTrue === $checksFalse) {
            $properties['CHECKLIST_CARRIER'] = true;
        }

        return $properties;
    }

    /**
     * Устанавливаем дополнительные свойства
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
     * @throws \Exception
     */
    protected static function setLink($idElement, $groups)
    {
        Loader::includeModule("highloadblock");

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
            'filter' => ['=NAME' => 'FnsLinkDocuments']
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