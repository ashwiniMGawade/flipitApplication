<?php
$locale = '';
$request = $app->request;

$isFlipit = isFlipit($request);

if ($isFlipit) {
    $locale = determineLocale($request, $config);
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
            $app->get('/:id', 'Api\Controller\VisitorsController:getVisitor');
            $app->map('/:id', 'Api\Controller\VisitorsController:updateVisitor')->via('PUT', 'PATCH');
        }
    );

    $app->get(
        $localePath . '/',
        function () {
            echo json_encode(array("messages" => "Welcome to Slim Framework"));
        }
    );
}

function isFlipit($request)
{
    $baseUrl        = $request->getUrl();
    $parsedBaseUrl  = parse_url($baseUrl);
    $domain         = $parsedBaseUrl['host'];
    $domainPieces   = explode('.', $domain);

    return in_array('flipit',$domainPieces);
}

function determineLocale($request, $config)
{
    $resourceUrl        = $request->getResourceUri();
    $resourceUrlPieces  = explode('/', $resourceUrl);

    $locale = isset($resourceUrlPieces[1]) ? $resourceUrlPieces[1] : '';

    if (in_array($locale, $config['locale'])) {
        return $locale;
    }
    return false;
}
