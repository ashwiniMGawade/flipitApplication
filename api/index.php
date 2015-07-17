<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-type: application/json');
header('Access-Control-Max-Age: 86400');

// Sets the environment to testing for codeception
if (isset($_SERVER['HTTP_USER_AGENT']) && ($_SERVER['HTTP_USER_AGENT'] == 'Symfony2 BrowserKit' || strpos($_SERVER['HTTP_USER_AGENT'], 'PhantomJS') == true)) {
    apache_setenv('APPLICATION_ENV', 'testing');
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
