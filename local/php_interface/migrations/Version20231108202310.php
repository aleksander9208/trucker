<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity;

class Version20231108202310 extends Version
{
    protected $description = "Добавляем справочник для хранения ссылок";

    protected $moduleVersion = "4.6.1";

    public function up()
    {
        Loader::includeModule("highloadblock");

        $hlblock = HL\HighloadBlockTable::add([
            'NAME' => "FnsLinkDocuments",
            'TABLE_NAME' => "fns_link_documents",
        ]);
        $HLBLOCK_ID = $hlblock->getId();

        $obUserField  = new \CUserTypeEntity;
        $arFields = [
            0 => [
                'ENTITY_ID' => 'HLBLOCK_'.$HLBLOCK_ID, // Строковый идентификатор сущности
                'FIELD_NAME' => 'UF_ID_ELEMENT', // Код поля
                'USER_TYPE_ID' => 'integer',  // Тип
                'XML_ID' => '', // Внешний код
                'SORT' => '100', // Сортировка
                'MULTIPLE' => NULL,  // Множественное
                'MANDATORY' => 'N', // Обязательное
                'SHOW_FILTER' => 'E', // Показывать в фильтре списка
                'SHOW_IN_LIST' => NULL, // Показывать в списке
                'EDIT_IN_LIST' => NULL, // Разрешить редактирование в списке
                'IS_SEARCHABLE' => NULL, // Индексировать модулем поиска
                'SETTINGS' => [ // Дополнительные настройки
                    'DEFAULT_VALUE' => '', // Значение по умолчанию
                    'SIZE' => '60', // Ширина поля
                    'ROWS' => '1', // Высота поля
                    'MIN_LENGTH' => '0', //Минимальная длина строки
                    'MAX_LENGTH' => '0', // Максимальная длина строки
                    'REGEXP' => '', // Регулярное выражение для проверки значения
                ],
            ],
            1 => [
                'ENTITY_ID' => 'HLBLOCK_'.$HLBLOCK_ID, // Строковый идентификатор сущности
                'FIELD_NAME' => 'UF_ID_GROUP', // Код поля
                'USER_TYPE_ID' => 'string',  // Тип
                'XML_ID' => '', // Внешний код
                'SORT' => '100', // Сортировка
                'MULTIPLE' => NULL,  // Множественное
                'MANDATORY' => 'N', // Обязательное
                'SHOW_FILTER' => 'E', // Показывать в фильтре списка
                'SHOW_IN_LIST' => NULL, // Показывать в списке
                'EDIT_IN_LIST' => NULL, // Разрешить редактирование в списке
                'IS_SEARCHABLE' => NULL, // Индексировать модулем поиска
                'SETTINGS' => [ // Дополнительные настройки
                    'DEFAULT_VALUE' => '', // Значение по умолчанию
                    'SIZE' => '60', // Ширина поля
                    'ROWS' => '1', // Высота поля
                    'MIN_LENGTH' => '0', //Минимальная длина строки
                    'MAX_LENGTH' => '0', // Максимальная длина строки
                    'REGEXP' => '', // Регулярное выражение для проверки значения
                ],
            ],
            6 => [
                'ENTITY_ID' => 'HLBLOCK_'.$HLBLOCK_ID, // Строковый идентификатор сущности
                'FIELD_NAME' => 'UF_GROUP_NAME', // Код поля
                'USER_TYPE_ID' => 'string',  // Тип
                'XML_ID' => '', // Внешний код
                'SORT' => '100', // Сортировка
                'MULTIPLE' => NULL,  // Множественное
                'MANDATORY' => 'N', // Обязательное
                'SHOW_FILTER' => 'E', // Показывать в фильтре списка
                'SHOW_IN_LIST' => NULL, // Показывать в списке
                'EDIT_IN_LIST' => NULL, // Разрешить редактирование в списке
                'IS_SEARCHABLE' => NULL, // Индексировать модулем поиска
                'SETTINGS' => [ // Дополнительные настройки
                    'DEFAULT_VALUE' => '', // Значение по умолчанию
                    'SIZE' => '60', // Ширина поля
                    'ROWS' => '1', // Высота поля
                    'MIN_LENGTH' => '0', //Минимальная длина строки
                    'MAX_LENGTH' => '0', // Максимальная длина строки
                    'REGEXP' => '', // Регулярное выражение для проверки значения
                ],
            ],
            2 => [
                'ENTITY_ID' => 'HLBLOCK_'.$HLBLOCK_ID, // Строковый идентификатор сущности
                'FIELD_NAME' => 'UF_NAME_LINK', // Код поля
                'USER_TYPE_ID' => 'string',  // Тип
                'XML_ID' => '', // Внешний код
                'SORT' => '100', // Сортировка
                'MULTIPLE' => NULL,  // Множественное
                'MANDATORY' => 'N', // Обязательное
                'SHOW_FILTER' => 'E', // Показывать в фильтре списка
                'SHOW_IN_LIST' => NULL, // Показывать в списке
                'EDIT_IN_LIST' => NULL, // Разрешить редактирование в списке
                'IS_SEARCHABLE' => NULL, // Индексировать модулем поиска
                'SETTINGS' => [ // Дополнительные настройки
                    'DEFAULT_VALUE' => '', // Значение по умолчанию
                    'SIZE' => '60', // Ширина поля
                    'ROWS' => '1', // Высота поля
                    'MIN_LENGTH' => '0', //Минимальная длина строки
                    'MAX_LENGTH' => '0', // Максимальная длина строки
                    'REGEXP' => '', // Регулярное выражение для проверки значения
                ],
            ],
            3 => [
                'ENTITY_ID' => 'HLBLOCK_'.$HLBLOCK_ID, // Строковый идентификатор сущности
                'FIELD_NAME' => 'UF_LINK', // Код поля
                'USER_TYPE_ID' => 'string',  // Тип
                'XML_ID' => '', // Внешний код
                'SORT' => '100', // Сортировка
                'MULTIPLE' => NULL,  // Множественное
                'MANDATORY' => 'N', // Обязательное
                'SHOW_FILTER' => 'E', // Показывать в фильтре списка
                'SHOW_IN_LIST' => NULL, // Показывать в списке
                'EDIT_IN_LIST' => NULL, // Разрешить редактирование в списке
                'IS_SEARCHABLE' => NULL, // Индексировать модулем поиска
                'SETTINGS' => [ // Дополнительные настройки
                    'DEFAULT_VALUE' => '', // Значение по умолчанию
                    'SIZE' => '100', // Ширина поля
                    'ROWS' => '15', // Высота поля
                    'MIN_LENGTH' => '0', //Минимальная длина строки
                    'MAX_LENGTH' => '0', // Максимальная длина строки
                    'REGEXP' => '', // Регулярное выражение для проверки значения
                ],
            ],
            4 => [
                'ENTITY_ID' => 'HLBLOCK_'.$HLBLOCK_ID, // Строковый идентификатор сущности
                'FIELD_NAME' => 'UF_ATTACHMENTS', // Код поля
                'USER_TYPE_ID' => 'string',  // Тип
                'XML_ID' => '', // Внешний код
                'SORT' => '100', // Сортировка
                'MULTIPLE' => NULL,  // Множественное
                'MANDATORY' => 'N', // Обязательное
                'SHOW_FILTER' => 'E', // Показывать в фильтре списка
                'SHOW_IN_LIST' => NULL, // Показывать в списке
                'EDIT_IN_LIST' => NULL, // Разрешить редактирование в списке
                'IS_SEARCHABLE' => NULL, // Индексировать модулем поиска
                'SETTINGS' => [ // Дополнительные настройки
                    'DEFAULT_VALUE' => '', // Значение по умолчанию
                    'SIZE' => '60', // Ширина поля
                    'ROWS' => '1', // Высота поля
                    'MIN_LENGTH' => '0', //Минимальная длина строки
                    'MAX_LENGTH' => '0', // Максимальная длина строки
                    'REGEXP' => '', // Регулярное выражение для проверки значения
                ],
            ],
            5 => [
                'ENTITY_ID' => 'HLBLOCK_'.$HLBLOCK_ID, // Строковый идентификатор сущности
                'FIELD_NAME' => 'UF_EDM_ATTACHMENTS', // Код поля
                'USER_TYPE_ID' => 'string',  // Тип
                'XML_ID' => '', // Внешний код
                'SORT' => '100', // Сортировка
                'MULTIPLE' => NULL,  // Множественное
                'MANDATORY' => 'N', // Обязательное
                'SHOW_FILTER' => 'E', // Показывать в фильтре списка
                'SHOW_IN_LIST' => NULL, // Показывать в списке
                'EDIT_IN_LIST' => NULL, // Разрешить редактирование в списке
                'IS_SEARCHABLE' => NULL, // Индексировать модулем поиска
                'SETTINGS' => [ // Дополнительные настройки
                    'DEFAULT_VALUE' => '', // Значение по умолчанию
                    'SIZE' => '60', // Ширина поля
                    'ROWS' => '1', // Высота поля
                    'MIN_LENGTH' => '0', //Минимальная длина строки
                    'MAX_LENGTH' => '0', // Максимальная длина строки
                    'REGEXP' => '', // Регулярное выражение для проверки значения
                ],
            ],
        ];

        foreach ($arFields as $field) {
            $ID = $obUserField->Add($field);
        }
    }

    public function down()
    {
        //your code ...
    }
}
