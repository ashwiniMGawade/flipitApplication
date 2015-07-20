<?php
namespace Api\Controller;

use \Nocarrier\Hal;
use \Api\Controller\ApiBaseController;
use \Core\Domain\Factory\AdminFactory;

class ShopsController extends ApiBaseController
{
    public function getShop($id)
    {
        $shop = AdminFactory::getShop()->execute($id);
        echo $this->generateShopJsonData($shop);
        exit;
    }

    public function createShop()
    {
        $shop = AdminFactory::createShop()->execute();
        $params = json_decode($this->app->request->getBody(), true);
        $result = AdminFactory::addShop()->execute($shop, $params);
        if (is_array($result) && !empty($result)) {
            $this->app->response->setStatus(405);
            echo json_encode($result);
            return;
        }
        echo $this->generateShopJsonData($result);
        exit;
    }

    public function updateShop($id)
    {
        $shop = AdminFactory::getShop()->execute($id);
        $params = json_decode($this->app->request->getBody(), true);
        $result = AdminFactory::updateShop()->execute($shop, $params);
        if (is_array($result) && !empty($result)) {
            $this->app->response->setStatus(405);
            echo json_encode($result);
            return;
        }
        echo $this->generateShopJsonData($result);
        exit;
    }

    public function deleteShop($id)
    {
        $shop = AdminFactory::deleteShop()->execute($id);
        echo json_encode(array('msg'=>'Shop deleted successfully.'));
        exit;
    }

    private function generateShopJsonData($shop)
    {
        $affliateNetwork = $shop->getAffliatenetwork();

        $shopData = array(
            'name'                  => $shop->getName(),
            'permaLink'             => $shop->getPermaLink(),
            'overriteTitle'         => $shop->getOverriteTitle(),
            'metaDescription'       => $shop->getMetaDescription(),
            'usergenratedcontent'   => (int) $shop->getUsergenratedcontent(),
            'discussions'           => $shop->getDiscussions(),
            'title'                 => $shop->getTitle(),
            'subTitle'              => $shop->getSubTitle(),
            'notes'                 => $shop->getNotes(),
            'accountManagerName'    => $shop->getAccountManagerName(),
            'affliateNetwork'       => is_object($affliateNetwork)?$affliateNetwork->getName():'',
            'deepLinkStatus'        => (int) $shop->getDeepLinkStatus(),
            'refUrl'                => $shop->getRefUrl(),
            'actualUrl'             => $shop->getActualUrl(),
            'shopText'              => $shop->getShopText(),
        );
        $shop = new Hal('/shops/'.$shop->getId(), $shopData);
        return $shop->asJson();
    }
}
