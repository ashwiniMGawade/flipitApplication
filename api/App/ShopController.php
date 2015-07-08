<?php
namespace ApiApp;

use \ApiApp\ApiBaseController;

class ShopController extends ApiBaseController
{
    public function getShop($id)
    {
        $this->app->contentType("application/json");
        
        $shop = \Core\Domain\Factory\AdministratorFactory::getShop()->execute($id);
        if (false === is_object($shop)) {
            echo json_encode(array("msg"=>"Shop not found"));
            $this->app->response->setStatus(404);
        } else {
            $shopData = array(
                'name'                  => $shop->__get('name'),
                'overriteTitle'         => $shop->__get('overriteTitle'),
                'metaDescription'       => $shop->__get('metaDescription'),
                'usergenratedcontent'   => $shop->__get('usergenratedcontent'),
                'discussions'           => $shop->__get('discussions'),
                'title'                 => $shop->__get('title'),
                'subTitle'              => $shop->__get('subTitle'),
                'notes'                 => $shop->__get('notes'),
                'accountManagerName'    => $shop->__get('accountManagerName'),
                'deepLinkStatus'        => $shop->__get('deepLinkStatus'),
                'refUrl'                => $shop->__get('refUrl'),
                'actualUrl'             => $shop->__get('actualUrl'),
                'logo'                  => $shop->__get('logo'),
                'screenshotId'          => $shop->__get('screenshotId'),
                'shopText'              => $shop->__get('shopText'),
            );
            $this->app->response->setStatus(200);
            echo json_encode($shopData);
        }
    }
}
