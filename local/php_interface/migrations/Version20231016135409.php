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
}
