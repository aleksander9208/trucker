<?php

declare(strict_types=1);

namespace Taxcom\Library\Service;

use Bitrix\Iblock\IblockTable;
use CIBlockElement;
use JsonException;
use RdKafka\Conf;
use RdKafka\Consumer;
use RdKafka\TopicConf;

/**
 * Экспорт файлов перевозчика
 */
class ExportMessageKafkaConsumer
{
    /** @var string Адрес подключение к кафке */
    public const BROKER = '172.125.125.36:9092,172.125.125.37:9092,172.125.125.44:9092';

    /** @var string Топик с сообщениями */
    public const TOPIC = 'tracker';

    /** @var string */
    public const NO_MESSAGE = 'Новых сообщений нет';

    /**
     * Экспорт писем
     *
     * @return void
     * @throws JsonException
     */
    public function export()
    {
        $conf = new Conf();
        $conf->set('group.id', 'group_1');
        $conf->set('log_level', (string) LOG_DEBUG);
        $conf->set('debug', 'all');

        $rk = new Consumer($conf);
        $rk->setLogLevel(LOG_DEBUG);
        $rk->addBrokers(self::BROKER);


        $topicConf = new TopicConf();
        $topicConf->set("auto.commit.enable", 'false');
        ##$topicConf->set("enable.auto.offset.store", "true");
        #$topicConf->set("auto.commit.interval.ms", 0);
        #$topicConf->set("offset.store.sync.interval.ms", -1);
        #$topicConf->set("offset.store.method", 'broker');
        #$topicConf->set("request.required.acks", 1);
        $topicConf->set('offset.store.method', 'file');
        #$topicConf->set('offset.store.path', sys_get_temp_dir());
        $topicConf->set("auto.offset.reset", 'smallest');

        $topic = $rk->newTopic(self::TOPIC, $topicConf);
        $topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);


        while(true) {
            $msg = $topic->consume(0, 1000);

            if($msg->err) {
                var_dump($msg->errstr() );
                break;
            }

            if($msg !== null) {
                $carrier = json_decode($msg->payload, true,);

                if (is_array($carrier)) {
                    $element = new CIBlockElement;
                    $idIblock = self::getIblockId();
                    $id = self::getIdCarrier($carrier['execution_request_uid']);

                        if ($id === null) {
                            $id = $element->Add(self::getFields($carrier, $idIblock));
                        } else {
                            $element->update($id, self::getFields($carrier, $idIblock));
                        }
                        $element::SetPropertyValuesEx($id, $idIblock, self::getPropertyList($carrier, $msg->payload));
                }
                $topic->offsetStore($msg->partition, ($msg->offset+1) );
            } else {
                var_dump(self::NO_MESSAGE);
                break;
            }
        }
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
     * @return array
     */
    public static function getFields(array $carrier, int $idIblock): array
    {
        return [
            "IBLOCK_ID" => $idIblock,
            "NAME" => $carrier['execution_request_uid'],
            "ACTIVE" => "Y",
        ];
    }

    /**
     * Возвращаем свойства элемента
     *
     * @param array $carrier
     * @param string $json
     * @return array
     */
    public static function getPropertyList(array $carrier, string $json): array
    {
        //json который получили
        $properties['JSON'] = $json;
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

                // Подготавливаем документы
                $properties = self::setDocuments($check_group['name'], $checks, $properties, $checksTrue, $countChecks);
            }
        }

        if ($checksTrue === $checksFalse) {
            $properties['CHECKLIST_CARRIER'] = true;
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

    protected static function setDocuments(
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
                    //$properties['CONTRACT_EXPEDITION_FORWARDER_LINK'] = $checks['edm_attachments'][1]['original_file'];
                    foreach ($group['attachments'] as $file) {
                        $properties['CONTRACT_EXPEDITION_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'transportation_contract') {
                    $properties['CONTRACT_TRANSPORTATION_STATUS'] = $group['status'];
                    //$properties['CONTRACT_TRANSPORTATION_FORWARDER_LINK'] = $group['attachments'][1]['original_file'];
                    foreach ($group['attachments'] as $file) {
                        $properties['CONTRACT_TRANSPORTATION_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'order_one_time_contract') {
                    $properties['CONTRACT_ORDER_ONE_TIME_STATUS'] = $group['status'];
                    //$properties['CONTRACT_ORDER_ONE_TIME_FORWARDER_LINK'] = $group['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['CONTRACT_ORDER_ONE_TIME_LINK'] .= $file['url'] . ',';
                    }
                }
                break;
            case 'execution_documents':
                $properties['DOCUMENTS_CHECK'] = $checksTrue . '/' .$countChecks;
                if ($group['name'] === 'epd') {
                    $properties['DOCUMENTS_EPD_STATUS'] = $group['status'];
                    //$properties['DOCUMENTS_EPD_FORWARDER_LINK'] = $checks['edm_attachments'][1]['original_file'];
                    foreach ($group['attachments'] as $file) {
                        $properties['DOCUMENTS_EPD_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'expeditor_order') {
                    $properties['DOCUMENTS_EXPEDITOR_STATUS'] = $group['status'];
                    //$properties['DOCUMENTS_EXPEDITOR_FORWARDER_LINK'] = $checks['edm_attachments'][1]['original_file'];
                    foreach ($group['attachments'] as $file) {
                        $properties['DOCUMENTS_EXPEDITOR_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'expeditor_agent_receipt') {
                    $properties['DOCUMENTS_EXPEDITOR_RECEIPT_STATUS'] = $group['status'];
                    //$properties['DOCUMENTS_EXPEDITOR_RECEIPT_FORWARDER_LINK'] = $checks['edm_attachments'][1]['original_file'];
                    foreach ($group['attachments'] as $file) {
                        $properties['DOCUMENTS_EXPEDITOR_RECEIPT_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'driver_approvals') {
                    $properties['DOCUMENTS_DRIVER_APPROVALS_STATUS'] = $group['status'];
                    //$properties['DOCUMENTS_DRIVER_APPROVALS_FORWARDER_LINK'] = $checks['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['DOCUMENTS_DRIVER_APPROVALS_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'application_for_transportation') {
                    $properties['DOCUMENTS_APPLICATION_TRANSPORTATION_STATUS'] = $group['status'];
                    //$properties['DOCUMENTS_APPLICATION_TRANSPORTATION_FORWARDER_LINK'] = $checks['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['DOCUMENTS_APPLICATION_TRANSPORTATION_LINK'] .= $file['url'] . ',';
                    }
                }
                break;
            case 'automatic_checks':
                $properties['AUTOMATIC_CHECKS'] = $checksTrue . '/' .$countChecks;
                if ($group['name'] === 'prices') {
                    $properties['AUTOMATIC_PRICES_STATUS'] = $group['status'];
                    $properties['AUTOMATIC_PRICES'] = $group['results']['diff_percentage'];
                }
                if ($group['name'] === 'geo_monitoring') {
                    $properties['AUTOMATIC_GEO_MONITORING_STATUS'] = $group['status'];
                    $properties['AUTOMATIC_GEO_MONITORING'] = $group['results']['не известно'];
                }
                break;
            case 'accounting':
                $properties['ACCOUNTING_CHECKS'] = $checksTrue . '/' .$countChecks;
                if ($group['name'] === 'invoice') {
                    $properties['ACCOUNTING_INVOICE_STATUS'] = $group['status'];
                    //$properties['ACCOUNTING_INVOICE_FORWARDER_LINK'] = $checks['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['ACCOUNTING_INVOICE_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'act_of_service_acceptance') {
                    $properties['ACCOUNTING_ACT_ACCEPTANCE_STATUS'] = $group['status'];
                    //$properties['ACCOUNTING_ACT_ACCEPTANCE_FORWARDER_LINK'] = $checks['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['ACCOUNTING_ACT_ACCEPTANCE_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'act_of_service_acceptance_multiple_transportations') {
                    $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS'] = $group['status'];
                    //$properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_FORWARDER_LINK'] = $checks['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'transportation_registry') {
                    $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS'] = $group['status'];
                    //$properties['ACCOUNTING_TRANSPORTATION_REGISTRY_FORWARDER_LINK'] = $checks['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['ACCOUNTING_TRANSPORTATION_REGISTRY_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'tax_invoice') {
                    $properties['ACCOUNTING_TAX_INVOICE_STATUS'] = $group['status'];
                    //$properties['ACCOUNTING_TAX_INVOICE_FORWARDER_LINK'] = $checks['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['ACCOUNTING_TAX_INVOICE_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'universal_transfer_document') {
                    $properties['ACCOUNTING_UPD_STATUS'] = $group['status'];
                    //$properties['ACCOUNTING_UPD_FORWARDER_LINK'] = $checks['edm_attachments'][1]['original_file'];
                    foreach ($group['attachments'] as $file) {
                        $properties['ACCOUNTING_UPD_LINK'] .= $file['url'] . ',';
                    }
                }
                break;
            case 'vehicle_donkey':
                $properties['DONKEY_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['DONKEY_LICENSE_PLATE'] = $group['meta']['license_plate'];
                if ($group['name'] === 'sts') {
                    $properties['DONKEY_STS_STATUS'] = $group['status'];
                    //$properties['DONKEY_STS_FORWARDER_LINK'] = $checks['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['DONKEY_STS_LINK'] .= $file['url'] . ',';
                    }
                }
                break;
            case 'vehicle_main_trailer':
                $properties['TRAILER_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['TRAILER_LICENSE_PLATE'] = $group['meta']['license_plate'];
                if ($group['name'] === 'sts') {
                    $properties['TRAILER_STS_STATUS'] = $group['status'];
                    //$properties['TRAILER_STS_FORWARDER_LINK'] = $group['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['TRAILER_STS_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRAILER_RENT_AGREEMENT_STATUS'] = $group['status'];
                    //$properties['TRAILER_RENT_AGREEMENT_FORWARDER_LINK'] = $group['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['TRAILER_RENT_AGREEMENT_LINK'] .= $file['url'] . ',';
                    }
                }
                break;
            case 'vehicle_secondary_trailer':
                $properties['TRAILER_SECONDARY_CHECKS'] = $checksTrue . '/' .$countChecks;
                $properties['TRAILER_SECONDARY_LICENSE_PLATE'] = $group['meta']['license_plate'];
                if ($group['name'] === 'sts') {
                    $properties['TRAILER_SECONDARY_STS_STATUS'] = $group['status'];
                    //$properties['TRAILER_SECONDARY_STS_FORWARDER_LINK'] = $group['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['TRAILER_SECONDARY_STS_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRAILER_SECONDARY_RENT_AGREEMENT_STATUS'] = $group['status'];
                    //$properties['TRAILER_SECONDARY_RENT_AGREEMENT_FORWARDER_LINK'] = $group['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['TRAILER_SECONDARY_RENT_AGREEMENT_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'agreement_withLeasingCompany') {
                    $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_STATUS'] = $group['status'];
                    //$properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_FORWARDER_LINK'] = $group['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'marriage_certificate') {
                    $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_STATUS'] = $group['status'];
                    //$properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_FORWARDER_LINK'] = $group['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'free_usage_agreement') {
                    $properties['TRAILER_SECONDARY_FREE_USAGE_STATUS'] = $group['status'];
                    //$properties['TRAILER_SECONDARY_FREE_USAGE_FORWARDER_LINK'] = $group['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['TRAILER_SECONDARY_FREE_USAGE_LINK'] .= $file['url'] . ',';
                    }
                }
                break;
            case 'vehicle_truck':
                $properties['TRUCK_CHECKS'] = $checksTrue . '/' .$countChecks;
                // Номерной знак
                $properties['TRUCK_LICENSE_PLATE'] = $group['meta']['license_plate'];
                if ($group['name'] === 'sts') {
                    $properties['TRUCK_STS_STATUS'] = $group['status'];
                    //$properties['TRUCK_STS_FORWARDER_LINK'] = $group['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['TRUCK_STS_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'rent_agreement') {
                    $properties['TRUCK_RENT_AGREEMENT_STATUS'] = $group['status'];
                    //$properties['TRUCK_RENT_AGREEMENT_FORWARDER_LINK'] = $group['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['TRUCK_RENT_AGREEMENT_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'agreement_withLeasingCompany') {
                    $properties['TRUCK_AGREEMENT_LEASING_COMPANY_STATUS'] = $group['status'];
                    //$properties['TRUCK_AGREEMENT_LEASING_COMPANY_FORWARDER_LINK'] = $group['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['TRUCK_AGREEMENT_LEASING_COMPANY_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'marriage_certificate') {
                    $properties['TRUCK_MARRIAGE_CERTIFICATE_STATUS'] = $group['status'];
                    //$properties['TRUCK_MARRIAGE_CERTIFICATE_FORWARDER_LINK'] = $group['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['TRUCK_MARRIAGE_CERTIFICATE_LINK'] .= $file['url'] . ',';
                    }
                }
                if ($group['name'] === 'free_usage_agreement') {
                    $properties['TRUCK_FREE_USAGE_STATUS'] = $group['status'];
                    //$properties['TRUCK_FREE_USAGE_FORWARDER_LINK'] = $group['attachments'][1]['url'];
                    foreach ($group['attachments'] as $file) {
                        $properties['TRUCK_FREE_USAGE_LINK'] .= $file['url'] . ',';
                    }
                }
                break;
        }

        return $properties;
    }
}