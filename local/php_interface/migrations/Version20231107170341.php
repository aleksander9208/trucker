<?php

namespace Sprint\Migration;


class Version20231107170341 extends Version
{
    protected $description = "Добавление полей для экспедитора";

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
                'NAME' => 'Договор перевозки статус с заказчиком',
                'CODE' => 'CONTRACT_EXPEDITION_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 2,
            ],
            1 => [
                'NAME' => 'Договор транспортной экспедиции статус с заказчиком',
                'CODE' => 'CONTRACT_TRANSPORTATION_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 2,
            ],
            2 => [
                'NAME' => 'Заказ (разовая договор-заявка) статус с заказчиком',
                'CODE' => 'CONTRACT_ORDER_ONE_TIME_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 2,
            ],
            3 => [
                'NAME' => 'Подписанная ЭТрН с заказчиком',
                'CODE' => 'DOCUMENTS_EPD_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            4 => [
                'NAME' => 'Поручение экспедитору с заказчиком',
                'CODE' => 'DOCUMENTS_EXPEDITOR_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            5 => [
                'NAME' => 'Экспедиторская расписка с заказчиком',
                'CODE' => 'DOCUMENTS_EXPEDITOR_RECEIPT_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            6 => [
                'NAME' => 'Подтверждение договорных отношений с водителем с заказчиком',
                'CODE' => 'DOCUMENTS_DRIVER_APPROVALS_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            7 => [
                'NAME' => 'Заявка на перевозку с заказчиком',
                'CODE' => 'DOCUMENTS_APPLICATION_TRANSPORTATION_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 3,
            ],
            10 => [
                'NAME' => 'Счёт с заказчиком',
                'CODE' => 'ACCOUNTING_INVOICE_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            11 => [
                'NAME' => 'Акт о приемке выполненных работ по услуге с заказчиком',
                'CODE' => 'ACCOUNTING_ACT_ACCEPTANCE_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            12 => [
                'NAME' => 'Акт о приемке выполненных работ, включающий несколько перевозок с заказчиком',
                'CODE' => 'ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            13 => [
                'NAME' => 'Реестр на перевозки с заказчиком',
                'CODE' => 'ACCOUNTING_TRANSPORTATION_REGISTRY_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            14 => [
                'NAME' => 'Счёт-фактура с заказчиком',
                'CODE' => 'ACCOUNTING_TAX_INVOICE_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            15 => [
                'NAME' => 'УПД с заказчиком',
                'CODE' => 'ACCOUNTING_UPD_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 5,
            ],
            16 => [
                'NAME' => 'СТС тягач с заказчиком',
                'CODE' => 'DONKEY_STS_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            17 => [
                'NAME' => 'СТС прицеп с заказчиком',
                'CODE' => 'TRAILER_STS_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            18 => [
                'NAME' => 'Договор аренды прицепа с заказчиком',
                'CODE' => 'TRAILER_RENT_AGREEMENT_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            19 => [
                'NAME' => 'СТС второго (прицеп) с заказчиком',
                'CODE' => 'TRAILER_SECONDARY_STS_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            20 => [
                'NAME' => 'Договор аренды второго (прицеп) с заказчиком',
                'CODE' => 'TRAILER_SECONDARY_RENT_AGREEMENT_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            21 => [
                'NAME' => 'Договор с лизинговой компанией второго (прицеп) с заказчиком',
                'CODE' => 'TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            22 => [
                'NAME' => 'Свидетельство о браке второго (прицепа) с заказчиком',
                'CODE' => 'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            23 => [
                'NAME' => 'Договор безвозмездного использования второго (прицепа) с заказчиком',
                'CODE' => 'TRAILER_SECONDARY_FREE_USAGE_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
            24 => [
                'NAME' => 'СТС грузовика с заказчиком',
                'CODE' => 'TRUCK_STS_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            25 => [
                'NAME' => 'Договор аренды грузовик с заказчиком',
                'CODE' => 'TRUCK_RENT_AGREEMENT_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            26 => [
                'NAME' => 'Договор с лизинговой компанией грузовик с заказчиком',
                'CODE' => 'TRUCK_AGREEMENT_LEASING_COMPANY_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            27 => [
                'NAME' => 'Свидетельство о браке грузовик с заказчиком',
                'CODE' => 'TRUCK_MARRIAGE_CERTIFICATE_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
            28 => [
                'NAME' => 'Договор безвозмездного использования грузовик с заказчиком',
                'CODE' => 'TRUCK_FREE_USAGE_FORWARDER_LINK',
                'TYPE' => 'S',
                'SORT' => 9,
            ],
        ];
    }
}
