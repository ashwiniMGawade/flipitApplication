<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\Shop;
use \Core\Domain\Repository\ShopRepositoryInterface;
use \Core\Domain\Validator\ShopValidator;

class AddShopUsecase
{
    protected $shopRepository;

    protected $shopValidator;

    public function __construct(ShopRepositoryInterface $shopRepository, ShopValidator $shopValidator)
    {
        $this->shopRepository   = $shopRepository;
        $this->shopValidator    = $shopValidator;
    }

    public function execute(Shop $shop, $params = array())
    {
        $affliateNetwork = '';

        $shop->setCreatedAt(new \DateTime('now'));
        $shop->setDeleted(0);
        $shop->setUpdatedAt(new \DateTime('now'));
        $shop->setAddtosearch(0);
        if (isset($params['name'])) {
            $shop->setName($params['name']);
        }
        if (isset($params['navigationUrl'])) {
            $shop->setPermaLink($params['navigationUrl']);
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
            $affliateNetwork = $params['affliateNetwork'];
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

        if (is_object($affliateNetwork) && !$affliateNetwork->getId()) {
            throw new \Exception('Invalid affiliate network');
        }
        $shop->setAffliatenetwork($affliateNetwork);

        $validationResult = $this->shopValidator->validate($shop);
        if ($validationResult !== true) {
            return $validationResult;
        }

        return $this->shopRepository->persist($shop);
    }
}
