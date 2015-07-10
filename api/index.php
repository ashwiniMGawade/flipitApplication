<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
}
include '../c3.php';

require_once '../vendor/autoload.php';

require_once dirname(__FILE__) . '/App/Config/default.php';

$app = new \RKA\Slim($config['app']);

//Use of JSON middleware
$app->add(new Api\Middleware\JSON());
//Use of ErrorHandler middleware
$app->add(new Api\Middleware\ErrorHandler());

$app->get('/shops/:id', 'Api\Controller\ShopsController:getShop');

$app->post('/shops', 'Api\Controller\ShopsController:createShop');

$app->map('/shops/:id', 'Api\Controller\ShopsController:updateShop')->via('PUT', 'PATCH');

$app->get(
    '/',
    function () {
        echo json_encode(array("msg"=>"Welcome to Slim Framework"));
    }
);

$app->run();
