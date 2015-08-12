<?php
$req = $app->request;
$isFlipit = isFlipit($req);
$locale = determineLocale($req, $isFlipit);

define('LOCALE', $locale);

$locale = ( $locale ? '/'.$locale : '');

if(($isFlipit && $locale) || !$isFlipit) {

    $app->group(
        $locale.'/shops',
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
            echo json_encode(array("msg" => "Welcome to Slim Framework"));
        }
    );
}

function isFlipit($req)
{
    $baseUrl = $req->getUrl();
    $parsedBaseUrl = parse_url($baseUrl);
    $domain = $parsedBaseUrl['host'];
    $domainPieces = explode('.', $domain);
    return in_array('flipit',$domainPieces);
}

function determineLocale($req, $isFlipit)
{
    $resourceUrl = $req->getResourceUri();

    $resourceUrlPieces = explode('/', $resourceUrl);
    return $isFlipit ? ( isset($resourceUrlPieces[1]) ? $resourceUrlPieces[1] : '' ) : '';
}