<?php
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

?>
<div class="uk-container uk-container-large">

    <form class="form_filter">
        <div class="form_filter-date">
            <div class="form_cvartal">
                <div class="filter_date-cvartal">
                    <div class="filter_cvartal-main">
                        <label class="filter_checkbox">
                            <input class="uk-checkbox" type="checkbox">
                            <span>1-й квартал</span>
                        </label>
                    </div>
                    <div class="filter-month">
                        <div class="filter_cvartal-month">
                            <label class="filter_checkbox">
                                <input class="uk-checkbox" type="checkbox">
                                <span>янв</span>
                            </label>
                        </div>
                        <div class="filter_cvartal-month">
                            <label class="filter_checkbox">
                                <input class="uk-checkbox" type="checkbox">
                                <span>фев</span>
                            </label>
                        </div>
                        <div class="filter_cvartal-month">
                            <label class="filter_checkbox">
                                <input class="uk-checkbox" type="checkbox">
                                <span>мар</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="filter_date-cvartal">
                    <div class="filter_cvartal-main">
                        <label class="filter_checkbox">
                            <input class="uk-checkbox" type="checkbox">
                            <span>2-й квартал</span>
                        </label>
                    </div>
                    <div class="filter-month">
                        <div class="filter_cvartal-month">
                            <label class="filter_checkbox">
                                <input class="uk-checkbox" type="checkbox">
                                <span>апр</span>
                            </label>
                        </div>
                        <div class="filter_cvartal-month">
                            <label class="filter_checkbox">
                                <input class="uk-checkbox" type="checkbox">
                                <span>май</span>
                            </label>
                        </div>
                        <div class="filter_cvartal-month">
                            <label class="filter_checkbox">
                                <input class="uk-checkbox" type="checkbox">
                                <span>июн</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="filter_date-cvartal">
                    <div class="filter_cvartal-main">
                        <label class="filter_checkbox">
                            <input class="uk-checkbox" type="checkbox">
                            <span>3-й квартал</span>
                        </label>
                    </div>
                    <div class="filter-month">
                        <div class="filter_cvartal-month">
                            <label class="filter_checkbox">
                                <input class="uk-checkbox" type="checkbox">
                                <span>июл</span>
                            </label>
                        </div>
                        <div class="filter_cvartal-month">
                            <label class="filter_checkbox">
                                <input class="uk-checkbox" type="checkbox">
                                <span>авг</span>
                            </label>
                        </div>
                        <div class="filter_cvartal-month">
                            <label class="filter_checkbox">
                                <input class="uk-checkbox" type="checkbox">
                                <span>сен</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="filter_date-cvartal">
                    <div class="filter_cvartal-main">
                        <label class="filter_checkbox">
                            <input class="uk-checkbox" type="checkbox">
                            <span>4-й квартал</span>
                        </label>
                    </div>
                    <div class="filter-month">
                        <div class="filter_cvartal-month">
                            <label class="filter_checkbox">
                                <input class="uk-checkbox" type="checkbox">
                                <span>окт</span>
                            </label>
                        </div>
                        <div class="filter_cvartal-month">
                            <label class="filter_checkbox">
                                <input class="uk-checkbox" type="checkbox">
                                <span>ноя</span>
                            </label>
                        </div>
                        <div class="filter_cvartal-month">
                            <label class="filter_checkbox">
                                <input class="uk-checkbox" type="checkbox">
                                <span>дек</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form_date">
                <div class="form_date-year">
                    <label class ="filter_checkbox">
                        <input class="uk-checkbox" type="checkbox">
                        <span>Весь год</span>
                    </label>
                </div>
                <div class="form_date-period">
                    <label>
                        <input class="uk-date" type="date">
                    </label>
                </div>
            </div>
        </div>

        <div class="filter-search">
            <div class="uk-search">
                <input class="uk-search-input" type="search" placeholder="Поиск по организации или ИНН">
                <a href="" class="uk-search-icon-flip" uk-search-icon></a>
            </div>
        </div>
    </form>

    <div class="statistics">
        <div class="statistics_result">
            <div class="statistics-info">
                <div class="statistics-title">6 470</div>
                выполнено всего перевозок
            </div>
        </div>
        <div class="statistics_good">
            <div class="statistics-info">
                <div class="statistics-title">
                    5 879<sup><span>95,48%</span></sup>
                </div>
                проверка по чек-листу пройдена
            </div>
        </div>
        <div class="statistics_error">
            <div class="statistics-info">
                <div class="statistics-title">
                    591<sup><span>4,52%</span></sup>
                </div>
                проверка по чек-листу не пройдена
            </div>
            <div class="statistics_error-info">
                <div>
                    <span>129</span> с недостающими документами
                </div>
                <div>
                    <span>57</span> нет подтверждения через геомониторинг
                </div>
                <div>
                    <span>405</span> цена не соответствует рыночной
                </div>
            </div>
            <div class="statistics_error-search">
                <label>
                    <span uk-icon="icon: search"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="filter_list">
        <div class="filter_list-top">
            <div class="filter_list-top-error-organizations">
                <label class="filter_top-organizations">
                    <input class="uk-checkbox" type="checkbox">
                    <span>Топ проблемных организаций</span>
                </label>
                <div class="filter_list-type-organizations">
                    <label>
                        <input class="uk-checkbox" type="checkbox">
                        <span>Грузовладельцы</span>
                    </label>
                    <label>
                        <input class="uk-checkbox" type="checkbox">
                        <span>Перевозчики</span>
                    </label>
                    <label>
                        <input class="uk-checkbox" type="checkbox">
                        <span>Экспедиторы</span>
                    </label>
                </div>
            </div>
            <div class="filter_list-top-error-transportation">
                <span>Отображать проблемные перевозки</span>
                <select class="uk-select">
                    <option>Все</option>
                    <option>Вредные</option>
                </select>
            </div>
        </div>
        <div class="filter_file-download">
            <span uk-icon="icon: download"></span>
            <span>Скачать документы</span>
        </div>
    </div>

    <?php
    $APPLICATION->IncludeComponent(
        'bitrix:main.ui.grid',
        '',
        [
            'GRID_ID' => $arResult['GRID_ID'],
            'COLUMNS' => $arResult['COLUMNS'],
            'ROWS' => $arResult['ROWS'],
            "NAV_OBJECT" => $arResult["NAV"],
            "SHOW_ROW_ACTIONS_MENU" => false,
            "SHOW_ROW_CHECKBOXES" => true,
            "SHOW_CHECK_ALL_CHECKBOXES" => true,
            "SHOW_GRID_SETTINGS_MENU" => false,
            "SHOW_NAVIGATION_PANEL" => true,
            "SHOW_PAGINATION" => true,
            "SHOW_SELECTED_COUNTER" => false,
            "SHOW_TOTAL_COUNTER" => false,
            "SHOW_PAGESIZE" => false,
            "SHOW_ACTION_PANEL" => false,
            "ALLOW_COLUMNS_SORT" => false,
            "ALLOW_COLUMNS_RESIZE" => false,
            "ALLOW_HORIZONTAL_SCROLL" => false,
            "ALLOW_SORT" => false,
            "ALLOW_PIN_HEADER" => false,
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => false,
            'AJAX_MODE' => 'N',
        ]
    );
    ?>

    <div id="info-bar" uk-offcanvas="mode: slide; flip: true; overlay: true">
        <div class="canvas-bar_content uk-offcanvas-bar">

            <div class="bar-content_title">
                <div class="bar-title">
                    Перевозка <span>№98-B21</span>
                </div>
                <div class="bar-status">
                    Проверка не пройдена
                </div>
            </div>

            <div class="bar-content_date">
                Дата погрузки <span>24.01.2023</span>
            </div>

            <div class="bar-content_deviation_price">
                Отклонение от рыночной цены
                <span class="deviation-price_percent">
                    <span uk-icon="icon: arrow-down"></span> 3,37%
                </span>
            </div>

            <div class="bar-content_members">
                Грузовладелец
                <span>ИП Соломин Петр Эдуардович</span>
                456789012345
            </div>

            <div class="bar-content_members">
                Перевозчик
                <span>ООО "СкладСервис"</span>
                456789012345
            </div>

            <div class="bar-content_link_archiv">
                <span uk-icon="icon: download"></span>
                Скачать архив документов
            </div>

            <div class="bar-detail">
                <div class="bar-detail_title">
                    Детали чек-листа
                </div>

                <ul class="subnav-title uk-subnav uk-subnav-pill" uk-switcher>
                    <li><a href="#">С перевозчиком</a></li>
                    <li><a href="#">С заказчиком</a></li>
                </ul>

                <ul class="subnav-content uk-switcher uk-margin">
                    <li>
                        <div class="detail-content">
                            <div class="detail-content_confirmation">
                                <div class="detail-content_title">
                                    Подписанные договоры <span class="detail-status_good">1/1</span>
                                </div>

                                <div class="status-info_confirmation">
                                    <span class="status-info_confirmation_title">Договор перевозки</span>
                                    <a href="" class="status-link_file">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                            </div>

                            <div class="detail-content_confirmation">
                                <div class="detail-content_title">
                                    Оформление перевозки <span class="detail-status_error">Выполнено 4/5</span>
                                </div>

                                <div class="status-info_confirmation">
                                    <span class="status-info_confirmation_title">Заявка на перевозку</span>
                                    <a href="" class="status-link_file">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                                <div class="status-info_confirmation">
                                    <span class="status-info_confirmation_title">Подписанная ЭТрН</span>
                                    <a href="" class="status-link_file">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                                <div class="status-info_confirmation">
                                    <span class="status-info_confirmation_title">Стоимость перевозки соответствует рыночным ценам</span>
                                    <a href="" class="status-link_file">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                                <div class="status-info_confirmation status-info_confirmation_error">
                                    <span class="status-info_confirmation_title">Подтверждения перевозки через геомониторинг</span>
                                </div>
                                <div class="status-info_confirmation">
                                    <span class="status-info_confirmation_title">Подтверждения договорных отношений с водителем</span>
                                    <a href="" class="status-link_file">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                            </div>

                            <div class="detail-content_confirmation">
                                <div class="detail-content_title">
                                    Подтверждения владения ТС <span class="detail-content_auto">Р601ТТ58</span>
                                    <span class="detail-status_good">2/2</span>
                                </div>

                                <div class="status-info_confirmation">
                                    <span class="status-info_confirmation_title">Договор аренды</span>
                                    <a href="" class="status-link_file">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                                <div class="status-info_confirmation">
                                    <span class="status-info_confirmation_title">СТС</span>
                                    <a href="" class="status-link_file">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>

                    </li>
                </ul>
            </div>
        </div>
    </div>

<!--    <table class="uk-table">-->
<!--        <thead class="vitrina_table-title">-->
<!--            <tr>-->
<!--                <th>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </th>-->
<!--                <th>№ перевозки</th>-->
<!--                <th>Дата погрузки</th>-->
<!--                <th>Грузовладелец</th>-->
<!--                <th>Экспедитор</th>-->
<!--                <th>Перевозчик</th>-->
<!--                <th>Отклонение от рыночной цены, %</th>-->
<!--                <th>Чек-лист с перевозчиком</th>-->
<!--                <th>Чек-лист с экспедитором</th>-->
<!--            </tr>-->
<!--        </thead>-->
<!--        <tbody class="vitrina_list">-->
<!--            <tr class="vitrina_table-list">-->
<!--                <td>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </td>-->
<!--                <td><a href="">1A-5678</a></td>-->
<!--                <td>24.01.2023</td>-->
<!--                <td>ООО "АвтоСервис"<span>9011234567890</span></td>-->
<!--                <td>ООО "ЭнергоСервис"<span>456789012345</span></td>-->
<!--                <td>ООО "СтройТехИнвест"<span>90123456789</span></td>-->
<!--                <td><span uk-icon="icon: arrow-up"></span>2,41</td>-->
<!--                <td><span class="transit-good"></span></td>-->
<!--                <td><span class="transit-good"></span></td>-->
<!--            </tr>-->
<!--            <tr class="vitrina_table-list">-->
<!--                <td>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </td>-->
<!--                <td><a href="">98-В21</a></td>-->
<!--                <td>26.12.2022</td>-->
<!--                <td>ИП Соломин Петр Эдуардо...<span></span></td>-->
<!--                <td><span></span></td>-->
<!--                <td>ООО "СкладСервис"<span>678901234567</span></td>-->
<!--                <td><div><span uk-icon="icon: arrow-down"></span>3,37</div></td>-->
<!--                <td><span class="transit-wait"></span></td>-->
<!--                <td></td>-->
<!--            </tr>-->
<!--            <tr class="vitrina_table-list">-->
<!--                <td>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </td>-->
<!--                <td><a href="">С5790</a></td>-->
<!--                <td>13.09.2022</td>-->
<!--                <td>ООО "СтройМастер"<span>789012345678</span></td>-->
<!--                <td><span></span></td>-->
<!--                <td>ИП Волкова Елена Антонов...<span></span></td>-->
<!--                <td><span uk-icon="icon: arrow-up">5,58</td>-->
<!--                <td><span class="transit-good"></span></td>-->
<!--                <td></td>-->
<!--            </tr>-->
<!--            <tr class="vitrina_table-list">-->
<!--                <td>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </td>-->
<!--                <td><a href="">24-D613</a></td>-->
<!--                <td>04.04.2023</td>-->
<!--                <td>ООО "МедиаГруппа"<span>678901234567</span></td>-->
<!--                <td><span></span></td>-->
<!--                <td>ООО "МедиаПродакшн"<span>123456789012</span></td>-->
<!--                <td><span uk-icon="icon: arrow-down">18,12</td>-->
<!--                <td><span class="transit-error"></span></td>-->
<!--                <td></td>-->
<!--            </tr>-->
<!--            <tr class="vitrina_table-list">-->
<!--                <td>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </td>-->
<!--                <td><a href="">98-72FO</a></td>-->
<!--                <td>21.07.2022</td>-->
<!--                <td>ИП Шишкин Сергей Петров...<span></span></td>-->
<!--                <td><span></span></td>-->
<!--                <td>ООО "Бизнес-Системы"<span>890123456789</span></td>-->
<!--                <td><span uk-icon="icon: arrow-down">11,33</td>-->
<!--                <td><span class="transit-error"></span></td>-->
<!--                <td></td>-->
<!--            </tr>-->
<!--            <tr class="vitrina_table-list">-->
<!--                <td>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </td>-->
<!--                <td><a href="">G1234H</a></td>-->
<!--                <td>15.02.2023</td>-->
<!--                <td>ООО "ТехноПродукт"<span>345678901234</span></td>-->
<!--                <td>ИП Назарова Ольга Дмитр...<span>789012345678</span></td>-->
<!--                <td>ИП Сергеева Марина Игоре...<span>8901234567890</span></td>-->
<!--                <td><span uk-icon="icon: arrow-up">44,03</td>-->
<!--                <td><span class="transit-good"></span></td>-->
<!--                <td><span class="transit-good"></span></td>-->
<!--            </tr>-->
<!--            <tr class="vitrina_table-list">-->
<!--                <td>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </td>-->
<!--                <td><a href="">98-760</a></td>-->
<!--                <td>27.11.2022</td>-->
<!--                <td>ООО "АгроСервис"<span>234567890123</span></td>-->
<!--                <td><span></span></td>-->
<!--                <td>ООО "ПромТех"<span></span></td>-->
<!--                <td><span uk-icon="icon: arrow-up">8,16</td>-->
<!--                <td><span class="transit-error"></span></td>-->
<!--                <td></td>-->
<!--            </tr>-->
<!--            <tr class="vitrina_table-list">-->
<!--                <td>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </td>-->
<!--                <td><a href="">2J579</a></td>-->
<!--                <td>18.10.2022</td>-->
<!--                <td>ИП Смирнова Светлана Ал...<span>123456789012</span></td>-->
<!--                <td>ООО "СпортМагазин"<span>234567890123</span></td>-->
<!--                <td>ООО "Горизонт"<span>456789012345</span></td>-->
<!--                <td><span uk-icon="icon: arrow-down">1,55</td>-->
<!--                <td><span class="transit-error"></span></td>-->
<!--                <td></td>-->
<!--            </tr>-->
<!--            <tr class="vitrina_table-list">-->
<!--                <td>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </td>-->
<!--                <td><a href="">К4-680</a></td>-->
<!--                <td>02.05.2022</td>-->
<!--                <td>ООО "БизнесПартнер"<span>12345678901</span></td>-->
<!--                <td>ООО "АртМебель"<span>901234567890</span></td>-->
<!--                <td>ИП Жукова Наталья Макси...<span></span></td>-->
<!--                <td><span uk-icon="icon: arrow-up">2 561,88</td>-->
<!--                <td><span class="transit-good"></span></td>-->
<!--                <td><span class="transit-error"></span></td>-->
<!--            </tr>-->
<!--            <tr class="vitrina_table-list">-->
<!--                <td>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </td>-->
<!--                <td><a href="">9L-5432</a></td>-->
<!--                <td>06.03.2023</td>-->
<!--                <td>ООО "ТрансЛогистика"<span>890123456789</span></td>-->
<!--                <td><span></span></td>-->
<!--                <td>ООО ""Эко-Сервис<span>123456789012</span></td>-->
<!--                <td><span uk-icon="icon: arrow-up">8,15</td>-->
<!--                <td><span class="transit-error"></span></td>-->
<!--                <td></td>-->
<!--            </tr>-->
<!--            <tr class="vitrina_table-list">-->
<!--                <td>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </td>-->
<!--                <td><a href="">M7890</a></td>-->
<!--                <td>29.08.2023</td>-->
<!--                <td>ИП Горбунов Алексей Викт...<span>567890123456</span></td>-->
<!--                <td>ИП Козлов Дмитрий Серге...<span>678901234567</span></td>-->
<!--                <td>ООО "Авто-Маркет"<span>901234567890</span></td>-->
<!--                <td><span uk-icon="icon: arrow-down">21,93</td>-->
<!--                <td><span class="transit-good"></span></td>-->
<!--                <td><span class="transit-good"></span></td>-->
<!--            </tr>-->
<!--            <tr class="vitrina_table-list">-->
<!--                <td>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </td>-->
<!--                <td><a href="">1N-5790</a></td>-->
<!--                <td>08.01.2023</td>-->
<!--                <td>ООО "ЭнергоСервис"<span>345678901234</span></td>-->
<!--                <td><span></span></td>-->
<!--                <td>ИП Морозов Андрей Андре...<span>890123456789</span></td>-->
<!--                <td><span uk-icon="icon: arrow-down">4,67</td>-->
<!--                <td><span class="transit-good"></span></td>-->
<!--                <td></td>-->
<!--            </tr>-->
<!--            <tr class="vitrina_table-list">-->
<!--                <td>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </td>-->
<!--                <td><a href="">98-7601O</a></td>-->
<!--                <td>23.06.2022</td>-->
<!--                <td>ИП Назарова Ольга Дмитр...<span></span></td>-->
<!--                <td>ООО "АгроПродукт"<span></span></td>-->
<!--                <td>ООО "ТехноСтрой"<span></span></td>-->
<!--                <td><span uk-icon="icon: arrow-up">1,58</td>-->
<!--                <td><span class="transit-good"></span></td>-->
<!--                <td><span class="transit-good"></span></td>-->
<!--            </tr>-->
<!--        </tbody>-->
<!--    </table>-->

<!--    <div class="content_vitrina">-->
<!--        <div class="vitrina_table">-->
<!--            <div class="vitrina_table-title">-->
<!--                <div>-->
<!--                    <label>-->
<!--                        <input class="uk-checkbox" type="checkbox">-->
<!--                    </label>-->
<!--                </div>-->
<!--                <div>№ перевозки</div>-->
<!--                <div>Дата погрузки</div>-->
<!--                <div>Грузовладелец</div>-->
<!--                <div>Экспедитор</div>-->
<!--                <div>Перевозчик</div>-->
<!--                <div>Отклонение от рыночной цены, %</div>-->
<!--                <div>Чек-лист с перевозчиком</div>-->
<!--                <div>Чек-лист с экспедитором</div>-->
<!--            </div>-->
<!--            <div class="vitrina_list">-->
<!--                <div class="vitrina_table-list">-->
<!--                    <div>-->
<!--                        <label>-->
<!--                            <input class="uk-checkbox" type="checkbox">-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <div id="offcanvas-push" uk-offcanvas="mode: push">...</div>-->
<!--                    <div>24.01.2023</div>-->
<!--                    <div>ООО "АвтоСервис"<span>9011234567890</span></div>-->
<!--                    <div>ООО "ЭнергоСервис"<span>456789012345</span></div>-->
<!--                    <div>ООО "СтройТехИнвест"<span>90123456789</span></div>-->
<!--                    <div><span uk-icon="icon: arrow-up"></span>2,41</div>-->
<!--                    <div><span class="transit-good"></span></div>-->
<!--                    <div><span class="transit-good"></span></div>-->
<!--                </div>-->
<!--                <div class="vitrina_table-list">-->
<!--                    <div>-->
<!--                        <label>-->
<!--                            <input class="uk-checkbox" type="checkbox">-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <div><a href="">98-В21</a></div>-->
<!--                    <div>26.12.2022</div>-->
<!--                    <div>ИП Соломин Петр Эдуардо...<span></span></div>-->
<!--                    <div><span></span></div>-->
<!--                    <div>ООО "СкладСервис"<span>678901234567</span></div>-->
<!--                    <div><div><span uk-icon="icon: arrow-down"></span>3,37</div></div>-->
<!--                    <div><span class="transit-wait"></span></div>-->
<!--                    <div></div>-->
<!--                </div>-->
<!--                <div class="vitrina_table-list">-->
<!--                    <div>-->
<!--                        <label>-->
<!--                            <input class="uk-checkbox" type="checkbox">-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <div><a href="">С5790</a></div>-->
<!--                    <div>13.09.2022</div>-->
<!--                    <div>ООО "СтройМастер"<span>789012345678</span></div>-->
<!--                    <div><span></span></div>-->
<!--                    <div>ИП Волкова Елена Антонов...<span></span></div>-->
<!--                    <div><span uk-icon="icon: arrow-up">5,58</div>-->
<!--                    <div><span class="transit-good"></span></div>-->
<!--                    <div></div>-->
<!--                </div>-->
<!--                <div class="vitrina_table-list">-->
<!--                    <div>-->
<!--                        <label>-->
<!--                            <input class="uk-checkbox" type="checkbox">-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <div><a href="">24-D613</a></div>-->
<!--                    <div>04.04.2023</div>-->
<!--                    <div>ООО "МедиаГруппа"<span>678901234567</span></div>-->
<!--                    <div><span></span></div>-->
<!--                    <div>ООО "МедиаПродакшн"<span>123456789012</span></div>-->
<!--                    <div><span uk-icon="icon: arrow-down">18,12</div>-->
<!--                    <div><span class="transit-error"></span></div>-->
<!--                    <div></div>-->
<!--                </div>-->
<!--                <div class="vitrina_table-list">-->
<!--                    <div>-->
<!--                        <label>-->
<!--                            <input class="uk-checkbox" type="checkbox">-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <div><a href="">98-72FO</a></div>-->
<!--                    <div>21.07.2022</div>-->
<!--                    <div>ИП Шишкин Сергей Петров...<span></span></div>-->
<!--                    <div><span></span></div>-->
<!--                    <div>ООО "Бизнес-Системы"<span>890123456789</span></div>-->
<!--                    <div><span uk-icon="icon: arrow-down">11,33</div>-->
<!--                    <div><span class="transit-error"></span></div>-->
<!--                    <div></div>-->
<!--                </div>-->
<!--                <div class="vitrina_table-list">-->
<!--                    <div>-->
<!--                        <label>-->
<!--                            <input class="uk-checkbox" type="checkbox">-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <div><a href="">G1234H</a></div>-->
<!--                    <div>15.02.2023</div>-->
<!--                    <div>ООО "ТехноПродукт"<span>345678901234</span></div>-->
<!--                    <div>ИП Назарова Ольга Дмитр...<span>789012345678</span></div>-->
<!--                    <div>ИП Сергеева Марина Игоре...<span>8901234567890</span></div>-->
<!--                    <div><span uk-icon="icon: arrow-up">44,03</div>-->
<!--                    <div><span class="transit-good"></span></div>-->
<!--                    <div><span class="transit-good"></span></div>-->
<!--                </div>-->
<!--                <div class="vitrina_table-list">-->
<!--                    <div>-->
<!--                        <label>-->
<!--                            <input class="uk-checkbox" type="checkbox">-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <div><a href="">98-760</a></div>-->
<!--                    <div>27.11.2022</div>-->
<!--                    <div>ООО "АгроСервис"<span>234567890123</span></div>-->
<!--                    <div><span></span></div>-->
<!--                    <div>ООО "ПромТех"<span></span></div>-->
<!--                    <div><span uk-icon="icon: arrow-up">8,16</div>-->
<!--                    <div><span class="transit-error"></span></div>-->
<!--                    <div></div>-->
<!--                </div>-->
<!--                <div class="vitrina_table-list">-->
<!--                    <div>-->
<!--                        <label>-->
<!--                            <input class="uk-checkbox" type="checkbox">-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <div><a href="">2J579</a></div>-->
<!--                    <div>18.10.2022</div>-->
<!--                    <div>ИП Смирнова Светлана Ал...<span>123456789012</span></div>-->
<!--                    <div>ООО "СпортМагазин"<span>234567890123</span></div>-->
<!--                    <div>ООО "Горизонт"<span>456789012345</span></div>-->
<!--                    <div><span uk-icon="icon: arrow-down">1,55</div>-->
<!--                    <div><span class="transit-error"></span></div>-->
<!--                    <div></div>-->
<!--                </div>-->
<!--                <div class="vitrina_table-list">-->
<!--                    <div>-->
<!--                        <label>-->
<!--                            <input class="uk-checkbox" type="checkbox">-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <div><a href="">К4-680</a></div>-->
<!--                    <div>02.05.2022</div>-->
<!--                    <div>ООО "БизнесПартнер"<span>12345678901</span></div>-->
<!--                    <div>ООО "АртМебель"<span>901234567890</span></div>-->
<!--                    <div>ИП Жукова Наталья Макси...<span></span></div>-->
<!--                    <div><span uk-icon="icon: arrow-up">2 561,88</div>-->
<!--                    <div><span class="transit-good"></span></div>-->
<!--                    <div><span class="transit-error"></span></div>-->
<!--                </div>-->
<!--                <div class="vitrina_table-list">-->
<!--                    <div>-->
<!--                        <label>-->
<!--                            <input class="uk-checkbox" type="checkbox">-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <div><a href="">9L-5432</a></div>-->
<!--                    <div>06.03.2023</div>-->
<!--                    <div>ООО "ТрансЛогистика"<span>890123456789</span></div>-->
<!--                    <div><span></span></div>-->
<!--                    <div>ООО ""Эко-Сервис<span>123456789012</span></div>-->
<!--                    <div><span uk-icon="icon: arrow-up">8,15</div>-->
<!--                    <div><span class="transit-error"></span></div>-->
<!--                    <div></div>-->
<!--                </div>-->
<!--                <div class="vitrina_table-list">-->
<!--                    <div>-->
<!--                        <label>-->
<!--                            <input class="uk-checkbox" type="checkbox">-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <div><a href="">M7890</a></div>-->
<!--                    <div>29.08.2023</div>-->
<!--                    <div>ИП Горбунов Алексей Викт...<span>567890123456</span></div>-->
<!--                    <div>ИП Козлов Дмитрий Серге...<span>678901234567</span></div>-->
<!--                    <div>ООО "Авто-Маркет"<span>901234567890</span></div>-->
<!--                    <div><span uk-icon="icon: arrow-down">21,93</div>-->
<!--                    <div><span class="transit-good"></span></div>-->
<!--                    <div><span class="transit-good"></span></div>-->
<!--                </div>-->
<!--                <div class="vitrina_table-list">-->
<!--                    <div>-->
<!--                        <label>-->
<!--                            <input class="uk-checkbox" type="checkbox">-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <div><a href="">1N-5790</a></div>-->
<!--                    <div>08.01.2023</div>-->
<!--                    <div>ООО "ЭнергоСервис"<span>345678901234</span></div>-->
<!--                    <div><span></span></div>-->
<!--                    <div>ИП Морозов Андрей Андре...<span>890123456789</span></div>-->
<!--                    <div><span uk-icon="icon: arrow-down">4,67</div>-->
<!--                    <div><span class="transit-good"></span></div>-->
<!--                    <div></div>-->
<!--                </div>-->
<!--                <div class="vitrina_table-list">-->
<!--                    <div>-->
<!--                        <label>-->
<!--                            <input class="uk-checkbox" type="checkbox">-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <div><a href="">98-7601O</a></div>-->
<!--                    <div>23.06.2022</div>-->
<!--                    <div>ИП Назарова Ольга Дмитр...<span></span></div>-->
<!--                    <div>ООО "АгроПродукт"<span></span></div>-->
<!--                    <div>ООО "ТехноСтрой"<span></span></div>-->
<!--                    <div><span uk-icon="icon: arrow-up">1,58</div>-->
<!--                    <div><span class="transit-good"></span></div>-->
<!--                    <div><span class="transit-good"></span></div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->

</div>