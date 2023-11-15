<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Центр мониторинга перевозок");

$APPLICATION->IncludeComponent(
    "vitrina:transportation.list",
    "",
    []
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");