<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\Shop;
use \Core\Domain\Repository\ShopRepositoryInterface;
use \Core\Domain\Repository\AffliateNetworkRepositoryInterface;
use \Core\Domain\Validator\ShopValidator;
use \Core\Domain\Adapter\PurifierInterface;

class UpdateShopUsecase
{
    protected $shopRepository;

    protected $shopValidator;

    protected $affliateNetworkRepository;

    protected $htmlPurifier;

    public function __construct(ShopRepositoryInterface $shopRepository, ShopValidator $shopValidator, AffliateNetworkRepositoryInterface $affliateNetworkRepository, PurifierInterface $htmlPurifier)
    {
        $this->shopRepository               = $shopRepository;
        $this->shopValidator                = $shopValidator;
        $this->affliateNetworkRepository    = $affliateNetworkRepository;
        $this->htmlPurifier                 = $htmlPurifier;
    }

    public function execute(Shop $shop, $params = array())
    {
        $params = $this->htmlPurifier->purify($params);

        if (isset($params['name'])) {
            $shop->setName($params['name']);
        }
        if (isset($params['permaLink'])) {
            $shop->setPermaLink($params['permaLink']);
        }
        if (isset($params['overriteTitle'])) {
            $shop->setOverriteTitle($params['overriteTitle']);
        }
        if (isset($params['metaDescription'])) {
            $shop->setMetaDescription($params['metaDescription']);
        }
        if (isset($params['usergenratedcontent'])) {
            $shop->setUsergenratedcontent($params['usergenratedcontent']);
        }
        if (isset($params['discussions'])) {
            $shop->setDiscussions($params['discussions']);
        }
        if (isset($params['title'])) {
            $shop->setTitle($params['title']);
        }
        if (isset($params['subTitle'])) {
            $shop->setSubTitle($params['subTitle']);
        }
        if (isset($params['notes'])) {
            $shop->setNotes($params['notes']);
        }
        if (isset($params['accountManagerName'])) {
            $shop->setAccountManagerName($params['accountManagerName']);
        }
        if (isset($params['affliateNetwork'])) {
            $affliateNetwork = $this->affliateNetworkRepository->findOneBy('\Core\Domain\Entity\AffliateNetwork', array('name'=>$params['affliateNetwork']));
            if (!is_object($affliateNetwork)) {
                throw new \Exception('Invalid affliate network');
            }
            $shop->setAffliatenetwork($affliateNetwork);
        }
        if (isset($params['deepLinkStatus'])) {
            $shop->setDeepLinkStatus($params['deepLinkStatus']);
        }
        if (isset($params['refUrl'])) {
            $shop->setRefUrl($params['refUrl']);
        }
        if (isset($params['actualUrl'])) {
            $shop->setActualUrl($params['actualUrl']);
        }
        if (isset($params['shopText'])) {
            $shop->setShopText($params['shopText']);
        }
        if (isset($params['classification'])) {
            $shop->setClassification($params['classification']);
        }

        $shop->setUpdatedAt(new \DateTime('now'));

        $validationResult = $this->shopValidator->validate($shop);

        if ($validationResult !== true) {
            return $validationResult;
        }

        return $this->shopRepository->save($shop);
    }
}
