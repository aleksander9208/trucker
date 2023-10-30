<?php

use Bitrix\Main\Routing\RoutingConfigurator;
use Taxcom\Library\Controller\Vitrina;

return function (RoutingConfigurator $routes) {
    $routes
        ->prefix('api')
        ->group(function (RoutingConfigurator $routes) {
            /** API v1 */
            $routes
                ->prefix('v1/vhs')
                ->group(function (RoutingConfigurator $routes) {

                    /** Перевозчик */
                    $routes
                        ->prefix('vitrina')
                        ->where('id', '\d+')
                        ->group(function (RoutingConfigurator $routes) {
                            $routes->post('{id}', [Vitrina::class, 'getAction']);
                        });
                });
        });
};