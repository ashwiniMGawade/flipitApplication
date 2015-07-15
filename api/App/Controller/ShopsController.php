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
        $affliateNetwork = $shop->getAffliatenetwork();

        //print_r($shop->getCategoryshops()->__get('shop')); die;
        $shopData = array(
            'name'                  => $shop->getName(),
            'overriteTitle'         => $shop->getOverriteTitle(),
            'metaDescription'       => $shop->getMetaDescription(),
            'usergenratedcontent'   => $shop->getUsergenratedcontent()?'Yes':'No',
            'discussions'           => $shop->getDiscussions()?'Yes':'No',
            'title'                 => $shop->getTitle(),
            'subTitle'              => $shop->getSubTitle(),
            'notes'                 => $shop->getNotes(),
            'accountManagerName'    => $shop->getAccountManagerName(),
            'affilliate_network'    => is_object($affliateNetwork)?$affliateNetwork->__get('name'):'',
            'deepLinkStatus'        => $shop->getDeepLinkStatus()?'Yes':'No',
            'refUrl'                => $shop->getRefUrl(),
            'actualUrl'             => $shop->getActualUrl(),
            'shopText'              => $shop->getShopText(),
        );
        $shop = new Hal('/shops/'.$id, $shopData);
        echo $shop->asJson();
        exit;
    }
}
