<?php
header('Access-Control-Allow-Origin:*');
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
 //   header('Access-Control-Max-Age: 1000');
}

include '../c3.php';

require_once '../vendor/autoload.php';
require_once 'App/Config/default.php';

$app = new \RKA\Slim($config['app']);

//Use of JSON middleware
$app->add(new Api\Middleware\JSON());
//Use of ErrorHandler middleware
$app->add(new Api\Middleware\ErrorHandler());

require_once 'App/Config/router.php';

$app->run();
