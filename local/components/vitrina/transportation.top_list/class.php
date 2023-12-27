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
class TransportationTopList extends CBitrixComponent
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
        $request = Context::getCurrent()->getRequest();

        $filter = ['!CARGO_OWNER.VALUE' => '',];
        $select = [
            'ID',
            'CARGO_OWNER_VALUE' => 'CARGO_OWNER.VALUE',
            'CARGO_OWNER_INN_VALUE' => 'CARGO_OWNER_INN.VALUE',
            'FORWARDER_VALUE' => 'FORWARDER.VALUE',
            'FORWARDER_INN_VALUE' => 'FORWARDER_INN.VALUE',
            'CARRIER_VALUE' => 'CARRIER.VALUE',
            'CARRIER_INN_VALUE' => 'CARRIER_INN.VALUE',
        ];

        if ($request->get('top') === 'forwarders') {
            $filter = ['!FORWARDER.VALUE' => '',];
        }

        if ($request->get('top') === 'carriers') {
            $filter = ['!CARRIER.VALUE' => '',];
        }

        $filter['!STATUS_SHIPPING.VALUE'] = 'archived';

        if ($request->get('FIND') !== null) {
            $filter[] = [
                'LOGIC' => "OR",
                'NAME' => '%' . $request->get('FIND') . '%',
                'CARGO_OWNER_VALUE' => '%' . $request->get('FIND') . '%',
                'CARGO_OWNER_INN_VALUE' => '%' . $request->get('FIND') . '%',
                'FORWARDER_VALUE' => '%' . $request->get('FIND') . '%',
                'FORWARDER_INN_VALUE' => '%' . $request->get('FIND') . '%',
                'CARRIER_VALUE' => '%' . $request->get('FIND') . '%',
                'CARRIER_INN_VALUE' => '%' . $request->get('FIND') . '%',
            ];
        }

        $vitrinaTop = \Bitrix\Iblock\Elements\ElementVitrinaApiTable::getList([
            'filter' => $filter,
            'select' =>  $select,
            "order" => ['ID' => 'ASC'],
            "count_total" => true,
        ])->fetchAll();

        $top = [];
        foreach ($vitrinaTop as $item) {
            $gridCode = 'vitrina_grid_' . $item['ID'];

            $gridOptions = new Options($gridCode);
            $sort = $gridOptions->GetSorting([
                'sort' => ["ID" => "DESC"],
                'vars' => ["by" => "by", "order" => "order"]
            ]);

            $navParams = $gridOptions->GetNavParams();
            $nav = new PageNavigation($gridCode);
            $nav->allowAllRecords(false)->setPageSize(10)->initFromUri();

            $filterItemTop = $filterCompany = ['CARGO_OWNER.VALUE' => $item['CARGO_OWNER_VALUE'], '!STATUS_SHIPPING.VALUE' => 'archived'];
            $topCompanyName = $item['CARGO_OWNER_VALUE'];
            $topCompanyInn = $item['CARGO_OWNER_INN_VALUE'];

            if ($request->get('top') === 'forwarders') {
                $filterItemTop = $filterCompany = ['FORWARDER.VALUE' => $item['FORWARDER_VALUE'], '!STATUS_SHIPPING.VALUE' => 'archived'];
                $topCompanyName = $item['FORWARDER_VALUE'];
                $topCompanyInn = $item['FORWARDER_INN_VALUE'];
            }

            if ($request->get('top') === 'carriers') {
                $filterItemTop = $filterCompany = ['CARRIER.VALUE' => $item['CARRIER_VALUE'], '!STATUS_SHIPPING.VALUE' => 'archived'];
                $topCompanyName = $item['CARRIER_VALUE'];
                $topCompanyInn = $item['CARRIER_INN_VALUE'];
            }

            $filterItemTop[] = [
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
                '!STATUS_SHIPPING.VALUE' => 'archived',
            ];

            $vitrina = \Bitrix\Iblock\Elements\ElementVitrinaApiTable::getList([
                'filter' => $filterItemTop,
                'select' => [
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

            $vitrinaList = [];

            foreach ($vitrina->fetchAll() as $shipping) {
                $statusCarrier = $statusFor = '';
                $deviation = HLBlock::getPrice($shipping['ID']);
                $date = explode('-', $shipping['DATE_SHIPMENT_VALUE']);

                if($deviation !== '') {
                    $deviation = '<div class="icon-deviation_down"><span uk-icon="icon: arrow-down"></span>' . HLBlock::getPrice($shipping['ID']) . '</div>';
                }

                if ($shipping['CHECKLIST_CARRIER_VALUE'] === '1') {
                    $statusCarrier = '<span class="transit-good"></span>';
                } elseif($shipping['CHECKLIST_CARRIER_VALUE'] === '0') {
                    $statusCarrier = '<span class="transit-error"></span>';
                } elseif ($item['CHECKLIST_CARRIER_VALUE'] === '2') {
                    $statusCarrier = '<span class="transit-progress"></span>';
                }

                if ($shipping['CHECKLIST_FORWARDER_VALUE'] === '1') {
                    $statusFor = '<span class="transit-good"></span>';
                } elseif ($shipping['CHECKLIST_FORWARDER_VALUE'] === '0') {
                    $statusFor = '<span class="transit-error"></span>';
                } elseif ($item['CHECKLIST_FORWARDER_VALUE'] === '2') {
                    $statusFor = '<span class="transit-progress"></span>';
                }

                $vitrinaList[] = [
                    'data' => [
                        "ID" => $shipping['ID'],
                        "ID_TRANSPORTATION" => '<a href="#info-bar" class="info_bar-content" uk-toggle>' . $shipping['NAME'] . '</a>',
                        "DATE_SHIPMENT"  => $date[2] . '.' . $date[1] . '.' . $date[0],
                        "CARGO_OWNER" => $shipping['CARGO_OWNER_VALUE'] . '<span>' . $shipping['CARGO_OWNER_INN_VALUE'] . '</span>',
                        "FORWARDER" => $shipping['FORWARDER_VALUE'] . '<span>' . $shipping['FORWARDER_INN_VALUE'] . '</span>',
                        "CARRIER" => $shipping['CARRIER_VALUE'] . '<span>' . $shipping['CARRIER_INN_VALUE'] . '</span>',
                        "DEVIATION_FROM_PRICE" => $deviation,
                        "CHECKLIST_CARRIER" => $statusCarrier,
                        "CHECKLIST_FORWARDER" => $statusFor,
                    ],
                ];
            }

            $top[$topCompanyInn] = [
                'NAME' => $topCompanyName,
                'INN' => $topCompanyInn,
                'GRID_CODE' => $gridCode,
                'SUM_COUNT' => Vitrina::getCountElement($filterCompany),
                'COUNT' => $vitrina->getCount(),
                'SHIPPING' => $vitrinaList,
                'NAV' => $nav,
            ];
        }

        usort($top, function($a,$b){
            return ($b['COUNT']-$a['COUNT']);
        });

        $this->arResult["ROWS"] = $top;
    }
}