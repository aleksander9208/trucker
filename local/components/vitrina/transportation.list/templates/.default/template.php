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

    <div class="form_filter">
        <form class="form_filter-date">
            <div class="form_cvartal">
                <div class="filter_date-cvartal">
                    <div class="filter_cvartal-main">
                        <label class="filter_checkbox">
                            <input name="kvartal" value="1" type="submit">
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
                            <input name="kvartal" value="2" type="submit">
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
                            <input name="kvartal" value="3" type="submit">
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
                            <input name="kvartal" value="4" type="submit">
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
                        <input name="year" value="<?= $arResult['YEAR'] ?>" type="submit">
                        <span>Весь год</span>
                    </label>
                </div>
<!--                <div class="form_date-period">-->
<!--                    <label>-->
<!--                        <input class="uk-date" type="date">-->
<!--                    </label>-->
<!--                </div>-->
            </div>
        </form>

        <?php
        $APPLICATION->IncludeComponent(
            'bitrix:main.ui.filter',
            '',
        [
            'FILTER_ID' => $arResult["GRID_CODE"],
            'GRID_ID' => $arResult["GRID_CODE"],
            'FILTER' => [],
            'ENABLE_LIVE_SEARCH' => false,
            'ENABLE_LABEL' => false,
            'VALUE_REQUIRED_MODE' => false
        ]);
        ?>
    </div>

    <div class="statistics">
        <div class="statistics_result">
            <div class="statistics-info">
                <div class="statistics-title">
                    <?= number_format($arResult["COUNT"], 0, '', ' ') ?>
                </div>
                выполнено всего перевозок
            </div>
        </div>
        <div class="statistics_good">
            <div class="statistics-info">
                <div class="statistics-title">
                    <?= number_format($arResult["COUNT_GOOD"], 0, '', ' ') ?>
                    <sup><span><?= $arResult['COUNT_GOOD_PERCENT'] ?>%</span></sup>
                </div>
                проверка по чек-листу пройдена
            </div>
        </div>
        <div class="statistics_error">
            <div class="statistics-info">
                <div class="statistics-title">
                    <?= number_format($arResult["COUNT_ERROR"], 0, '', ' ') ?>
                    <sup><span><?= $arResult['COUNT_ERROR_PERCENT'] ?>%</span></sup>
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
                    <label class="filter_list-label">
                        <input class="uk-checkbox" type="checkbox">
                        <span>Грузовладельцы</span>
                    </label>
                    <label class="filter_list-label">
                        <input class="uk-checkbox" type="checkbox">
                        <span>Перевозчики</span>
                    </label>
                    <label class="filter_list-label">
                        <input class="uk-checkbox" type="checkbox">
                        <span>Экспедиторы</span>
                    </label>
                </div>
            </div>
            <div class="filter_list-top-error-transportation">
                <span>Отображать проблемные перевозки</span>
                <select class="uk-select">
                    <option>Все</option>
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
            'GRID_ID' => $arResult['GRID_CODE'],
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
                    Перевозка <span id="carriage_id"></span>
                </div>
                <div class="bar-status">
                    Проверка не пройдена
                </div>
            </div>

            <div class="bar-content_date">
                Дата погрузки <span id="carriage_date"></span>
            </div>

            <div class="bar-content_deviation_price" id="deviation-price">
                Отклонение от рыночной цены
                <span class="deviation-price_percent" id="carriage_deviation-price"></span>
            </div>

            <div class="bar-content_members" id="carriage_owner"></div>

            <div class="bar-content_members" id="carriage_carrier"></div>

            <div class="bar-content_members" id="carriage_forwarder"></div>

            <span href="" class="bar-content_link_archiv" id="link_archiv" data-id="">
                <span uk-icon="icon: download"></span>
                Скачать архив документов
            </span>

            <div class="bar-detail">
                <div class="bar-detail_title">
                    Детали чек-листа
                </div>

                <ul class="subnav-title uk-subnav uk-subnav-pill" uk-switcher>
                    <li id="checklist_carrier"><a href="#">С перевозчиком</a></li>
                    <li id="checklist_forwarder"><a href="#">С заказчиком</a></li>
                </ul>

                <ul class="subnav-content uk-switcher uk-margin">
                    <li id="checklist_carrier">
                        <div class="detail-content">

                            <?php foreach ($arResult['INFO_BAR_DOC'] as $docGroup) { ?>
                                <div class="detail-content_confirmation" id="<?= $docGroup['ID'] ?>">
                                    <div class="detail-content_title">
                                        <?= $docGroup['NAME'] ?>
                                        <?php if ($docGroup['ID_PLATE']) { ?>
                                            <span class="detail-content_auto" id="<?= $docGroup['ID_PLATE'] ?>"></span>
                                        <?php } ?>
                                        <span id="<?= $docGroup['ID_CHECK'] ?>"></span>
                                    </div>

                                    <?php foreach ($docGroup['DOCUMENTS'] as $document) { ?>
                                        <div class="status-info_confirmation" id="<?= $document['ID'] ?>">
                                            <span class="status-info_confirmation_title"><?= $document['NAME'] ?></span>
                                            <a href="" target="_blank" class="status-link_file" id="<?= $document['LINK_ID'] ?>">
                                                <?php if($document['FILE']) { ?>
                                                    Посмотреть
                                                <?php } ?>
                                                <span uk-icon="icon: check;"></span>
                                            </a>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                        </div>
                    </li>
                    <li id="checklist_forwarder">
                        <div class="detail-content">
                            <div class="detail-content_confirmation">
                                <div class="detail-content_title">
                                    Подписанные договоры <span id="detail_status-transportation">1/1</span>
                                </div>

                                <div class="status-info_confirmation" id="status_transportation-link">
                                    <span class="status-info_confirmation_title">Договор перевозки</span>
                                    <a href="" class="status-link_file" id="status_transportation-file">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                            </div>

                            <div class="detail-content_confirmation">
                                <div class="detail-content_title">
                                    Оформление перевозки <span id="documents_check"></span>
                                </div>

                                <div class="status-info_confirmation" id="documents_link">
                                    <span class="status-info_confirmation_title">Заявка на перевозку</span>
                                    <a href="" class="status-link_file" id="documents_file">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                                <div class="status-info_confirmation" id="epd_link">
                                    <span class="status-info_confirmation_title">Подписанная ЭТрН</span>
                                    <a href="" class="status-link_file" id="epd_file">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                                <div class="status-info_confirmation" id="prices_link">
                                    <span class="status-info_confirmation_title">Стоимость перевозки соответствует рыночным ценам</span>
                                    <a href="" class="status-link_file" id="prices_file">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                                <div class="status-info_confirmation" id="geo_link">
                                    <span class="status-info_confirmation_title">Подтверждения перевозки через геомониторинг</span>
                                    <a href="" class="status-link_file" id="geo_file">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                                <div class="status-info_confirmation" id="driver_link">
                                    <span class="status-info_confirmation_title">Подтверждения договорных отношений с водителем</span>
                                    <a href="" class="status-link_file" id="driver_file">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                            </div>

                            <div class="detail-content_confirmation">
                                <div class="detail-content_title">
                                    Подтверждения владения ТС <span class="detail-content_auto"></span>
                                    <span id="truck_check"></span>
                                </div>

                                <div class="status-info_confirmation" id="truck_rent">
                                    <span class="status-info_confirmation_title">Договор аренды</span>
                                    <a href="" class="status-link_file" id="truck_link">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                                <div class="status-info_confirmation" id="truck_sts">
                                    <span class="status-info_confirmation_title">СТС</span>
                                    <a href="" class="status-link_file" id="sts_link">
                                        Посмотреть <span uk-icon="icon: check;"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>