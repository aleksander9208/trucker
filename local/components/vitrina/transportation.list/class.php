<?php

use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Grid\Options;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\UI\PageNavigation;

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
        $this->arResult['COLUMNS'] = self::getColumns();
        $this->getRows();
        $this->arResult['YEAR'] = date("Y");
        $this->arResult['INFO_BAR_DOC'] = self::getDocuments();
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
    public static function getColumns(): array
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
    public static function getDocuments(): array
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
     * Формируем массив данных
     *
     * @return void
     * @throws LoaderException
     */
    public function getRows(): void
    {
        Loader::includeModule('iblock');

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
                'CARGO_OWNER_INN_VALUE' => '%' . $filterData["FIND"] . '%',
                'FORWARDER_INN_VALUE' => '%' . $filterData["FIND"] . '%',
                'CARRIER_INN_VALUE' => '%' . $filterData["FIND"] . '%',
            ];
        }

        $vitrina = \Bitrix\Iblock\Elements\ElementVitrinaApiTable::getList([
            'filter' => $this->arResult["FILTER"],
            'select' => [
                'ID',
                'NAME',
                'DATE_SHIPMENT_' => 'DATE_SHIPMENT',
                'CARGO_OWNER_' => 'CARGO_OWNER',
                'CARGO_OWNER_INN_' => 'CARGO_OWNER_INN',
                'FORWARDER_' => 'FORWARDER',
                'FORWARDER_INN_' => 'FORWARDER_INN',
                'CARRIER_' => 'CARRIER',
                'CARRIER_INN_' => 'CARRIER_INN',
                'DEVIATION_MARKET_PRICE_' => 'AUTOMATIC_PRICES',
                'CHECKLIST_CARRIER_' => 'CHECKLIST_CARRIER',
                'CHECKLIST_FORWARDER_' => 'CHECKLIST_FORWARDER',
            ],
            "offset" => $nav->getOffset(),
            "limit" => $nav->getLimit(),
            "order" => ['ID' => 'ASC'],
            "count_total" => true,
        ]);

        $this->arResult['COUNT'] = $vitrina->getCount();

        $nav->setRecordCount($vitrina->getCount());
        /**
         * TODO Удалить хардкод перед публикацией
         */
        $good = $error = 0;
        foreach ($vitrina->fetchAll() as $item) {
            $date = explode('-', $item['DATE_SHIPMENT_VALUE']);
            $goodStatus = $errorStatus = '';

            if ($item['CHECKLIST_CARRIER_VALUE'] === '1') {
                $this->arResult['COUNT_GOOD'] = $good;
                $goodStatus = '<span class="transit-good"></span>';
            }

            if ($item['CHECKLIST_FORWARDER_VALUE'] === '0') {
                $this->arResult['COUNT_ERROR'] = $error;
                $errorStatus = '<span class="transit-error"></span>';
            }

            $vitrinaList[] = [
                'data' => [
                    "ID" => $item['ID'],
                    "ID_TRANSPORTATION" => '<a href="#info-bar" class="info_bar-content" uk-toggle>' . $item['NAME'] . '</a>',
                    "DATE_SHIPMENT"  => $date[2] . '.' . $date[1] . '.' . $date[0],
                    "CARGO_OWNER" => $item['CARGO_OWNER_VALUE'] . '<span>' . $item['CARGO_OWNER_INN_VALUE'] . '</span>',
                    "FORWARDER" => $item['FORWARDER_VALUE'] . '<span>' . $item['FORWARDER_INN_VALUE'] . '</span>',
                    "CARRIER" => $item['CARRIER_VALUE'] . '<span>' . $item['CARRIER_INN_VALUE'] . '</span>',
                    "DEVIATION_FROM_PRICE" => $item['DEVIATION_MARKET_PRICE_VALUE'],
                    "CHECKLIST_CARRIER" => $goodStatus,
                    "CHECKLIST_FORWARDER" => $errorStatus,
                ],
            ];

            $good++;
            $error++;
        }

        if ($this->arResult['COUNT'] > 0) {
            $this->arResult['COUNT_GOOD_PERCENT'] = round($this->arResult['COUNT_GOOD']/$this->arResult['COUNT'] * 100, 2);
            $this->arResult['COUNT_ERROR_PERCENT'] = round($this->arResult['COUNT_ERROR']/$this->arResult['COUNT'] * 100 , 2);
        }

        $this->arResult["ROWS"] = $vitrinaList;
        $this->arResult["NAV"] = $nav;
    }
}