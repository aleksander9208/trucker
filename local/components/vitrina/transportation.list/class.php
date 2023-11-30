<?php

use Bitrix\Main\Context;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Grid\Options;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\UI\PageNavigation;
use Taxcom\Library\HLBlock\HLBlock;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Компонент вывода списка перевозок
 */
class TransportationList extends CBitrixComponent
{
    /** @var ErrorCollection $errors */
    public ErrorCollection $errors;

    /**
     * @return void
     * @throws LoaderException
     */
    protected function prepareResult(): void
    {
        $this->arResult['YEAR'] = date("Y");
        $this->arResult['COLUMNS'] = self::getColumns();
        $this->arResult['INFO_BAR_DOC'] = self::getDocuments();
        $this->arResult['INFO_BAR_DOC_FOR'] = self::getDocumentsFor();
        $this->arResult['FILTER_YEAR'] = self::getFilterYear();
        $this->getPercent();
        $this->getRows();

    }

    /**
     * @return void
     */
    protected function printErrors(): void
    {
        foreach ($this->errors as $error) {
            ShowError($error);
        }
    }

    /**
     * @return void
     * @throws LoaderException
     */
    public function executeComponent(): void
    {
        Loader::includeModule('iblock');
        Loader::includeModule('taxcom.library');

        $this->errors = new ErrorCollection();

        if ($this->errors->count() <= 0) {
            $this->prepareResult();
        } else {
            $this->printErrors();
        }

        $this->includeComponentTemplate();
    }

    /**
     * Возвращаем колонки таблицы
     *
     * @return array
     */
    protected static function getColumns(): array
    {
        return [
            [
                "id" => "ID_TRANSPORTATION",
                "name" => "№ перевозки",
                "default" => true,
                "class" => "row-item1"
            ],
            [
                "id" => "DATE_SHIPMENT",
                "name" => "Дата погрузки",
                "default" => true,
                "class" => "row-item1"
            ],
            [
                "id" => "CARGO_OWNER",
                "name" => "Грузовладелец",
                "default" => true,
                "class" => "no-column"
            ],
            [
                "id" => "FORWARDER",
                "name" => "Экспедитор",
                "default" => true,
                "class" => "no-column"
            ],
            [
                "id" => "CARRIER",
                "name" => "Перевозчик",
                "default" => true,
                "class" => "no-column"
            ],
            [
                "id" => "DEVIATION_FROM_PRICE",
                "name" => "Отклонение от рыночной цены, %",
                "default" => true,
                "class" => "no-column"
            ],
            [
                "id" => "CHECKLIST_CARRIER",
                "name" => "Чек-лист с перевозчиком",
                "default"=> true,
                "class" => "no-column"
            ],
            [
                "id" => "CHECKLIST_FORWARDER",
                "name" => "Чек-лист с экспедитором",
                "default" => true,
                "class" => "no-column"
            ],
        ];
    }

    /**
     * Возвращаем список документов
     * и групп
     *
     * @return array[]
     */
    protected static function getDocuments(): array
    {
        return [
            0 => [
                'ID' => 'contract',
                'NAME' => 'Подписанные договоры',
                'ID_CHECK' => 'detail_status-transportation',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'transport_link',
                        'NAME' => 'Договор перевозки',
                        'LINK_ID' => 'transport_file',
                        'FILE' => true,
                    ],
                    1 => [
                        'ID' => 'contract_link',
                        'NAME' => 'Договор транспортной экспедиции',
                        'LINK_ID' => 'contract_file',
                        'FILE' => true,
                    ],
                    2 => [
                        'ID' => 'one_time_link',
                        'NAME' => 'Заказ (разовая договор-заявка)',
                        'LINK_ID' => 'one_time_file',
                        'FILE' => true,
                    ],
                ],
            ],
            1 => [
                'ID' => 'execution_documents',
                'NAME' => 'Оформление перевозки',
                'ID_CHECK' => 'documents_check',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'documents_link',
                        'NAME' => 'Заявка на перевозку',
                        'LINK_ID' => 'documents_file',
                        'FILE' => true,
                    ],
                    1 => [
                        'ID' => 'epd_link',
                        'NAME' => 'Подписанная ЭТрН',
                        'LINK_ID' => 'epd_file',
                        'FILE' => true,
                    ],
                    2 => [
                        'ID' => 'driver_link',
                        'NAME' => 'Подтверждения договорных отношений с водителем',
                        'LINK_ID' => 'driver_file',
                        'FILE' => true,
                    ],
                    3 => [
                        'ID' => 'exp_link',
                        'NAME' => 'Поручение экспедитору',
                        'LINK_ID' => 'exp_file',
                        'FILE' => true,
                    ],
                    4 => [
                        'ID' => 'receipt_link',
                        'NAME' => 'Экспедиторская расписка',
                        'LINK_ID' => 'receipt_file',
                        'FILE' => true,
                    ],
                ],
            ],
            2 => [
                'ID' => 'automatic',
                'NAME' => 'Автоматические проверки',
                'ID_CHECK' => 'auto_check',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'prices_link',
                        'NAME' => 'Стоимость перевозки соответствует рыночным ценам',
                        'LINK_ID' => 'prices_file',
                        'FILE' => false,
                    ],
                    1 => [
                        'ID' => 'geo_link',
                        'NAME' => 'Подтверждения перевозки через геомониторинг',
                        'LINK_ID' => 'geo_file',
                        'FILE' => false,
                    ],
                ],
            ],
            3 => [
                'ID' => 'accounting',
                'NAME' => 'Бухгалтерские документы',
                'ID_CHECK' => 'accounting_check',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'invoice_link',
                        'NAME' => 'Счёт',
                        'LINK_ID' => 'invoice_file',
                        'FILE' => true,
                    ],
                    1 => [
                        'ID' => 'act_link',
                        'NAME' => 'Акт о приемке выполненных работ по услуге',
                        'LINK_ID' => 'act_file',
                        'FILE' => true,
                    ],
                    2 => [
                        'ID' => 'multi_link',
                        'NAME' => 'Акт о приемке выполненных работ, включающий несколько перевозок',
                        'LINK_ID' => 'multi_file',
                        'FILE' => true,
                    ],
                    3 => [
                        'ID' => 'reg_link',
                        'NAME' => 'Реестр на перевозки',
                        'LINK_ID' => 'reg_file',
                        'FILE' => true,
                    ],
                    4 => [
                        'ID' => 'tax_link',
                        'NAME' => 'Счёт-фактура',
                        'LINK_ID' => 'tax_file',
                        'FILE' => true,
                    ],
                    5 => [
                        'ID' => 'upd_link',
                        'NAME' => 'УПД',
                        'LINK_ID' => 'upd_file',
                        'FILE' => true,
                    ],
                ],
            ],
            4 => [
                'ID' => 'donkey',
                'NAME' => 'Подтверждения владения (тягач)',
                'ID_CHECK' => 'donkey_check',
                'ID_PLATE' => 'donkey_plate',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'donkey_link',
                        'NAME' => 'СТС',
                        'LINK_ID' => 'donkey_file',
                        'FILE' => true,
                    ],
                    1 => [
                        'ID' => 'donkey_rent_link',
                        'NAME' => 'Договор аренды',
                        'LINK_ID' => 'donkey_rent_file',
                        'FILE' => true,
                    ],
                    2 => [
                        'ID' => 'donkey_lias_link',
                        'NAME' => 'Договор с лизинговой компанией',
                        'LINK_ID' => 'donkey_lias_file',
                        'FILE' => true,
                    ],
                    3 => [
                        'ID' => 'donkey_cer_link',
                        'NAME' => 'Свидетельство о браке',
                        'LINK_ID' => 'donkey_cer_file',
                        'FILE' => true,
                    ],
                    4 => [
                        'ID' => 'donkey_usage_link',
                        'NAME' => 'Договор безвозмездного использования',
                        'LINK_ID' => 'donkey_usage_file',
                        'FILE' => true,
                    ],
                ],
            ],
            5 => [
                'ID' => 'trailer',
                'NAME' => 'Подтверждения владения (прицеп)',
                'ID_CHECK' => 'trailer_check',
                'ID_PLATE' => 'trailer_plate',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'trailer_ctc_link',
                        'NAME' => 'СТС',
                        'LINK_ID' => 'trailer_ctc_file',
                        'FILE' => true,
                    ],
                    1 => [
                        'ID' => 'trailer_rent_link',
                        'NAME' => 'Договор аренды',
                        'LINK_ID' => 'trailer_rent_file',
                        'FILE' => true,
                    ],
                    2 => [
                        'ID' => 'trailer_leas_link',
                        'NAME' => 'Договор с лизинговой компанией',
                        'LINK_ID' => 'trailer_leas_file',
                        'FILE' => true,
                    ],
                    3 => [
                        'ID' => 'trailer_cert_link',
                        'NAME' => 'Свидетельство о браке',
                        'LINK_ID' => 'trailer_cert_file',
                        'FILE' => true,
                    ],
                    4 => [
                        'ID' => 'trailer_usage_link',
                        'NAME' => 'Договор безвозмездного использования',
                        'LINK_ID' => 'trailer_usage_file',
                        'FILE' => true,
                    ],
                ],
            ],
            6 => [
                'ID' => 'trailer_sec',
                'NAME' => 'Подтверждение владения второго (прицеп)',
                'ID_CHECK' => 'trailer_sec_check',
                'ID_PLATE' => 'trailer_sec_plate',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'trailer_sec_ctc_link',
                        'NAME' => 'СТС',
                        'LINK_ID' => 'trailer_sec_ctc_file',
                        'FILE' => true,
                    ],
                    1 => [
                        'ID' => 'trailer_sec_rent_link',
                        'NAME' => 'Договор аренды',
                        'LINK_ID' => 'trailer_sec_rent_file',
                        'FILE' => true,
                    ],
                    2 => [
                        'ID' => 'trailer_sec_lias_link',
                        'NAME' => 'Договор с лизинговой компанией',
                        'LINK_ID' => 'trailer_sec_lias_file',
                        'FILE' => true,
                    ],
                    3 => [
                        'ID' => 'trailer_sec_cer_link',
                        'NAME' => 'Свидетельство о браке',
                        'LINK_ID' => 'trailer_sec_cer_file',
                        'FILE' => true,
                    ],
                    4 => [
                        'ID' => 'trailer_sec_usage_link',
                        'NAME' => 'Договор безвозмездного использования',
                        'LINK_ID' => 'trailer_sec_usage_file',
                        'FILE' => true,
                    ],
                ],
            ],
            7 => [
                'ID' => 'truck',
                'NAME' => 'Подтверждение владения грузовик',
                'ID_CHECK' => 'truck_check',
                'ID_PLATE' => 'truck_plate',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'truck_sts_link',
                        'NAME' => 'СТС',
                        'LINK_ID' => 'truck_sts_file',
                        'FILE' => true,
                    ],
                    1 => [
                        'ID' => 'truck_rent',
                        'NAME' => 'Договор аренды',
                        'LINK_ID' => 'truck_link',
                        'FILE' => true,
                    ],
                    2 => [
                        'ID' => 'truck_leas_link',
                        'NAME' => 'Договор с лизинговой компанией',
                        'LINK_ID' => 'truck_leas_file',
                        'FILE' => true,
                    ],
                    3 => [
                        'ID' => 'truck_cert_link',
                        'NAME' => 'Свидетельство о браке',
                        'LINK_ID' => 'truck_cert_file',
                        'FILE' => true,
                    ],
                    4 => [
                        'ID' => 'truck_usage_link',
                        'NAME' => 'Договор безвозмездного использования',
                        'LINK_ID' => 'truck_usage_file',
                        'FILE' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Возвращаем список документов
     * и групп
     *
     * @return array[]
     */
    protected static function getDocumentsFor(): array
    {
        return [
            0 => [
                'ID' => 'contract_for',
                'NAME' => 'Подписанные договоры',
                'ID_CHECK' => 'detail_status-transportation_for',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'transport_link_for',
                        'NAME' => 'Договор перевозки',
                        'LINK_ID' => 'transport_file_for',
                        'FILE' => true,
                    ],
                    1 => [
                        'ID' => 'contract_link_for',
                        'NAME' => 'Договор транспортной экспедиции',
                        'LINK_ID' => 'contract_file_for',
                        'FILE' => true,
                    ],
                    2 => [
                        'ID' => 'one_time_link_for',
                        'NAME' => 'Заказ (разовая договор-заявка)',
                        'LINK_ID' => 'one_time_file_for',
                        'FILE' => true,
                    ],
                ],
            ],
            1 => [
                'ID' => 'execution_documents_for',
                'NAME' => 'Оформление перевозки',
                'ID_CHECK' => 'documents_check_for',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'documents_link_for',
                        'NAME' => 'Заявка на перевозку',
                        'LINK_ID' => 'documents_file_for',
                        'FILE' => true,
                    ],
                    1 => [
                        'ID' => 'epd_link_for',
                        'NAME' => 'Подписанная ЭТрН',
                        'LINK_ID' => 'epd_file_for',
                        'FILE' => true,
                    ],
                    2 => [
                        'ID' => 'driver_link_for',
                        'NAME' => 'Подтверждения договорных отношений с водителем',
                        'LINK_ID' => 'driver_file_for',
                        'FILE' => true,
                    ],
                    3 => [
                        'ID' => 'exp_link_for',
                        'NAME' => 'Поручение экспедитору',
                        'LINK_ID' => 'exp_file_for',
                        'FILE' => true,
                    ],
                    4 => [
                        'ID' => 'receipt_link_for',
                        'NAME' => 'Экспедиторская расписка',
                        'LINK_ID' => 'receipt_file_for',
                        'FILE' => true,
                    ],
                ],
            ],
            2 => [
                'ID' => 'automatic_for',
                'NAME' => 'Автоматические проверки',
                'ID_CHECK' => 'auto_check_for',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'prices_link_for',
                        'NAME' => 'Стоимость перевозки соответствует рыночным ценам',
                        'LINK_ID' => 'prices_file_for',
                        'FILE' => false,
                    ],
                    1 => [
                        'ID' => 'geo_link_for',
                        'NAME' => 'Подтверждения перевозки через геомониторинг',
                        'LINK_ID' => 'geo_file_for',
                        'FILE' => false,
                    ],
                ],
            ],
            3 => [
                'ID' => 'accounting_for',
                'NAME' => 'Бухгалтерские документы',
                'ID_CHECK' => 'accounting_check_for',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'invoice_link_for',
                        'NAME' => 'Счёт',
                        'LINK_ID' => 'invoice_file_for',
                        'FILE' => true,
                    ],
                    1 => [
                        'ID' => 'act_link_for',
                        'NAME' => 'Акт о приемке выполненных работ по услуге',
                        'LINK_ID' => 'act_file_for',
                        'FILE' => true,
                    ],
                    2 => [
                        'ID' => 'multi_link_for',
                        'NAME' => 'Акт о приемке выполненных работ, включающий несколько перевозок',
                        'LINK_ID' => 'multi_file_for',
                        'FILE' => true,
                    ],
                    3 => [
                        'ID' => 'reg_link_for',
                        'NAME' => 'Реестр на перевозки',
                        'LINK_ID' => 'reg_file_for',
                        'FILE' => true,
                    ],
                    4 => [
                        'ID' => 'tax_link_for',
                        'NAME' => 'Счёт-фактура',
                        'LINK_ID' => 'tax_file_for',
                        'FILE' => true,
                    ],
                    5 => [
                        'ID' => 'upd_link_for',
                        'NAME' => 'УПД',
                        'LINK_ID' => 'upd_file_for',
                        'FILE' => true,
                    ],
                ],
            ],
            4 => [
                'ID' => 'donkey_for',
                'NAME' => 'Подтверждения владения (тягач)',
                'ID_CHECK' => 'donkey_check_for',
                'ID_PLATE' => 'donkey_plate_for',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'donkey_link_for',
                        'NAME' => 'СТС',
                        'LINK_ID' => 'donkey_file_for',
                        'FILE' => true,
                    ],
                    1 => [
                        'ID' => 'donkey_rent_link_for',
                        'NAME' => 'Договор аренды',
                        'LINK_ID' => 'donkey_rent_file_for',
                        'FILE' => true,
                    ],
                    2 => [
                        'ID' => 'donkey_leas_link_for',
                        'NAME' => 'Договор с лизинговой компанией',
                        'LINK_ID' => 'donkey_leas_file_for',
                        'FILE' => true,
                    ],
                    3 => [
                        'ID' => 'donkey_cert_link_for',
                        'NAME' => 'Свидетельство о браке',
                        'LINK_ID' => 'donkey_cert_file_for',
                        'FILE' => true,
                    ],
                    4 => [
                        'ID' => 'donkey_usage_link_for',
                        'NAME' => 'Договор безвозмездного использования',
                        'LINK_ID' => 'donkey_usage_file_for',
                        'FILE' => true,
                    ],
                ],
            ],
            5 => [
                'ID' => 'trailer_for',
                'NAME' => 'Подтверждения владения (прицеп)',
                'ID_CHECK' => 'trailer_check_for',
                'ID_PLATE' => 'trailer_plate_for',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'trailer_ctc_link_for',
                        'NAME' => 'СТС',
                        'LINK_ID' => 'trailer_ctc_file_for',
                        'FILE' => true,
                    ],
                    1 => [
                        'ID' => 'trailer_rent_link_for',
                        'NAME' => 'Договор аренды',
                        'LINK_ID' => 'trailer_rent_file_for',
                        'FILE' => true,
                    ],
                    2 => [
                        'ID' => 'trailer_leas_link_for',
                        'NAME' => 'Договор с лизинговой компанией',
                        'LINK_ID' => 'trailer_leas_file_for',
                        'FILE' => true,
                    ],
                    3 => [
                        'ID' => 'trailer_cert_link_for',
                        'NAME' => 'Свидетельство о браке',
                        'LINK_ID' => 'trailer_cert_file_for',
                        'FILE' => true,
                    ],
                    4 => [
                        'ID' => 'trailer_usage_link_for',
                        'NAME' => 'Договор безвозмездного использования',
                        'LINK_ID' => 'trailer_usage_file_for',
                        'FILE' => true,
                    ],
                ],
            ],
            6 => [
                'ID' => 'trailer_sec_for',
                'NAME' => 'Подтверждение владения второго (прицеп)',
                'ID_CHECK' => 'trailer_sec_check_for',
                'ID_PLATE' => 'trailer_sec_plate_for',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'trailer_sec_ctc_link_for',
                        'NAME' => 'СТС',
                        'LINK_ID' => 'trailer_sec_ctc_file_for',
                        'FILE' => true,
                    ],
                    1 => [
                        'ID' => 'trailer_sec_rent_link_for',
                        'NAME' => 'Договор аренды',
                        'LINK_ID' => 'trailer_sec_rent_file_for',
                        'FILE' => true,
                    ],
                    2 => [
                        'ID' => 'trailer_sec_lias_link_for',
                        'NAME' => 'Договор с лизинговой компанией',
                        'LINK_ID' => 'trailer_sec_lias_file_for',
                        'FILE' => true,
                    ],
                    3 => [
                        'ID' => 'trailer_sec_cer_link_for',
                        'NAME' => 'Свидетельство о браке',
                        'LINK_ID' => 'trailer_sec_cer_file_for',
                        'FILE' => true,
                    ],
                    4 => [
                        'ID' => 'trailer_sec_usage_link_for',
                        'NAME' => 'Договор безвозмездного использования',
                        'LINK_ID' => 'trailer_sec_usage_file_for',
                        'FILE' => true,
                    ],
                ],
            ],
            7 => [
                'ID' => 'truck_for',
                'NAME' => 'Подтверждение владения грузовик',
                'ID_CHECK' => 'truck_check_for',
                'ID_PLATE' => 'truck_plate_for',
                'DOCUMENTS' => [
                    0 => [
                        'ID' => 'truck_sts_link_for',
                        'NAME' => 'СТС',
                        'LINK_ID' => 'truck_sts_file_for',
                        'FILE' => true,
                    ],
                    1 => [
                        'ID' => 'truck_rent_for',
                        'NAME' => 'Договор аренды',
                        'LINK_ID' => 'truck_link_for',
                        'FILE' => true,
                    ],
                    2 => [
                        'ID' => 'truck_leas_link_for',
                        'NAME' => 'Договор с лизинговой компанией',
                        'LINK_ID' => 'truck_leas_file_for',
                        'FILE' => true,
                    ],
                    3 => [
                        'ID' => 'truck_cert_link_for',
                        'NAME' => 'Свидетельство о браке',
                        'LINK_ID' => 'truck_cert_file_for',
                        'FILE' => true,
                    ],
                    4 => [
                        'ID' => 'truck_usage_link_for',
                        'NAME' => 'Договор безвозмездного использования',
                        'LINK_ID' => 'truck_usage_file_for',
                        'FILE' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Возвращаем фильтр
     * годовых параметров
     *
     * @return array[]
     */
    protected static function getFilterYear(): array
    {
        return [
            0 => [
                'NAME' => '1-й квартал',
                'VALUE' => '1',
                'MONTH' => [
                    0 => [
                        'NAME' => 'янв',
                        'VALUE' => 'january',
                    ],
                    1 => [
                        'NAME' => 'фев',
                        'VALUE' => 'february',
                    ],
                    2 => [
                        'NAME' => 'мар',
                        'VALUE' => 'march',
                    ],
                ],
            ],
            1 => [
                'NAME' => '2-й квартал',
                'VALUE' => '2',
                'MONTH' => [
                    0 => [
                        'NAME' => 'апр',
                        'VALUE' => 'april',
                    ],
                    1 => [
                        'NAME' => 'май',
                        'VALUE' => 'may',
                    ],
                    2 => [
                        'NAME' => 'июн',
                        'VALUE' => 'june',
                    ],
                ],
            ],
            2 => [
                'NAME' => '3-й квартал',
                'VALUE' => '3',
                'MONTH' => [
                    0 => [
                        'NAME' => 'июл',
                        'VALUE' => 'july',
                    ],
                    1 => [
                        'NAME' => 'авг',
                        'VALUE' => 'august',
                    ],
                    2 => [
                        'NAME' => 'сен',
                        'VALUE' => 'september',
                    ],
                ],
            ],
            3 => [
                'NAME' => '4-й квартал',
                'VALUE' => '4',
                'MONTH' => [
                    0 => [
                        'NAME' => 'окт',
                        'VALUE' => 'october',
                    ],
                    1 => [
                        'NAME' => 'ноя',
                        'VALUE' => 'november',
                    ],
                    2 => [
                        'NAME' => 'дек',
                        'VALUE' => 'december',
                    ],
                ],
            ],
        ];
    }

    /**
     * Формируем массив данных
     *
     * @return void
     * @throws LoaderException
     */
    public function getRows(): void
    {
        $this->arResult["GRID_CODE"] = 'vitrina_grid';

        $gridOptions = new Options($this->arResult["GRID_CODE"]);
        $sort = $gridOptions->GetSorting([
            'sort' => ["ID" => "DESC"],
            'vars' => ["by" => "by", "order" => "order"]
        ]);

        $navParams = $gridOptions->GetNavParams();
        $nav = new PageNavigation($this->arResult["GRID_CODE"]);
        $nav->allowAllRecords(false)->setPageSize(10)->initFromUri();

        $filterOption = new Bitrix\Main\UI\Filter\Options($this->arResult["GRID_CODE"]);
        $filterData = $filterOption->getFilter([]);

        $this->arResult["FILTER"] = [];

        if ($filterData["FILTER_APPLIED"]) {
            $this->arResult["FILTER"][] = [
                'LOGIC' => "OR",
                'NAME' => '%' . $filterData["FIND"] . '%',
                'CARGO_OWNER_VALUE' => '%' . $filterData["FIND"] . '%',
                'CARGO_OWNER_INN_VALUE' => '%' . $filterData["FIND"] . '%',
                'FORWARDER_VALUE' => '%' . $filterData["FIND"] . '%',
                'FORWARDER_INN_VALUE' => '%' . $filterData["FIND"] . '%',
                'CARRIER_VALUE' => '%' . $filterData["FIND"] . '%',
                'CARRIER_INN_VALUE' => '%' . $filterData["FIND"] . '%',
            ];
        }

        $request = Context::getCurrent()->getRequest();

        $this->getFilterQuarter($request->get('kvartal'));

        $this->getFilterMonth($request->get('month'));

        if ($request->get('year')) {
            $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-01-01 00:00:00';
            $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-12-31 23:59:59';
        }

        if ($request->get('statistics') !== 'doc') {
            $this->getFilterStatistics($request->get('statistics'));
        }

        $filter = $this->arResult["FILTER"];

        if ($request->get('statistics') === 'doc') {
            $filter = ['ID' => HLBlock::getIdNoDocument()];
        }

        $filter['!STATUS_SHIPPING.VALUE'] = 'archived';

        $vitrina = \Bitrix\Iblock\Elements\ElementVitrinaApiTable::getList([
            'filter' => $filter,
            'select' =>  [
                'ID',
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
                'AUTOMATIC_PRICES_STATUS_VALUE' => 'AUTOMATIC_PRICES_STATUS.VALUE',
                'AUTOMATIC_GEO_MONITORING_STATUS_VALUE' => 'AUTOMATIC_GEO_MONITORING_STATUS.VALUE',
                'AUTOMATIC_PRICES_FOR_STATUS_VALUE' => 'AUTOMATIC_PRICES_FOR_STATUS.VALUE',
                'AUTOMATIC_GEO_MONITORING_FOR_STATUS_VALUE' => 'AUTOMATIC_GEO_MONITORING_FOR_STATUS.VALUE',
            ],
            "offset" => $nav->getOffset(),
            "limit" => $nav->getLimit(),
            "order" => ['DATE_SHIPMENT.VALUE' => 'DESC'],
            "count_total" => true,
        ]);

        $nav->setRecordCount($vitrina->getCount());

        foreach ($vitrina->fetchAll() as $item) {
            $deviation = HLBlock::getPrice($item['ID']);
            $date = explode('-', $item['DATE_SHIPMENT_VALUE']);

            if($deviation !== '') {
                $deviation = '<div class="icon-deviation_down"><span uk-icon="icon: arrow-down"></span>' . HLBlock::getPrice($item['ID']) . '</div>';
            }

            if ($item['CHECKLIST_CARRIER_VALUE'] === '1') {
                $statusCarrier = '<span class="transit-good"></span>';
            } elseif($item['CHECKLIST_CARRIER_VALUE'] === '0') {
                $statusCarrier = '<span class="transit-error"></span>';
            } else {
                $statusCarrier = '<span class="transit-progress"></span>';
            }

            if ($item['CHECKLIST_FORWARDER_VALUE'] === '1') {
                $statusFor = '<span class="transit-good"></span>';
            } elseif ($item['CHECKLIST_FORWARDER_VALUE'] === '0') {
                $statusFor = '<span class="transit-error"></span>';
            } else {
                $statusFor = '<span class="transit-progress"></span>';
            }

            $vitrinaList[] = [
                'data' => [
                    "ID" => $item['ID'],
                    "ID_TRANSPORTATION" => '<a href="#info-bar" class="info_bar-content" uk-toggle>' . $item['NAME'] . '</a>',
                    "DATE_SHIPMENT"  => $date[2] . '.' . $date[1] . '.' . $date[0],
                    "CARGO_OWNER" => $item['CARGO_OWNER_VALUE'] . '<span>' . $item['CARGO_OWNER_INN_VALUE'] . '</span>',
                    "FORWARDER" => $item['FORWARDER_VALUE'] . '<span>' . $item['FORWARDER_INN_VALUE'] . '</span>',
                    "CARRIER" => $item['CARRIER_VALUE'] . '<span>' . $item['CARRIER_INN_VALUE'] . '</span>',
                    "DEVIATION_FROM_PRICE" => $deviation,
                    "CHECKLIST_CARRIER" => $statusCarrier,
                    "CHECKLIST_FORWARDER" => $statusFor,
                ],
            ];
        }

        $this->arResult["ROWS"] = $vitrinaList;
        $this->arResult["NAV"] = $nav;
    }

    /**
     * Расчет статистики
     *
     * @return void
     * @throws LoaderException
     */
    protected function getPercent(): void
    {
        $vitrina = \Bitrix\Iblock\Elements\ElementVitrinaApiTable::getList([
            'filter' => ['!STATUS_SHIPPING.VALUE' => 'archived'],
            'select' => [
                'ID',
                'NAME',
                'STATUS_SHIPPING_VALUE' => 'STATUS_SHIPPING.VALUE',
                'CHECKLIST_CARRIER_VALUE' => 'CHECKLIST_CARRIER.VALUE',
                'CHECKLIST_FORWARDER_VALUE' => 'CHECKLIST_FORWARDER.VALUE',
                'AUTOMATIC_PRICES_STATUS_VALUE' => 'AUTOMATIC_PRICES_STATUS.VALUE',
                'AUTOMATIC_GEO_MONITORING_STATUS_VALUE' => 'AUTOMATIC_GEO_MONITORING_STATUS.VALUE',
                'AUTOMATIC_PRICES_FOR_STATUS_VALUE' => 'AUTOMATIC_PRICES_FOR_STATUS.VALUE',
                'AUTOMATIC_GEO_MONITORING_FOR_STATUS_VALUE' => 'AUTOMATIC_GEO_MONITORING_FOR_STATUS.VALUE',
            ],
            "order" => ['ID' => 'ASC'],
            "count_total" => true,
        ]);

        $this->arResult['COUNT'] = $vitrina->getCount();

        $error = $good = $geo = $price = $doc = 0;
        foreach ($vitrina->fetchAll() as $item) {
            if ($item['CHECKLIST_CARRIER_VALUE'] === '1' &&
                $item['CHECKLIST_FORWARDER_VALUE'] === '1'
            ) {
                $good++;
            }

            if ($item['CHECKLIST_CARRIER_VALUE'] !== '1' &&
                $item['CHECKLIST_FORWARDER_VALUE'] !== '1'
            ) {
                $error++;
            }

            if (HLBlock::isDocument($item['ID'])) {
                $doc++;
            }

            if ($item['AUTOMATIC_GEO_MONITORING_STATUS_VALUE'] === 'failed' ||
                $item['AUTOMATIC_GEO_MONITORING_FOR_STATUS_VALUE'] === 'failed' ||
                $item['AUTOMATIC_GEO_MONITORING_STATUS_VALUE'] === 'in_progress' ||
                $item['AUTOMATIC_GEO_MONITORING_FOR_STATUS_VALUE'] === 'in_progress'
            ) {
                $geo++;
            }

            if ($item['AUTOMATIC_PRICES_STATUS_VALUE'] === 'failed' ||
                $item['AUTOMATIC_PRICES_FOR_STATUS_VALUE'] === 'failed' ||
                $item['AUTOMATIC_PRICES_STATUS_VALUE'] === 'in_progress' ||
                $item['AUTOMATIC_PRICES_FOR_STATUS_VALUE'] === 'in_progress'
            ) {
                $price++;
            }
        }

        $this->arResult['COUNT_ERROR'] = $error;
        $this->arResult['COUNT_GOOD'] = $good;
        $this->arResult['COUNT_ERROR_DOC'] = $doc;
        $this->arResult['COUNT_ERROR_GEO'] = $geo;
        $this->arResult['COUNT_ERROR_PRICE'] = $price;

        if ($this->arResult['COUNT'] > 0) {
            $this->arResult['COUNT_GOOD_PERCENT'] =  round($this->arResult['COUNT_GOOD']/$this->arResult['COUNT'] * 100, 2);
            $this->arResult['COUNT_ERROR_PERCENT'] = round($this->arResult['COUNT_ERROR']/$this->arResult['COUNT'] * 100, 2);
        }
    }

    /**
     * Возвращаем фильтр по
     * кварталам
     *
     * @param string|null $number
     * @return void
     */
    protected function getFilterQuarter(string $number = null): void
    {
        switch ($number){
            case '1':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-01-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-03-31 23:59:59';
                break;
            case '2':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-04-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-06-30 23:59:59';
                break;
            case '3':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-07-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-09-30 23:59:59';
                break;
            case '4':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-10-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-12-31 23:59:59';
                break;
        }
    }

    /**
     * Возвращаем фильтр по
     * месяцам
     * @param string|null $month
     * @return void
     */
    protected function getFilterMonth(string $month = null): void
    {
        switch ($month){
            case 'january':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-01-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-01-31 23:59:59';
                break;
            case 'february':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-02-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-02-30 23:59:59';
                break;
            case 'march':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-03-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-03-31 23:59:59';
                break;
            case 'april':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-04-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-04-30 23:59:59';
                break;
            case 'may':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-05-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-05-31 23:59:59';
                break;
            case 'june':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-06-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-06-30 23:59:59';
                break;
            case 'july':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-07-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-07-31 23:59:59';
                break;
            case 'august':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-08-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-08-31 23:59:59';
                break;
            case 'september':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-09-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-09-30 23:59:59';
                break;
            case 'october':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-10-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-10-31 23:59:59';
                break;
            case 'november':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-11-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-11-30 23:59:59';
                break;
            case 'december':
                $this->arResult["FILTER"]['>=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-12-01 00:00:00';
                $this->arResult["FILTER"]['<=DATE_SHIPMENT_VALUE'] = $this->arResult['YEAR'] . '-12-31 23:59:59';
                break;
        }
    }

    /**
     * Возвращаем фильтр по
     * статистики
     *
     * @param string|null $status
     * @return void
     */
    protected function getFilterStatistics(string $status = null): void
    {
        switch ($status){
            case 'total':
                $this->arResult["FILTER"]['ACTIVE'] = 'Y';
                break;
            case 'good':
                $this->arResult["FILTER"]['CHECKLIST_CARRIER_VALUE'] = '1';
                $this->arResult["FILTER"]['CHECKLIST_FORWARDER_VALUE'] = '1';
                break;
            case 'error':
                $this->arResult["FILTER"][] = [
                    'LOGIC' => "OR",
                    'CONTRACT_EXPEDITION_STATUS.VALUE' => 'failed',
                    'CONTRACT_TRANSPORTATION_STATUS.VALUE' => 'failed',
                    'CONTRACT_ORDER_ONE_TIME_STATUS.VALUE' => 'failed',
                    'DOCUMENTS_EPD_STATUS.VALUE' => 'failed',
                    'DOCUMENTS_EXPEDITOR_STATUS.VALUE' => 'failed',
                    'DOCUMENTS_EXPEDITOR_RECEIPT_STATUS.VALUE' => 'failed',
                    'DOCUMENTS_DRIVER_APPROVALS_STATUS.VALUE' => 'failed',
                    'DOCUMENTS_APPLICATION_TRANSPORTATION_STATUS.VALUE' => 'failed',
                    'AUTOMATIC_PRICES_STATUS.VALUE' => 'failed',
                    'AUTOMATIC_GEO_MONITORING_STATUS.VALUE' => 'failed',
                    'ACCOUNTING_INVOICE_STATUS.VALUE' => 'failed',
                    'ACCOUNTING_ACT_ACCEPTANCE_STATUS.VALUE' => 'failed',
                    'ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS.VALUE' => 'failed',
                    'ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS.VALUE' => 'failed',
                    'ACCOUNTING_TAX_INVOICE_STATUS.VALUE' => 'failed',
                    'ACCOUNTING_UPD_STATUS.VALUE' => 'failed',
                    'DONKEY_STS_STATUS.VALUE' => 'failed',
                    'DONKEY_RENT_AGREEMENT_STATUS.VALUE' => 'failed',
                    'TRAILER_STS_STATUS.VALUE' => 'failed',
                    'TRAILER_RENT_AGREEMENT_STATUS.VALUE' => 'failed',
                    'TRAILER_SECONDARY_STS_STATUS.VALUE' => 'failed',
                    'TRAILER_SECONDARY_RENT_AGREEMENT_STATUS.VALUE' => 'failed',
                    'TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_STATUS.VALUE' => 'failed',
                    'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_STATUS.VALUE' => 'failed',
                    'TRAILER_SECONDARY_FREE_USAGE_STATUS.VALUE' => 'failed',
                    'TRUCK_STS_STATUS.VALUE' => 'failed',
                    'TRUCK_RENT_AGREEMENT_STATUS.VALUE' => 'failed',
                    'TRUCK_AGREEMENT_LEASING_COMPANY_STATUS.VALUE' => 'failed',
                    'TRUCK_MARRIAGE_CERTIFICATE_STATUS.VALUE' => 'failed',
                    'TRUCK_FREE_USAGE_STATUS.VALUE' => 'failed',
                    'CONTRACT_EXPEDITION_FOR_STATUS.VALUE' => 'failed',
                    'CONTRACT_TRANSPORTATION_FOR_STATUS.VALUE' => 'failed',
                    'CONTRACT_ORDER_ONE_TIME_FOR_STATUS.VALUE' => 'failed',
                    'DOCUMENTS_EPD_FOR_STATUS.VALUE' => 'failed',
                    'DOCUMENTS_EXPEDITOR_FOR_STATUS.VALUE' => 'failed',
                    'DOCUMENTS_EXPEDITOR_RECEIPT_FOR_STATUS.VALUE' => 'failed',
                    'DOCUMENTS_DRIVER_APPROVALS_FOR_STATUS.VALUE' => 'failed',
                    'DOCUMENTS_APPLICATION_TRANSPORT_FOR_STATUS.VALUE' => 'failed',
                    'AUTOMATIC_PRICES_FOR_STATUS.VALUE' => 'failed',
                    'AUTOMATIC_GEO_MONITORING_FOR_STATUS.VALUE' => 'failed',
                    'ACCOUNTING_INVOICE_FOR_STATUS.VALUE' => 'failed',
                    'ACCOUNTING_ACT_ACCEPTANCE_FOR_STATUS.VALUE' => 'failed',
                    'ACCOUNTING_ACT_MULTIPLE_TRANSPORT_FOR_STATUS.VALUE' => 'failed',
                    'ACCOUNTING_TRANSPORTATION_REGISTRY_FOR_STATUS.VALUE' => 'failed',
                    'ACCOUNTING_TAX_INVOICE_FOR_STATUS.VALUE' => 'failed',
                    'ACCOUNTING_UPD_FOR_STATUS.VALUE' => 'failed',
                    'DONKEY_STS_FOR_STATUS.VALUE' => 'failed',
                    'DONKEY_RENT_FOR_STATUS.VALUE' => 'failed',
                    'TRAILER_STS_FOR_STATUS.VALUE' => 'failed',
                    'TRAILER_RENT_AGREEMENT_FOR_STATUS.VALUE' => 'failed',
                    'TRAILER_SECONDARY_STS_FOR_STATUS.VALUE' => 'failed',
                    'TRAILER_SECONDARY_RENT_AGREEMENT_FOR_STATUS.VALUE' => 'failed',
                    'TRAILER_SECONDARY_LEASING_COMPANY_FOR_STATUS.VALUE' => 'failed',
                    'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_FOR_STATUS.VALUE' => 'failed',
                    'TRAILER_SECONDARY_FREE_USAGE_FOR_STATUS.VALUE' => 'failed',
                    'TRUCK_STS_FOR_STATUS.VALUE' => 'failed',
                    'TRUCK_RENT_AGREEMENT_FOR_STATUS.VALUE' => 'failed',
                    'TRUCK_AGREEMENT_LEASING_COMPANY_FOR_STATUS.VALUE' => 'failed',
                    'TRUCK_MARRIAGE_CERTIFICATE_FOR_STATUS.VALUE' => 'failed',
                    'TRUCK_FREE_USAGE_FOR_STATUS.VALUE' => 'failed',
                    'DONKEY_LEASING_COMPANY_FOR_STATUS.VALUE' => 'failed',
                    'DONKEY_MARRIAGE_CERTIFICATE_FOR_STATUS.VALUE' => 'failed',
                    'DONKEY_FREE_USAGE_FOR_STATUS.VALUE' => 'failed',
                    'TRAILER_LEASING_COMPANY_FOR_STATUS.VALUE' => 'failed',
                    'TRAILER_MARRIAGE_CERTIFICATE_FOR_STATUS.VALUE' => 'failed',
                    'TRAILER_FREE_USAGE_FOR_STATUS.VALUE' => 'failed',
                    'DONKEY_LEASING_COMPANY_STATUS.VALUE' => 'failed',
                    'DONKEY_MARRIAGE_CERTIFICATE_STATUS.VALUE' => 'failed',
                    'DONKEY_FREE_USAGE_STATUS.VALUE' => 'failed',
                    'TRAILER_LEASING_COMPANY_STATUS.VALUE' => 'failed',
                    'TRAILER_MARRIAGE_CERTIFICATE_STATUS.VALUE' => 'failed',
                    'TRAILER_FREE_USAGE_STATUS.VALUE' => 'failed',
                ];
                break;
            case 'geo':
                $this->arResult["FILTER"][] = [
                    'LOGIC' => "OR",
                    'AUTOMATIC_GEO_MONITORING_STATUS_VALUE' => 'failed',
                    'AUTOMATIC_GEO_MONITORING_FOR_STATUS_VALUE' => 'failed',
                ];
                break;
            case 'price':
                $this->arResult["FILTER"][] = [
                    'LOGIC' => "OR",
                    'AUTOMATIC_PRICES_STATUS_VALUE' => 'failed',
                    'AUTOMATIC_PRICES_FOR_STATUS_VALUE' => 'failed',
                ];
                break;
        }
    }
}