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

use Bitrix\Main\Context;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$request = Context::getCurrent()->getRequest();

if ($request->get('year')) {
    $classFilterYear = 'focused';
}
?>
<div class="uk-container uk-container-large">

    <div class="form_filter">
        <form class="form_filter-date">
            <div class="form_cvartal">
                <?php foreach ($arResult['FILTER_YEAR'] as $kvartal) {
                    $classFilterKvartal = '';

                    if ($request->get('kvartal') === $kvartal['VALUE']) {
                        $classFilterKvartal = 'focused';
                    }
                ?>
                    <div class="filter_date-cvartal">
                        <div class="filter_cvartal-main">
                            <label class="filter_checkbox <?=$classFilterKvartal ?>">
                                <input name="kvartal" value="<?= $kvartal['VALUE'] ?>" type="submit">
                                <span><?= $kvartal['NAME'] ?></span>
                            </label>
                        </div>

                        <div class="filter-month">
                            <?php foreach ($kvartal['MONTH'] as $month) {
                                $classFilter = '';

                                if ($request->get('month') === $month['VALUE']) {
                                    $classFilter = 'focused';
                                }
                            ?>
                                <div class="filter_cvartal-month">
                                    <label class="filter_checkbox <?=$classFilter ?>">
                                        <input class="uk-checkbox" name="month" value="<?= $month['VALUE'] ?>" type="submit">
                                        <span><?= $month['NAME'] ?></span>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="form_date">
                <div class="form_date-year">
                    <label class ="filter_checkbox <?= $classFilterYear ?>">
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

    <form class="statistics">
        <div class="statistics_result">
            <div class="statistics-info">
                <label class="statistics_checkbox">
                    <input name="statistics" value="total" type="submit">
                    <div class="statistics-title">
                        <?= $arResult["COUNT"] ?>
                    </div>
                    выполнено всего перевозок
                </label>
            </div>
        </div>
        <div class="statistics_good">
            <div class="statistics-info">
                <label class="statistics_checkbox">
                    <input name="statistics" value="good" type="submit">
                    <div class="statistics-title">
                        <?= $arResult["COUNT_GOOD"] ?>
                        <sup><span><?= $arResult['COUNT_GOOD_PERCENT'] ?>%</span></sup>
                    </div>
                    проверка по чек-листу пройдена
                </label>
            </div>
        </div>
        <div class="statistics_error">
            <div class="statistics-info">
                <label class="statistics_checkbox">
                    <input name="statistics" value="error" type="submit">
                    <div class="statistics-title">
                        <?= $arResult["COUNT_ERROR"] ?>
                        <sup><span><?= $arResult['COUNT_ERROR_PERCENT'] ?>%</span></sup>
                    </div>
                    проверка по чек-листу не пройдена
                </label>
            </div>
            <div class="statistics_error-info">
                <div>
                    <label class="statistics_checkbox">
                        <input name="statistics" value="doc" type="submit">
                        <span><?= $arResult["COUNT_ERROR_DOC"] ?></span> с недостающими документами
                    </label>
                </div>
                <div>
                    <label class="statistics_checkbox">
                        <input name="statistics" value="geo" type="submit">
                        <span><?= $arResult["COUNT_ERROR_GEO"] ?></span> нет подтверждения через геомониторинг
                    </label>
                </div>
                <div>
                    <label class="statistics_checkbox">
                        <input name="statistics" value="price" type="submit">
                        <span><?= $arResult["COUNT_ERROR_PRICE"] ?></span> цена не соответствует рыночной
                    </label>
                </div>
            </div>
            <div class="statistics_error-search">
                <label>
                    <span uk-icon="icon: search"></span>
                </label>
            </div>
        </div>
    </form>

    <div class="filter_list">
        <div class="filter_list-top">
            <form action="/top" method="get" class="filter_list-top-error-organizations">
                <label class="filter_top-organizations">
                    <input class="uk-checkbox" type="checkbox">
                    <span>Топ проблемных организаций</span>
                </label>
                <div class="filter_list-type-organizations">
                    <label class="filter_list-label">
                        <input class="uk-checkbox" name="top" value="cargo" type="submit">
                        <span>Грузовладельцы</span>
                    </label>
                    <label class="filter_list-label">
                        <input class="uk-checkbox" name="top" value="carriers" type="submit">
                        <span>Перевозчики</span>
                    </label>
                    <label class="filter_list-label">
                        <input class="uk-checkbox" name="top" value="forwarders" type="submit">
                        <span>Экспедиторы</span>
                    </label>
                </div>
            </form>
            <form class="filter_list-top-error-transportation">
            <?php
                if ($request->get('statistics') === 'doc') {
                    $selectDoc = 'selected';
                }

                if ($request->get('statistics') === 'geo') {
                    $selectGeo = 'selected';
                }

                if ($request->get('statistics') === 'price') {
                    $selectPrice = 'selected';
                }
            ?>
                <span>Отображать проблемные перевозки</span>
                <select class="uk-select" name="statistics" onchange="this.form.submit()">
                    <option>Все</option>
                    <option <?= $selectDoc ?> value="doc">C недостающими документами</option>
                    <option <?= $selectGeo ?> value="geo">Нет подтверждения через геомониторинг</option>
                    <option <?= $selectPrice ?> value="price">Цена не соответствует рыночной</option>
                </select>
            </form>
        </div>
        <div class="filter_file-download" id="file_filter_download">
            <span uk-icon="icon: download"></span>
            <span>Скачать документы</span>
        </div>
    </div>

    <div class="vitrina_error"></div>
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
<!--                <div class="bar-status" id="status_shipping"></div>-->
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

                                            <div class="status-link_file" id="<?= $document['LINK_ID'] ?>" type="button">
                                                <?php if($document['FILE']) { ?> Посмотреть<?php } ?><span uk-icon="icon: check;"></span>
                                            </div>
                                            <div uk-dropdown="mode: click" class="list_file_link" id="list_file_<?= $document['ID'] ?>"></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                        </div>
                    </li>
                    <li id="checklist_forwarder">
                        <div class="detail-content">

                            <?php foreach ($arResult['INFO_BAR_DOC_FOR'] as $docGroup) { ?>
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

                                            <div class="status-link_file" id="<?= $document['LINK_ID'] ?>" type="button">
                                                <?php if($document['FILE']) { ?> Посмотреть<?php } ?><span uk-icon="icon: check;"></span>
                                            </div>
                                            <div uk-dropdown="mode: click" class="list_file_link" id="list_file_<?= $document['ID'] ?>"></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>