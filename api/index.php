<?php
use \Core\Persistence\Factory\CacheFactory;

// Sets the environment to testing for codeception
if (isset($_SERVER['HTTP_USER_AGENT']) && ($_SERVER['HTTP_USER_AGENT'] == 'Symfony2 BrowserKit' || strpos($_SERVER['HTTP_USER_AGENT'], 'PhantomJS') == true)) {
    define('APPLICATION_ENV', 'testing');
}

defined('APPLICATION_ENV') ||
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV'): 'production'));

if (APPLICATION_ENV !== 'production') {
    include '../c3.php';
}

require_once '../vendor/autoload.php';
require_once 'App/Config/default.php';

$app = new \RKA\Slim($config['app']);

$cacheService = CacheFactory::keyValueCache();

//Use of JSON middleware
$app->add(new Api\Middleware\JSON());
//Use of ErrorHandler middleware
$app->add(new Api\Middleware\ErrorHandler());

$app->add(new Api\Middleware\RateLimit('/visitors', $cacheService));

require_once 'App/Config/router.php';

$app->run();
