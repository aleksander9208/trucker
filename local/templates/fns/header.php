<?php

/**
 * Bitrix vars
 *
 * @global CMain $APPLICATION
 */

use Bitrix\Main\Context;
use Bitrix\Main\Page\Asset;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$request = Context::getCurrent()->getRequest();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <?php

        Asset::getInstance()->addString("<link rel='icon' href='/favicon.ico' type='image/x-icon'/>");
        Asset::getInstance()->addString("<link rel='shortcut icon' href='/favicon.ico' type='image/x-icon'/>");

        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/css/template_styles.css");
        Asset::getInstance()->addString("<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/uikit/3.17.1/css/uikit.min.css'/>");

        $APPLICATION->ShowHead();
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    </head>
<body>
<?php $APPLICATION->ShowPanel(); ?>

<div class="content">