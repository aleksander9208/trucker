<?php

namespace Sprint\Migration;


class Version20231111221138 extends Version
{
    protected $description = "Добавляем свойства статусов и чек листов для экспедитора";

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
            //чеклист
            0 => [
                'NAME' => 'Подписанные договоры/чеклист экспедитора',
                'CODE' => 'CONTRACT_FOR_CHECK',
                'TYPE' => 'S',
                'SORT' => 2,
            ],
            1 => [
                'NAME' => 'Оформление перевозки/чеклист экспедитора',
                'CODE' => 'DOCUMENTS_FOR_CHECK',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            2 => [
                'NAME' => 'Автоматические проверки/чеклист экспедитора',
                'CODE' => 'AUTOMATIC_FOR_CHECKS',
                'TYPE' => 'S',
                'SORT' => 4,
            ],
            3 => [
                'NAME' => 'Бухгалтерские документы/чек-лист экспедитора',
                'CODE' => 'ACCOUNTING_FOR_CHECKS',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            4 => [
                'NAME' => 'Подтверждение владения (тягач) чек/лист экспедитора',
                'CODE' => 'DONKEY_FOR_CHECKS',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            5 => [
                'NAME' => 'Подтверждение владения (прицеп) чек/лист экспедитора',
                'CODE' => 'TRAILER_FOR_CHECKS',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            6 => [
                'NAME' => 'Подтверждение владения второго (прицеп)/чеклист экспедитора',
                'CODE' => 'TRAILER_SECONDARY_FOR_CHECKS',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            7 => [
                'NAME' => 'Подтверждение владения грузовик/чеклист экспедитора',
                'CODE' => 'TRUCK_FOR_CHECKS',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            //Статусы
            8 => [
                'NAME' => 'Договор перевозки статус экспедитора',
                'CODE' => 'CONTRACT_EXPEDITION_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 2,
            ],
            9 => [
                'NAME' => 'Договор транспортной экспедиции статус экспедитора',
                'CODE' => 'CONTRACT_TRANSPORTATION_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 2,
            ],
            10 => [
                'NAME' => 'Заказ (разовая договор-заявка) статус экспедитора',
                'CODE' => 'CONTRACT_ORDER_ONE_TIME_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 2,
            ],
            11 => [
                'NAME' => 'Подписанная ЭТрН статус экспедитора',
                'CODE' => 'DOCUMENTS_EPD_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            12 => [
                'NAME' => 'Поручение экспедитору статус экспедитора',
                'CODE' => 'DOCUMENTS_EXPEDITOR_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            13 => [
                'NAME' => 'Экспедиторская расписка статус экспедитора',
                'CODE' => 'DOCUMENTS_EXPEDITOR_RECEIPT_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            14 => [
                'NAME' => 'Подтверждение договорных отношений с водителем статус экспедитора',
                'CODE' => 'DOCUMENTS_DRIVER_APPROVALS_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            15 => [
                'NAME' => 'Заявка на перевозку статус экспедитора',
                'CODE' => 'DOCUMENTS_APPLICATION_TRANSPORT_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            16 => [
                'NAME' => 'Стоимость перевозки соответствует рыночным ценам статус экспедитора',
                'CODE' => 'AUTOMATIC_PRICES_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 4,
            ],
            17 => [
                'NAME' => 'Подтверждение перевозки через геомониторинг статус экспедитора',
                'CODE' => 'AUTOMATIC_GEO_MONITORING_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 4,
            ],
            18 => [
                'NAME' => 'Счёт статус экспедитора',
                'CODE' => 'ACCOUNTING_INVOICE_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            19 => [
                'NAME' => 'Акт о приемке выполненных работ по услуге статус экспедитора',
                'CODE' => 'ACCOUNTING_ACT_ACCEPTANCE_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            20 => [
                'NAME' => 'Акт о приемке выполненных работ, включающий несколько перевозок статус экспедитора',
                'CODE' => 'ACCOUNTING_ACT_MULTIPLE_TRANSPORT_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            21 => [
                'NAME' => 'Реестр на перевозки статус экспедитора',
                'CODE' => 'ACCOUNTING_TRANSPORTATION_REGISTRY_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            22 => [
                'NAME' => 'Счёт-фактура статус экспедитора',
                'CODE' => 'ACCOUNTING_TAX_INVOICE_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            23 => [
                'NAME' => 'УПД статус экспедитора',
                'CODE' => 'ACCOUNTING_UPD_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            24 => [
                'NAME' => 'СТС тягач статус экспедитора',
                'CODE' => 'DONKEY_STS_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            25 => [
                'NAME' => 'Договор аренды тягач статус экспедитора',
                'CODE' => 'DONKEY_RENT_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            26 => [
                'NAME' => 'СТС прицеп статус экспедитора',
                'CODE' => 'TRAILER_STS_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            27 => [
                'NAME' => 'Договор аренды прицепа статус экспедитора',
                'CODE' => 'TRAILER_RENT_AGREEMENT_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            28 => [
                'NAME' => 'СТС второго (прицеп) статус экспедитора',
                'CODE' => 'TRAILER_SECONDARY_STS_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            29 => [
                'NAME' => 'Договор аренды второго (прицеп) статус экспедитора',
                'CODE' => 'TRAILER_SECONDARY_RENT_AGREEMENT_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            30 => [
                'NAME' => 'Договор с лизинговой компанией второго (прицеп) статус экспедитора',
                'CODE' => 'TRAILER_SECONDARY_LEASING_COMPANY_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            31 => [
                'NAME' => 'Свидетельство о браке второго (прицепа) статус экспедитора',
                'CODE' => 'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            32 => [
                'NAME' => 'Договор безвозмездного использования второго (прицепа) статус экспедитора',
                'CODE' => 'TRAILER_SECONDARY_FREE_USAGE_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            33 => [
                'NAME' => 'СТС грузовика статус экспедитора',
                'CODE' => 'TRUCK_STS_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            34 => [
                'NAME' => 'Договор аренды грузовик статус экспедитора',
                'CODE' => 'TRUCK_RENT_AGREEMENT_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            35 => [
                'NAME' => 'Договор с лизинговой компанией грузовик статус экспедитора',
                'CODE' => 'TRUCK_AGREEMENT_LEASING_COMPANY_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            36 => [
                'NAME' => 'Свидетельство о браке грузовик статус экспедитора',
                'CODE' => 'TRUCK_MARRIAGE_CERTIFICATE_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            37 => [
                'NAME' => 'Договор безвозмездного использования грузовик статус экспедитора',
                'CODE' => 'TRUCK_FREE_USAGE_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            //Номерные знаки
            38 => [
                'NAME' => 'Номерной знак тягач экспедитора',
                'CODE' => 'DONKEY_LICENSE_FOR_PLATE',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            39 => [
                'NAME' => 'Номерной знак прицеп',
                'CODE' => 'TRAILER_LICENSE_FOR_PLATE',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            40 => [
                'NAME' => 'Номерной знак второго (прицеп)',
                'CODE' => 'TRAILER_SECONDARY_LICENSE_FOR_PLATE',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            41 => [
                'NAME' => 'Номерной знак грузовик',
                'CODE' => 'TRUCK_LICENSE_FOR_PLATE',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            42 => [
                'NAME' => 'Статус перевозки',
                'CODE' => 'STATUS_SHIPPING',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
        ];
    }
}
