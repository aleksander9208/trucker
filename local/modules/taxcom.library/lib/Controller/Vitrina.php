<?php

declare(strict_types=1);

namespace Taxcom\Library\Controller;

use Bitrix\Main\Context;
use Bitrix\Main\Engine\AutoWire\Parameter;
use Bitrix\Main\Error;
use Bitrix\Main\UI\PageNavigation;

class Vitrina extends BaseController
{
    /**
     * @return array|Parameter[]
     */
    public function getAutoWiredParameters(): array
    {
        return array_merge(
            parent::getAutoWiredParameters(),
            [
                new Parameter(
                    PageNavigation::class,
                    static function () {
                        $pageNavigation = new PageNavigation('nav');
                        $pageNavigation
                            ->setPageSizes(range(1, 50))
                            ->setPageSize(20)
                            ->initFromUri();

                        return $pageNavigation;
                    }
                ),
            ],
        );
    }

    /**
     * Возвращаем данные по
     * перевозки
     *
     * @param int $id
     * @return array|null
     */
    public function getAction(int $id): ?array
    {
        try {
            return \Bitrix\Iblock\Elements\ElementVitrinaApiTable::getList([
                'filter' => ['ID' => $id],
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
            ])->fetch();
        } catch (\Exception $e) {
            $this->addError(new Error($e->getMessage(), $e->getCode()));

            return null;
        }
    }
}