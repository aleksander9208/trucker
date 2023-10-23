<?php

namespace Sprint\Migration;


class Version20231016135409 extends Version
{
    /** @var string */
    protected $description = "Создание инфоблока для хранения отчетов";

    /** @var string */
    protected $moduleVersion = "4.4.1";

    /** @var string */
    public const CODE = 'vitrina';

    /** @var string */
    public const CODE_API = 'vitrinaApi';

    /**
     * @return void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();

        $helper->Iblock()->saveIblockType([
            'ID' => 'content',
            'LANG' => [
                'en' => [
                    'NAME' => 'Витрина ФНС',
                    'SECTION_NAME' => 'Sections',
                    'ELEMENT_NAME' => 'Elements',
                ],
                'ru' => [
                    'NAME' => 'Витрина ФНС',
                    'SECTION_NAME' => 'Разделы',
                    'ELEMENT_NAME' => 'Элементы',
                ],
            ],
        ]);

        $iblockId = $helper->Iblock()->saveIblock([
            'NAME' => 'Витрина ФНС',
            'CODE' => self::CODE,
            'API_CODE' => self::CODE_API,
            'LID' => ['s1'],
            'IBLOCK_TYPE_ID' => 'content',
            'LIST_PAGE_URL' => '',
            'DETAIL_PAGE_URL' => '#SITE_DIR#/#ELEMENT_ID#',
        ]);

        $helper->Iblock()->saveIblockFields($iblockId, [
            'CODE' => [
                'DEFAULT_VALUE' => [
                    'TRANSLITERATION' => 'Y',
                    'UNIQUE' => 'Y',
                ],
            ],
        ]);

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

        $this->outSuccess('Инфоблок создан');
    }

    /**
     * @return void
     * @throws Exceptions\HelperException
     */
    public function down(): void
    {
        $helper = $this->getHelperManager();
        $ok = $helper->Iblock()->deleteIblockIfExists(self::CODE);

        if ($ok) {
            $this->outSuccess('Инфоблок удален');
        } else {
            $this->outError('Ошибка удаления инфоблока');
        }
    }

    /**
     * @return array[]
     */
    public static function getProperty(): array
    {
        return [
            0 => [
                'NAME' => 'Дата погрузки',
                'CODE' => 'DATE_SHIPMENT',
                'TYPE' => 'S:Date',
                'SORT' => 100,
            ],
            1 => [
                'NAME' => 'Грузовладелец',
                'CODE' => 'CARGO_OWNER',
                'TYPE' => 'S',
                'SORT' => 100,
            ],
            2 => [
                'NAME' => 'ИНН грузовладельца',
                'CODE' => 'CARGO_OWNER_INN',
                'TYPE' => 'S',
                'SORT' => 100,
            ],
            3 => [
                'NAME' => 'Экспедитор',
                'CODE' => 'FORWARDER',
                'TYPE' => 'S',
                'SORT' => 100,
            ],
            4 => [
                'NAME' => 'Инн экспедитор',
                'CODE' => 'FORWARDER_INN',
                'TYPE' => 'S',
                'SORT' => 100,
            ],
            5 => [
                'NAME' => 'Перевозчик',
                'CODE' => 'CARRIER',
                'TYPE' => 'S',
                'SORT' => 100,
            ],
            6 => [
                'NAME' => 'Инн перевозчика',
                'CODE' => 'CARRIER_INN',
                'TYPE' => 'S',
                'SORT' => 100,
            ],
            7 => [
                'NAME' => 'Отклонение от рыночной цены, %',
                'CODE' => 'DEVIATION_MARKET_PRICE',
                'TYPE' => 'S',
                'SORT' => 100,
            ],
            8 => [
                'NAME' => 'Чек-лист с перевозчиком',
                'CODE' => 'CHECKLIST_CARRIER',
                'TYPE' => 'S',
                'SORT' => 100,
            ],
            9 => [
                'NAME' => 'Чек-лист с экспедитором',
                'CODE' => 'CHECKLIST_FORWARDER',
                'TYPE' => 'S',
                'SORT' => 100,
            ],
            10 => [
                'NAME' => 'Ссылка на архив документов',
                'CODE' => 'LINK_DOCUMENT',
                'TYPE' => 'S',
                'SORT' => 100,
            ],
        ];
    }
}
