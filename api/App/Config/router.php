<?php
$app->group(
    '/shops',
    function () use ($app) {
        $app->get('/:id', 'Api\Controller\ShopsController:getShop');
        $app->post('/', 'Api\Controller\ShopsController:createShop');
        $app->map('/:id', 'Api\Controller\ShopsController:updateShop')->via('PUT', 'PATCH');
        $app->delete('/:id', 'Api\Controller\ShopsController:deleteShop');
    }
);

$app->group(
    '/visitors',
    function () use ($app) {
        $app->map('/', 'Api\Controller\VisitorsController:updateVisitor')->via('PUT', 'PATCH');
    }
);

$app->get(
    '/',
    function () {
        echo json_encode(array("msg"=>"Welcome to Slim Framework"));
    }
);
