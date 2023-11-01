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
                'SORT' => '100',
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
                'NAME' => 'Подписанные договоры',
                'CODE' => 'CONTRACT',
                'TYPE' => 'S',
            ],
            1 => [
                'NAME' => 'Автоматические проверки',
                'CODE' => 'AUTOMATIC_CHECKS',
                'TYPE' => 'S',
            ],
            2 => [
                'NAME' => 'Оформление перевозки',
                'CODE' => 'DOCUMENTS',
                'TYPE' => 'S',
            ],
            3 => [
                'NAME' => 'Бухгалтерские документы',
                'CODE' => 'ACCOUNTING',
                'TYPE' => 'S',
            ],
            4 => [
                'NAME' => 'Подтверждение владения (грузовик)',
                'CODE' => 'TRUCK',
                'TYPE' => 'S',
            ],
            5 => [
                'NAME' => 'Подтверждение владения (тягач)',
                'CODE' => 'DONKEY',
                'TYPE' => 'S',
            ],
            6 => [
                'NAME' => 'Подтверждение владения (прицеп)',
                'CODE' => 'MAIN_TRAILER',
                'TYPE' => 'S',
            ],
            7 => [
                'NAME' => 'Подтверждение владения второй (прицеп)',
                'CODE' => 'SECONDARY_TRAILER',
                'TYPE' => 'S',
            ],
            8 => [
                'NAME' => 'Заказ (разовая договор-заявка)',
                'CODE' => 'ORDER_ONE_TIME_CONTRACT_LINK',
                'TYPE' => 'S',
            ],
            9 => [
                'NAME' => 'Договор перевозки',
                'CODE' => 'TRANSPORTATION_CONTRACT_LINK',
                'TYPE' => 'S',
            ],
            10 => [
                'NAME' => 'Договор транспортной экспедиции',
                'CODE' => 'EXPEDITION_CONTRACT_LINK',
                'TYPE' => 'S',
            ],
            11 => [
                'NAME' => 'Подписанная ЭТрН',
                'CODE' => 'EPD_LINK',
                'TYPE' => 'S',
            ],
            12 => [
                'NAME' => 'Стоимость перевозки соответствует рыночным ценам',
                'CODE' => 'PRICES',
                'TYPE' => 'S',
            ],
            13 => [
                'NAME' => 'Подтверждение перевозки через геомониторинг',
                'CODE' => 'GEO_MONITORING',
                'TYPE' => 'S',
            ],
            14 => [
                'NAME' => 'Счёт',
                'CODE' => 'INVOICE_LINK',
                'TYPE' => 'S',
            ],
            15 => [
                'NAME' => 'Акт о приемке выполненных работ, включающий несколько перевозок',
                'CODE' => 'ACT_SERVICE_MULTIPLE_TRANSPORTATIONS_LINK',
                'TYPE' => 'S',
            ],
            16 => [
                'NAME' => 'Акт о приемке выполненных работ по услуге',
                'CODE' => 'ACT_SERVICE_ACCEPTANCE_LINK',
                'TYPE' => 'S',
            ],
            17 => [
                'NAME' => 'Реестр на перевозки',
                'CODE' => 'TRANSPORTATION_REGISTRY_LINK',
                'TYPE' => 'S',
            ],
            18 => [
                'NAME' => 'Счёт-фактура',
                'CODE' => 'TAX_INVOICE_LINK',
                'TYPE' => 'S',
            ],
            19 => [
                'NAME' => 'УПД',
                'CODE' => 'UPD_LINK',
                'TYPE' => 'S',
            ],
            20 => [
                'NAME' => 'СТС',
                'CODE' => 'STS_LINK',
                'TYPE' => 'S',
            ],
            21 => [
                'NAME' => 'Договор аренды',
                'CODE' => 'RENT_AGREEMENT_LINK',
                'TYPE' => 'S',
            ],
            22 => [
                'NAME' => 'Договор с лизинговой компанией',
                'CODE' => 'AGREEMENT_LEASING_COMPANY_LINK',
                'TYPE' => 'S',
            ],
            23 => [
                'NAME' => 'Договор безвозмездного использования',
                'CODE' => 'FREE_USAGE_AGREEMENT_LINK',
                'TYPE' => 'S',
            ],
            24 => [
                'NAME' => 'Свидетельство о браке',
                'CODE' => 'MARRIAGE_CERTIFICATE_LINK',
                'TYPE' => 'S',
            ],
            25 => [
                'NAME' => 'Подтверждение договорных отношений с водителем',
                'CODE' => 'DRIVER_APPROVALS_LINK',
                'TYPE' => 'S',
            ],
            26 => [
                'NAME' => 'Заявка на перевозку',
                'CODE' => 'APPLICATION_TRANSPORTATION_LINK',
                'TYPE' => 'S',
            ],
            27 => [
                'NAME' => 'Поручение экспедитору',
                'CODE' => 'EXPEDITOR_ORDER_LINK',
                'TYPE' => 'S',
            ],
            28 => [
                'NAME' => 'Экспедиторская расписка',
                'CODE' => 'EXPEDITOR_AGENT_RECEIPT_LINK',
                'TYPE' => 'S',
            ],
            29 => [
                'NAME' => 'Подписанные договоры/чеклист',
                'CODE' => 'CONTRACT_CHECK',
                'TYPE' => 'N',
            ],
            30 => [
                'NAME' => 'Оформление перевозки/чеклист',
                'CODE' => 'DOCUMENTS_CHECK',
                'TYPE' => 'N',
            ],
            31 => [
                'NAME' => 'Подтверждение владения (грузовик)/чеклист',
                'CODE' => 'TRUCK_CHECK',
                'TYPE' => 'N',
            ],
        ];
    }
}
