<?php
namespace Api\Controller;

use Core\Domain\Entity\AffliateNetwork;
use \Nocarrier\Hal;
use \Api\Controller\ApiBaseController;
use \Core\Domain\Factory\AdministratorFactory;

class ShopsController extends ApiBaseController
{
    public function getShop($id)
    {
        $shop = AdministratorFactory::getShop()->execute($id);
        echo $this->generateShopJsonData($shop);
        exit;
    }

    public function createShop()
    {
        $shop = AdministratorFactory::createShop()->execute();print_r($_REQUEST); die;
        //$params = json_decode($this->app->request->getBody(),true);
        $params = json_decode($_POST['body'],true);
        $result = AdministratorFactory::addShop()->execute($shop, $params);
        if ( is_array($result) && !empty($result)) {
            $this->app->response->setStatus(405);
            echo json_encode($result);
            return;
        }
        echo $this->generateShopJsonData($result);
        exit;
    }

    public function updateShop($id)
    {
        $shop = AdministratorFactory::getShop()->execute($id);
        //$params = json_decode($this->app->request->getBody(),true);
        $params = json_decode($_REQUEST['body'],true);
        $result = AdministratorFactory::updateShop()->execute($shop, $params);
        if ( is_array($result) && !empty($result)) {
            $this->app->response->setStatus(405);
            echo json_encode($result);
            return;
        }
        echo $this->generateShopJsonData($result);
        exit;
    }

    public function deleteShop($id)
    {
        $shop = AdministratorFactory::deleteShop()->execute($id);
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
            'usergenratedcontent'   => $shop->getUsergenratedcontent()?'Yes':'No',
            'discussions'           => $shop->getDiscussions()?'Yes':'No',
            'title'                 => $shop->getTitle(),
            'subTitle'              => $shop->getSubTitle(),
            'notes'                 => $shop->getNotes(),
            'accountManagerName'    => $shop->getAccountManagerName(),
            'affliateNetwork'       => is_object($affliateNetwork)?$affliateNetwork->getName():'',
            'deepLinkStatus'        => $shop->getDeepLinkStatus()?'Yes':'No',
            'refUrl'                => $shop->getRefUrl(),
            'actualUrl'             => $shop->getActualUrl(),
            'shopText'              => $shop->getShopText(),
        );
        $shop = new Hal('/shops/'.$shop->getId(), $shopData);
        return $shop->asJson();
    }
}
