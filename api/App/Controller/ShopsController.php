<?php
namespace Api\Controller;

use \Nocarrier\Hal;
use \Api\Controller\ApiBaseController;
use \Core\Domain\Factory\AdministratorFactory;

class ShopsController extends ApiBaseController
{
    public function getShop($id)
    {
        $shop = AdministratorFactory::getShop()->execute($id);
        $shopData = array(
            'name'                  => $shop->getName(),
            'overriteTitle'         => $shop->getOverriteTitle(),
            'metaDescription'       => $shop->getMetaDescription(),
            'usergenratedcontent'   => $shop->getUsergenratedcontent(),
            'discussions'           => $shop->getDiscussions(),
            'title'                 => $shop->getTitle(),
            'subTitle'              => $shop->getSubTitle(),
            'notes'                 => $shop->getNotes(),
            'accountManagerName'    => $shop->getAccountManagerName(),
            'deepLinkStatus'        => $shop->getDeepLinkStatus(),
            'refUrl'                => $shop->getRefUrl(),
            'actualUrl'             => $shop->getActualUrl(),
            'logo'                  => $shop->getLogo(),
            'screenshotId'          => $shop->getScreenshotId(),
            'shopText'              => $shop->getShopText(),
        );
        $shop = new Hal('/shops/'.$id, $shopData);
        echo $shop->asJson();
        exit;
    }
}
