<?php

namespace Sprint\Migration;


class Version20231107162630 extends Version
{
    protected $description = "Добавляем статусы для свойств ";

    protected $moduleVersion = "4.6.1";

    public const CODE = 'vitrina';

    public const TYPE_ID = 'content';

    /**
     * @throws Exceptions\HelperException
     * @return void
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists(self::CODE, self::TYPE_ID);

        foreach (self::getProperty() as $property) {
            $helper->Iblock()->saveProperty($iblockId, [
                'NAME' => $property['NAME'],
                'ACTIVE' => 'Y',
                'SORT' => $property['SORT'],
                'CODE' => $property['CODE'],
                'DEFAULT_VALUE' => '',
                'PROPERTY_TYPE' => $property['TYPE'],
                'ROW_COUNT' => '1',
                'COL_COUNT' => '30',
                'LIST_TYPE' => 'C',
                'MULTIPLE' => 'N',
                'XML_ID' => NULL,
                'FILE_TYPE' => '',
                'MULTIPLE_CNT' => '5',
                'LINK_IBLOCK_ID' => '0',
                'WITH_DESCRIPTION' => 'N',
                'SEARCHABLE' => 'N',
                'FILTRABLE' => 'N',
                'IS_REQUIRED' => 'N',
                'VERSION' => '2',
                'USER_TYPE' => NULL,
                'USER_TYPE_SETTINGS' => NULL,
                'HINT' => '',
                'VALUES' => '',
            ]);
        }
    }

    /**
     * @return void
     * @throws Exceptions\HelperException
     */
    public function down(): void
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists(self::CODE, self::TYPE_ID);

        foreach (self::getProperty() as $property) {
            $helper->Iblock()->deletePropertyIfExists($iblockId, $property['CODE']);
        }
    }

    /**
     * Возвращаем список свойств
     * компании для API
     *
     * @return array
     */
    public static function getProperty(): array
    {
        return [
            0 => [
                'NAME' => 'Договор перевозки статус',
                'CODE' => 'CONTRACT_EXPEDITION_STATUS',
                'TYPE' => 'S',
                'SORT' => 2,
            ],
            1 => [
                'NAME' => 'Договор транспортной экспедиции статус',
                'CODE' => 'CONTRACT_TRANSPORTATION_STATUS',
                'TYPE' => 'S',
                'SORT' => 2,
            ],
            2 => [
                'NAME' => 'Заказ (разовая договор-заявка) статус',
                'CODE' => 'CONTRACT_ORDER_ONE_TIME_STATUS',
                'TYPE' => 'S',
                'SORT' => 2,
            ],
            3 => [
                'NAME' => 'Подписанная ЭТрН статус',
                'CODE' => 'DOCUMENTS_EPD_STATUS',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            4 => [
                'NAME' => 'Поручение экспедитору статус',
                'CODE' => 'DOCUMENTS_EXPEDITOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            5 => [
                'NAME' => 'Экспедиторская расписка статус',
                'CODE' => 'DOCUMENTS_EXPEDITOR_RECEIPT_STATUS',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            6 => [
                'NAME' => 'Подтверждение договорных отношений с водителем статус',
                'CODE' => 'DOCUMENTS_DRIVER_APPROVALS_STATUS',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            7 => [
                'NAME' => 'Заявка на перевозку статус',
                'CODE' => 'DOCUMENTS_APPLICATION_TRANSPORTATION_STATUS',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            8 => [
                'NAME' => 'Стоимость перевозки соответствует рыночным ценам статус',
                'CODE' => 'AUTOMATIC_PRICES_STATUS',
                'TYPE' => 'S',
                'SORT' => 4,
            ],
            9 => [
                'NAME' => 'Подтверждение перевозки через геомониторинг статус',
                'CODE' => 'AUTOMATIC_GEO_MONITORING_STATUS',
                'TYPE' => 'S',
                'SORT' => 4,
            ],
            10 => [
                'NAME' => 'Счёт статус',
                'CODE' => 'ACCOUNTING_INVOICE_STATUS',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            11 => [
                'NAME' => 'Акт о приемке выполненных работ по услуге статус',
                'CODE' => 'ACCOUNTING_ACT_ACCEPTANCE_STATUS',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            12 => [
                'NAME' => 'Акт о приемке выполненных работ, включающий несколько перевозок статус',
                'CODE' => 'ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_STATUS',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            13 => [
                'NAME' => 'Реестр на перевозки статус',
                'CODE' => 'ACCOUNTING_TRANSPORTATION_REGISTRY_STATUS',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            14 => [
                'NAME' => 'Счёт-фактура статус',
                'CODE' => 'ACCOUNTING_TAX_INVOICE_STATUS',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            15 => [
                'NAME' => 'УПД статус',
                'CODE' => 'ACCOUNTING_UPD_STATUS',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            16 => [
                'NAME' => 'СТС тягач статус',
                'CODE' => 'DONKEY_STS_STATUS',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            17 => [
                'NAME' => 'СТС прицеп статус',
                'CODE' => 'TRAILER_STS_STATUS',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            18 => [
                'NAME' => 'Договор аренды прицепа статус',
                'CODE' => 'TRAILER_RENT_AGREEMENT_STATUS',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            19 => [
                'NAME' => 'СТС второго (прицеп) статус',
                'CODE' => 'TRAILER_SECONDARY_STS_STATUS',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            20 => [
                'NAME' => 'Договор аренды второго (прицеп) статус',
                'CODE' => 'TRAILER_SECONDARY_RENT_AGREEMENT_STATUS',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            21 => [
                'NAME' => 'Договор с лизинговой компанией второго (прицеп) статус',
                'CODE' => 'TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_STATUS',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            22 => [
                'NAME' => 'Свидетельство о браке второго (прицепа) статус',
                'CODE' => 'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_STATUS',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            23 => [
                'NAME' => 'Договор безвозмездного использования второго (прицепа) статус',
                'CODE' => 'TRAILER_SECONDARY_FREE_USAGE_STATUS',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            24 => [
                'NAME' => 'СТС грузовика статус',
                'CODE' => 'TRUCK_STS_STATUS',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            25 => [
                'NAME' => 'Договор аренды грузовик статус',
                'CODE' => 'TRUCK_RENT_AGREEMENT_STATUS',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            26 => [
                'NAME' => 'Договор с лизинговой компанией грузовик статус',
                'CODE' => 'TRUCK_AGREEMENT_LEASING_COMPANY_STATUS',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            27 => [
                'NAME' => 'Свидетельство о браке грузовик статус',
                'CODE' => 'TRUCK_MARRIAGE_CERTIFICATE_STATUS',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            28 => [
                'NAME' => 'Договор безвозмездного использования грузовик статус',
                'CODE' => 'TRUCK_FREE_USAGE_STATUS',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
        ];
    }
}
