<?php

namespace Sprint\Migration;


class Version20231031154319 extends Version
{
    protected $description = "Добавляем свойства документов";

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
                'NAME' => 'Обьект json, который получили',
                'CODE' => 'JSON',
                'TYPE' => 'S',
                'SORT' => 1,
            ],
            1 => [
                'NAME' => 'Дата погрузки',
                'CODE' => 'DATE_SHIPMENT',
                'TYPE' => 'S:Date',
                'SORT' => 1,
            ],
            2 => [
                'NAME' => 'Перевозчик',
                'CODE' => 'CARRIER',
                'TYPE' => 'S',
                'SORT' => 1,
            ],
            4 => [
                'NAME' => 'Инн перевозчика',
                'CODE' => 'CARRIER_INN',
                'TYPE' => 'S',
                'SORT' => 1,
            ],
            5 => [
                'NAME' => 'Грузовладелец',
                'CODE' => 'CARGO_OWNER',
                'TYPE' => 'S',
                'SORT' => 1,
            ],
            6 => [
                'NAME' => 'ИНН грузовладельца',
                'CODE' => 'CARGO_OWNER_INN',
                'TYPE' => 'S',
                'SORT' => 1,
            ],
            7 => [
                'NAME' => 'Экспедитор',
                'CODE' => 'FORWARDER',
                'TYPE' => 'S',
                'SORT' => 1,
            ],
            8 => [
                'NAME' => 'Инн экспедитор',
                'CODE' => 'FORWARDER_INN',
                'TYPE' => 'S',
                'SORT' => 1,
            ],
            9 => [
                'NAME' => 'Подписанные договоры/чеклист',
                'CODE' => 'CONTRACT_CHECK',
                'TYPE' => 'N',
                'SORT' => 2,
            ],
            10 => [
                'NAME' => 'Договор транспортной экспедиции',
                'CODE' => 'CONTRACT_EXPEDITION_LINK',
                'TYPE' => 'S',
                'SORT' => 2,
            ],
            11 => [
                'NAME' => 'Договор перевозки',
                'CODE' => 'CONTRACT_TRANSPORTATION_LINK',
                'TYPE' => 'S',
                'SORT' => 2,
            ],
            12 => [
                'NAME' => 'Заказ (разовая договор-заявка)',
                'CODE' => 'CONTRACT_ORDER_ONE_TIME_LINK',
                'TYPE' => 'S',
                'SORT' => 2,
            ],
            13 => [
                'NAME' => 'Оформление перевозки/чеклист',
                'CODE' => 'DOCUMENTS_CHECK',
                'TYPE' => 'N',
                'SORT' => 3,
            ],
            14 => [
                'NAME' => 'Подписанная ЭТрН',
                'CODE' => 'DOCUMENTS_EPD_LINK',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            15 => [
                'NAME' => 'Поручение экспедитору',
                'CODE' => 'DOCUMENTS_EXPEDITOR_LINK',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            16 => [
                'NAME' => 'Экспедиторская расписка',
                'CODE' => 'DOCUMENTS_EXPEDITOR_RECEIPT_LINK',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            17 => [
                'NAME' => 'Подтверждение договорных отношений с водителем',
                'CODE' => 'DOCUMENTS_DRIVER_APPROVALS_LINK',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            18 => [
                'NAME' => 'Заявка на перевозку',
                'CODE' => 'DOCUMENTS_APPLICATION_TRANSPORTATION_LINK',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            19 => [
                'NAME' => 'Автоматические проверки/чеклист',
                'CODE' => 'AUTOMATIC_CHECKS',
                'TYPE' => 'S',
                'SORT' => 4,
            ],
            20 => [
                'NAME' => 'Стоимость перевозки соответствует рыночным ценам',
                'CODE' => 'AUTOMATIC_PRICES',
                'TYPE' => 'S',
                'SORT' => 4,
            ],
            21 => [
                'NAME' => 'Подтверждение перевозки через геомониторинг',
                'CODE' => 'AUTOMATIC_GEO_MONITORING',
                'TYPE' => 'S',
                'SORT' => 4,
            ],
            22 => [
                'NAME' => 'Бухгалтерские документы/чек-лист',
                'CODE' => 'ACCOUNTING_CHECKS',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            23 => [
                'NAME' => 'Счёт',
                'CODE' => 'ACCOUNTING_INVOICE_LINK',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            24 => [
                'NAME' => 'Акт о приемке выполненных работ по услуге',
                'CODE' => 'ACCOUNTING_ACT_ACCEPTANCE_LINK',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            25 => [
                'NAME' => 'Акт о приемке выполненных работ, включающий несколько перевозок',
                'CODE' => 'ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            26 => [
                'NAME' => 'Реестр на перевозки',
                'CODE' => 'ACCOUNTING_TRANSPORTATION_REGISTRY_LINK',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            27 => [
                'NAME' => 'Счёт-фактура',
                'CODE' => 'ACCOUNTING_TAX_INVOICE_LINK',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            28 => [
                'NAME' => 'УПД',
                'CODE' => 'ACCOUNTING_UPD_LINK',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            29 => [
                'NAME' => 'Подтверждение владения (тягач)',
                'CODE' => 'DONKEY_CHECKS',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            30 => [
                'NAME' => 'Номерной знак тягач',
                'CODE' => 'DONKEY_LICENSE_PLATE',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            31 => [
                'NAME' => 'СТС тягач',
                'CODE' => 'DONKEY_STS_LINK',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            32 => [
                'NAME' => 'Подтверждение владения (прицеп)',
                'CODE' => 'TRAILER_CHECKS',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            33 => [
                'NAME' => 'Номерной знак прицеп',
                'CODE' => 'TRAILER_LICENSE_PLATE',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            34 => [
                'NAME' => 'СТС прицеп',
                'CODE' => 'TRAILER_STS_LINK',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            35 => [
                'NAME' => 'Договор аренды прицепа',
                'CODE' => 'TRAILER_RENT_AGREEMENT_LINK',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            36 => [
                'NAME' => 'Подтверждение владения второго (прицеп)/чеклист',
                'CODE' => 'TRAILER_SECONDARY_CHECKS',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            37 => [
                'NAME' => 'Номерной знак второго (прицеп)',
                'CODE' => 'TRAILER_SECONDARY_LICENSE_PLATE',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            38 => [
                'NAME' => 'СТС второго (прицеп)',
                'CODE' => 'TRAILER_SECONDARY_STS_LINK',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            39 => [
                'NAME' => 'Договор аренды второго (прицеп)',
                'CODE' => 'TRAILER_SECONDARY_RENT_AGREEMENT_LINK',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            40 => [
                'NAME' => 'Договор с лизинговой компанией второго (прицеп)',
                'CODE' => 'TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            41 => [
                'NAME' => 'Свидетельство о браке второго (прицепа)',
                'CODE' => 'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            42 => [
                'NAME' => 'Договор безвозмездного использования второго (прицепа)',
                'CODE' => 'TRAILER_SECONDARY_FREE_USAGE_LINK',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            43 => [
                'NAME' => 'Подтверждение владения грузовик/чеклист',
                'CODE' => 'TRUCK_CHECKS',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            44 => [
                'NAME' => 'Номерной знак грузовик',
                'CODE' => 'TRUCK_LICENSE_PLATE',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            45 => [
                'NAME' => 'СТС второго (прицеп)',
                'CODE' => 'TRUCK_STS_LINK',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            46 => [
                'NAME' => 'Договор аренды грузовик',
                'CODE' => 'TRUCK_RENT_AGREEMENT_LINK',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            47 => [
                'NAME' => 'Договор с лизинговой компанией грузовик',
                'CODE' => 'TRUCK_AGREEMENT_LEASING_COMPANY_LINK',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            48 => [
                'NAME' => 'Свидетельство о браке грузовик',
                'CODE' => 'TRUCK_MARRIAGE_CERTIFICATE_LINK',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            49 => [
                'NAME' => 'Договор безвозмездного использования грузовик',
                'CODE' => 'TRUCK_FREE_USAGE_LINK',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            51 => [
                'NAME' => 'Чек-лист с перевозчиком',
                'CODE' => 'CHECKLIST_CARRIER',
                'TYPE' => 'S',
                'SORT' => 1,
            ],
            52 => [
                'NAME' => 'Чек-лист с экспедитором',
                'CODE' => 'CHECKLIST_FORWARDER',
                'TYPE' => 'S',
                'SORT' => 1,
            ],
        ];
    }
}
