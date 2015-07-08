<?php

include '../c3.php';

require_once '../vendor/autoload.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->contentType("application/json");

$app->group(
    '/shop',
    function () use ($app) {
        $app->get(
            '/:id',
            function ($id) use ($app) {
                $shop = \Core\Domain\Factory\AdministratorFactory::getShop()->execute($id);
                if (false === is_object($shop)) {
                    echo json_encode(array("msg"=>"Shop not found"));
                    $app->response->setStatus(404);
                } else {
                    $shopData = array(
                        'name' 					=> $shop->__get('name'),
                        'overriteTitle'			=> $shop->__get('overriteTitle'),
                        'metaDescription'		=> $shop->__get('metaDescription'),
                        'usergenratedcontent'	=> $shop->__get('usergenratedcontent'),
                        'discussions'			=> $shop->__get('discussions'),
                        'title'					=> $shop->__get('title'),
                        'subTitle'				=> $shop->__get('subTitle'),
                        'notes'					=> $shop->__get('notes'),
                        'accountManagerName'	=> $shop->__get('accountManagerName'),
                        'deepLinkStatus'		=> $shop->__get('deepLinkStatus'),
                        'refUrl'				=> $shop->__get('refUrl'),
                        'actualUrl'				=> $shop->__get('actualUrl'),
                        'logo'					=> $shop->__get('logo'),
                        'screenshotId'			=> $shop->__get('screenshotId'),
                        'shopText'				=> $shop->__get('shopText'),
                    );
                    $app->response->setStatus(200);
                    echo json_encode($shopData);
                }
            }
        );
    }
);

$app->get(
    '/',
    function () {
        echo json_encode(array("msg"=>"Welcome to Slim Framework"));
    }
);

$app->run();
