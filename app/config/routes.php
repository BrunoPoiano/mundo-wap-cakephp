<?php

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return static function (RouteBuilder $routes) {
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope("/api", function (RouteBuilder $api) {
        $api->scope("/visits", function (RouteBuilder $visits) {
            $visits->get("/", [
                "controller" => "Visits",
                "action" => "list",
            ]);
            $visits->get("/date", [
                "controller" => "Visits",
                "action" => "date",
            ]);
            $visits->post("/", [
                "controller" => "Visits",
                "action" => "create",
            ]);

            $visits->scope("/:id", function (RouteBuilder $visitsId) {
                $visitsId->put("/", [
                    "controller" => "Visits",
                    "action" => "edit",
                ]);
                $visitsId->patch("/", [
                    "controller" => "Visits",
                    "action" => "patch",
                ]);
                $visitsId->delete("/", [
                    "controller" => "Visits",
                    "action" => "delete",
                ]);
            });
        });
        $api->scope("/workdays", function (RouteBuilder $workdays) {
            $workdays->get("/", [
                "controller" => "Workdays",
                "action" => "list",
            ]);

            $workdays->post("/close", [
                "controller" => "Workdays",
                "action" => "closeWorkDay",
            ]);
        });
        $api->scope("/address", function (RouteBuilder $address) {
            $address->get("/", [
                "controller" => "Address",
                "action" => "list",
            ]);

            $address->scope("/:id", function (RouteBuilder $addressId) {
                $addressId->delete("/", [
                    "controller" => "Address",
                    "action" => "delete",
                ]);
            });
        });
    });
};
