<?php

require '../vendor/slim/slim/Slim/Slim.php';

\Slim\Slim::registerAutoloader();



/*use Core\Domain\Entity;
$offerEntity = new Offer();
print_r($offerEntity); die;*/



$app = new \Slim\Slim();

$app->contentType("application/json");

$app->get(
    '/',
    function () {
        echo json_encode(array("msg"=>"Welcome to Slim Framework"));
    }
);

$app->run();
