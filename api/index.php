<?php

include '../c3.php';

require_once '../vendor/autoload.php';

$app = new \RKA\Slim();

$app->get('/shop/:id', 'ApiApp\ShopController:getShop');

$app->get(
    '/',
    function () {
        echo json_encode(array("msg"=>"Welcome to Slim Framework"));
    }
);

$app->run();
