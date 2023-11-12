<?php

namespace Sprint\Migration;


class Version20231112200446 extends Version
{
    protected $description = "Добавляем свойства статусов для экспедитора ";

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
                'NAME' => 'Договор с лизинговой компанией (тягач) статус экспедитора',
                'CODE' => 'DONKEY_LEASING_COMPANY_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            1 => [
                'NAME' => 'Свидетельство о браке (тягач) статус экспедитора',
                'CODE' => 'DONKEY_MARRIAGE_CERTIFICATE_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            2 => [
                'NAME' => 'Договор с лизинговой компанией (прицеп) статус экспедитора',
                'CODE' => 'DONKEY_FREE_USAGE_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            3 => [
                'NAME' => 'СТС прицеп статус экспедитора',
                'CODE' => 'TRAILER_LEASING_COMPANY_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            4 => [
                'NAME' => 'Свидетельство о браке (прицепа) статус экспедитора',
                'CODE' => 'TRAILER_MARRIAGE_CERTIFICATE_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            5 => [
                'NAME' => 'Договор безвозмездного использования (прицеп) статус экспедитора',
                'CODE' => 'TRAILER_FREE_USAGE_FOR_STATUS',
                'TYPE' => 'S',
                'SORT' => 8,
            ],

            6 => [
                'NAME' => 'Договор с лизинговой компанией (тягач) статус',
                'CODE' => 'DONKEY_LEASING_COMPANY_STATUS',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            7 => [
                'NAME' => 'Свидетельство о браке (тягач) статус',
                'CODE' => 'DONKEY_MARRIAGE_CERTIFICATE_STATUS',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            8 => [
                'NAME' => 'Договор с лизинговой компанией (прицеп) статус',
                'CODE' => 'DONKEY_FREE_USAGE_STATUS',
                'TYPE' => 'S',
                'SORT' => 6,
            ],
            9 => [
                'NAME' => 'СТС прицеп статус экспедитора',
                'CODE' => 'TRAILER_LEASING_COMPANY_STATUS',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            10 => [
                'NAME' => 'Свидетельство о браке (прицепа) статус',
                'CODE' => 'TRAILER_MARRIAGE_CERTIFICATE_STATUS',
                'TYPE' => 'S',
                'SORT' => 7,
            ],
            11 => [
                'NAME' => 'Договор безвозмездного использования (прицеп) статус',
                'CODE' => 'TRAILER_FREE_USAGE_STATUS',
                'TYPE' => 'S',
                'SORT' => 8,
            ],
        ];
    }
}
