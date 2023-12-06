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
use Bitrix\Main\Page\Asset;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$request = Context::getCurrent()->getRequest();
$flex = '';

if ($request->get('year')) {
    $classFilterYear = 'focused';
}

if ($request->get('top')) {
    $flex = 'style="display: flex;"';
}

if ($request->get('top') === 'cargo') {
    $activeClassCargo = 'focused_type';
    $titleStatistics = 'Грузовладелец';
}

if ($request->get('top') === 'carriers') {
    $activeClassCar = 'focused_type';
    $titleStatistics = 'Перевозчик';
}

if ($request->get('top') === 'forwarders') {
    $activeClassFor = 'focused_type';
    $titleStatistics = 'Экспедитор';
}

$linkSearch = '/top/?top=' . $request->get('top');

if ($request->get('FIND')) {
    $linkSearch = '/top/?top=' . $request->get('top') . '&FIND=' . $request->get('FIND');
}

?>
<div class="uk-container uk-container-large">

    <div class="form_filter">
        <form action="/" method="get" class="form_filter-date">
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
        <form action="" method="get" class="filter-search">
            <input type="hidden" value="<?= $request->get('top') ?>" name="top">
            <input type="text" value="<?= $request->get('FIND') ?>" name="FIND" placeholder="Поиск по организации или ИНН">
        </form>
    </div>

    <form action="/" method="get" class="statistics">
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
                <div class="filter_list-type-organizations" <?= $flex ?>>
                    <label class="filter_list-label <?= $activeClassCargo ?>">
                        <input class="uk-checkbox" name="top" value="cargo" type="submit">
                        <span>Грузовладельцы</span>
                    </label>
                    <label class="filter_list-label <?= $activeClassCar ?>">
                        <input class="uk-checkbox" name="top" value="carriers" type="submit">
                        <span>Перевозчики</span>
                    </label>
                    <label class="filter_list-label <?= $activeClassFor ?>">
                        <input class="uk-checkbox" name="top" value="forwarders" type="submit">
                        <span>Экспедиторы</span>
                    </label>
                </div>
            </form>
<!--            <div class="filter_list-top-error-transportation">-->
<!--                <span>Отображать проблемные перевозки</span>-->
<!--                <select class="uk-select">-->
<!--                    <option>Все</option>-->
<!--                    <option>C недостающими документами</option>-->
<!--                    <option>Нет подтверждения через геомониторинг</option>-->
<!--                    <option>Цена не соответствует рыночной</option>-->
<!--                </select>-->
<!--            </div>-->
        </div>
        <div class="filter_file-download" id="file_filter_download_top">
            <span uk-icon="icon: download"></span>
            <span>Скачать документы</span>
        </div>
    </div>

    <div class="vitrina_error"></div>
    <div class="top_company_list">
        <div class="company_list_title">
            <div class="company_title_info">
                <?= $titleStatistics ?>
            </div>
            <div class="company_title_info">
                Перевозок с проблемами
            </div>
        </div>
        <ul uk-accordion>
            <?php foreach ($arResult["ROWS"] as $row) {
                $percent = $row['COUNT']/$row['SUM_COUNT'] * 100;
            ?>
                <li>
                    <a class="uk-accordion-title " href="#">
                        <div class="company_title_accordion">
                            <div class="company_list">
                                <?= $row['NAME'] ?>
                                <span><?= $row['INN'] ?></span>
                            </div>
                            <div class="company_statistic">
                                <div class="company_statistic_inner" style="width: <?= $row['PERCENT'] ?>%"></div>
                            </div>
                            <div class="company_shipping_count company_title_info">
                                <?= $row['COUNT'] ?>
                            </div>
                        </div>
                    </a>
                    <div class="uk-accordion-content">
                        <?php
                        $APPLICATION->IncludeComponent(
                            'bitrix:main.ui.grid',
                            '',
                            [
                                'GRID_ID' => $row['GRID_CODE'],
                                'COLUMNS' => $arResult['COLUMNS'],
                                'ROWS' => $row['SHIPPING'],
                                "NAV_OBJECT" => $row["NAV"],
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
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>

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
                                                <?php if($document['FILE']) { ?> Посмотреть <span uk-icon="icon: check;"></span><?php } ?>
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
                                                <?php if($document['FILE']) { ?> Посмотреть <span uk-icon="icon: check;"></span><?php } ?>
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

<script>
    $(document).ready(function () {
        $('.filter_top-organizations input').attr('checked',true);
    });
</script>