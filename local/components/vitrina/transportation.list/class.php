<?php

use Bitrix\Main\Context;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Grid\Options;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\UI\PageNavigation;
use Taxcom\Library\Helper\Vitrina;
use Taxcom\Library\Helper\VitrinaTable;
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
        $statistics = Vitrina::getPercent();

        $this->arResult['YEAR'] = date("Y");
        $this->arResult['COLUMNS'] = VitrinaTable::getColumns();
        $this->arResult['INFO_BAR_DOC'] = VitrinaTable::getDocuments();
        $this->arResult['INFO_BAR_DOC_FOR'] = VitrinaTable::getDocumentsFor();
        $this->arResult['FILTER_YEAR'] = VitrinaTable::getFilterYear();
        $this->arResult['COUNT'] = $statistics['COUNT'];
        $this->arResult['COUNT_ERROR'] = $statistics['COUNT_ERROR'];
        $this->arResult['COUNT_GOOD'] = $statistics['COUNT_GOOD'];
        $this->arResult['COUNT_ERROR_DOC'] = $statistics['COUNT_ERROR_DOC'];
        $this->arResult['COUNT_ERROR_GEO'] = $statistics['COUNT_ERROR_GEO'];
        $this->arResult['COUNT_ERROR_PRICE'] = $statistics['COUNT_ERROR_PRICE'];
        $this->arResult['COUNT_GOOD_PERCENT'] = $statistics['COUNT_GOOD_PERCENT'];
        $this->arResult['COUNT_ERROR_PERCENT'] = $statistics['COUNT_ERROR_PERCENT'];

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
            $statusCarrier = $statusFor = '';
            $deviation = HLBlock::getPrice($item['ID']);
            $date = explode('-', $item['DATE_SHIPMENT_VALUE']);

            if($deviation !== '') {
                $deviation = '<div class="icon-deviation_down"><span uk-icon="icon: arrow-down"></span>' . HLBlock::getPrice($item['ID']) . '</div>';
            }

            if ($item['CHECKLIST_CARRIER_VALUE'] === '1') {
                $statusCarrier = '<span class="transit-good"></span>';
            } elseif($item['CHECKLIST_CARRIER_VALUE'] === '0') {
                $statusCarrier = '<span class="transit-error"></span>';
            } elseif($item['CHECKLIST_CARRIER_VALUE'] === '2') {
                $statusCarrier = '<span class="transit-progress"></span>';
            }

            if ($item['CHECKLIST_FORWARDER_VALUE'] === '1') {
                $statusFor = '<span class="transit-good"></span>';
            } elseif ($item['CHECKLIST_FORWARDER_VALUE'] === '0') {
                $statusFor = '<span class="transit-error"></span>';
            } elseif ($item['CHECKLIST_FORWARDER_VALUE'] === '2') {
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