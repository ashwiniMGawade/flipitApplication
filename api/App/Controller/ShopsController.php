<?php
namespace Api\Controller;

use \Nocarrier\Hal;
use \Api\Controller\ApiBaseController;
use \Core\Domain\Factory\AdminFactory;

class ShopsController extends ApiBaseController
{
    protected $shopClassification = array(
                                            1 => 'A',
                                            2 => 'A+',
                                            3 => 'AA',
                                            4 => 'AA+',
                                            5 => 'AAA'
                                        );

    public function getShop($id)
    {
        $shop = AdminFactory::getShop()->execute($id);
        echo $this->generateShopJsonData($shop);
    }

    public function createShop()
    {
        $shop = AdminFactory::createShop()->execute();
        $params = json_decode($this->app->request->getBody(), true);
        if (isset($params['classification'])) {
            $params['classification'] = array_search($params['classification'], $this->shopClassification) ? : 1;
        }
        /*$result = AdminFactory::addShop()->execute($shop, $params);
        echo $this->generateShopJsonData($result);*/
        echo json_encode(array('msg'=>'This operation is not permitted.'));
    }

    public function updateShop($id)
    {
        $shop = AdminFactory::getShop()->execute($id);
        $params = json_decode($this->app->request->getBody(), true);
        /*$result = AdminFactory::updateShop()->execute($shop, $params);
        echo $this->generateShopJsonData($result);*/
        echo json_encode(array('msg'=>'This operation is not permitted.'));
    }

    public function deleteShop($id)
    {
        if (AdminFactory::deleteShop()->execute($id)) {
            echo json_encode(array('msg'=>'Shop deleted successfully.'));
        }
    }

    private function generateShopJsonData($shop)
    {
        if (is_array($shop) && !empty($shop)) {
            $this->app->response->setStatus(405);
            return json_encode($shop);
        }

        $affliateNetwork = $shop->getAffliatenetwork();

        $shopData = array(
            'id'                    => $shop->getId(),
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
            'classification'        => $this->shopClassification[$shop->getClassification()]
        );
        $shop = new Hal('/shops/'.$shop->getId(), $shopData);
        return $shop->asJson();
    }
}
