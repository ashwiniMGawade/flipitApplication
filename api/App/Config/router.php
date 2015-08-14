<?php
$locale = '';
$req = $app->request;

$isFlipit = isFlipit($req);

if ($isFlipit) {
    $locale = determineLocale($req, $config);
}
$localePath = $locale ? '/'.$locale : '';
define('LOCALE', $locale);

if (!$isFlipit || ($isFlipit && $locale)) {
    $app->group(
        $localePath . '/shops',
        function () use ($app) {
            $app->get('/:id', 'Api\Controller\ShopsController:getShop');
            $app->post('/', 'Api\Controller\ShopsController:createShop');
            $app->map('/:id', 'Api\Controller\ShopsController:updateShop')->via('PUT', 'PATCH');
            $app->delete('/:id', 'Api\Controller\ShopsController:deleteShop');
        }
    );

    $app->group(
        $localePath . '/visitors',
        function () use ($app) {
            $app->map('/', 'Api\Controller\VisitorsController:updateVisitor')->via('PUT', 'PATCH');
        }
    );

    $app->get(
        $localePath . '/',
        function () {
            echo json_encode(array("msg" => "Welcome to Slim Framework"));
        }
    );
}

function isFlipit($req)
{
    $baseUrl        = $req->getUrl();
    $parsedBaseUrl  = parse_url($baseUrl);
    $domain         = $parsedBaseUrl['host'];
    $domainPieces   = explode('.', $domain);

    return in_array('flipit',$domainPieces);
}

function determineLocale($req, $config)
{
    $resourceUrl        = $req->getResourceUri();
    $resourceUrlPieces  = explode('/', $resourceUrl);

    $locale = isset($resourceUrlPieces[1]) ? $resourceUrlPieces[1] : '';

    if (in_array($locale, $config['locale'])) {
        return $locale;
    }
    return false;
}
