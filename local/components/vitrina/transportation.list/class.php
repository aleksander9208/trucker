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
     */
    protected function prepareResult(): void
    {
        $this->arResult['COLUMNS'] = self::getColumns();
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
                'DEVIATION_MARKET_PRICE_' => 'DEVIATION_MARKET_PRICE',
                'CHECKLIST_CARRIER_' => 'CHECKLIST_CARRIER',
                'CHECKLIST_FORWARDER_' => 'CHECKLIST_FORWARDER',
                'LINK_DOCUMENT_' => 'LINK_DOCUMENT',
            ],
            "offset" => $nav->getOffset(),
            "limit" => $nav->getLimit(),
            "order" => ['ID' => 'ASC'],
            "count_total" => true,
        ]);

        $nav->setRecordCount($vitrina->getCount());
        /**
         * TODO Удалить хардкод перед публикацией
         */
        foreach ($vitrina->fetchAll() as $item) {
            $date = explode('-', $item['DATE_SHIPMENT_VALUE']);

            $vitrinaList[] = [
                'data' => [
                    "ID" => $item['ID'],
                    "ID_TRANSPORTATION" => '<a href="#info-bar" class="info_bar-content" uk-toggle>' . $item['NAME'] . '</a>',
                    "DATE_SHIPMENT"  => $date[2] . '.' . $date[1] . '.' . $date[0],
                    "CARGO_OWNER" => $item['CARGO_OWNER_VALUE'] . '<span>' . $item['CARGO_OWNER_INN_VALUE'] . '</span>',
                    "FORWARDER" => $item['FORWARDER_VALUE'] . '<span>' . $item['FORWARDER_INN_VALUE'] . '</span>',
                    "CARRIER" => $item['CARRIER_VALUE'] . '<span>' . $item['CARRIER_INN_VALUE'] . '</span>',
                    "DEVIATION_FROM_PRICE" => $item['DEVIATION_MARKET_PRICE_VALUE'],
//                    "CHECKLIST_CARRIER" => $item['CHECKLIST_CARRIER_VALUE'],
                    "CHECKLIST_CARRIER" => '<span class="transit-good"></span>',
//                    "CHECKLIST_FORWARDER" => $item['CHECKLIST_FORWARDER_VALUE'],
                    "CHECKLIST_FORWARDER" => '<span class="transit-error"></span>',
                ],
            ];
        }

        $this->arResult["ROWS"] = $vitrinaList;
        $this->arResult["NAV"] = $nav;
    }
}