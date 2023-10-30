<?php

use Bitrix\Main\Routing\RoutingConfigurator;
use Edidata\Epl\Controller\Carpark;
use Edidata\Epl\Controller\Employees;
use Edidata\Epl\Controller\Medical;
use Edidata\Epl\Controller\Report;
use Edidata\Epl\Controller\Carrier;

return function (RoutingConfigurator $routes) {
    $routes
        ->prefix('api')
        ->group(function (RoutingConfigurator $routes) {
            /** API v1 */
            $routes
                ->prefix('v1/vhs')
                ->group(function (RoutingConfigurator $routes) {

                });
        });
};