<?php

use Bitrix\Main\Page\Asset;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/jquery-3.7.0.min.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/uikit/js/uikit.min.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/uikit/js/uikit-icons.min.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/script.js");
?>

</body>
</html>