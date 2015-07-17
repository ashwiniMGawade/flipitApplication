<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Entity\AffliateNetwork;
use \Core\Domain\Entity\Shop;
use \Core\Domain\Repository\ShopRepositoryInterface;
use \Core\Domain\Repository\AffliateNetworkRepositoryInterface;
use \Core\Domain\Validator\ShopValidator;

class AddShopUsecase
{
    protected $shopRepository;

    protected $shopValidator;

    protected $affliateNetworkRepository;

    public function __construct(ShopRepositoryInterface $shopRepository, ShopValidator $shopValidator, AffliateNetworkRepositoryInterface $affliateNetworkRepository)
    {
        $this->shopRepository               = $shopRepository;
        $this->shopValidator                = $shopValidator;
        $this->affliateNetworkRepository    = $affliateNetworkRepository;
    }

    public function execute(Shop $shop, $params = array())
    {
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
            $affliateNetwork = $this->affliateNetworkRepository->findBy('\Core\Domain\Entity\AffliateNetwork', array('name'=>$params['affliateNetwork']));
            if ((empty($affliateNetwork)) || !is_object($affliateNetwork[0])) {
                throw new \Exception('Invalid affliate network');
            }
            $shop->setAffliatenetwork($affliateNetwork[0]);
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

        $shop->setCreatedAt(new \DateTime('now'));
        $shop->setDeleted(0);
        $shop->setUpdatedAt(new \DateTime('now'));
        $shop->setAddtosearch(0);
        $shop->setScreenshotId(1);
        $shop->setHowToUse(1);
        $shop->setDisplayExtraProperties(1);
        $shop->setShowSignupOption(0);
        $shop->setAddtosearch(0);
        $shop->setShowSimliarShops(0);
        $shop->setShowChains(0);
        $shop->setStrictConfirmation(0);
        $shop->setShowcustomtext(0);

        $validationResult = $this->shopValidator->validate($shop);

        if ($validationResult !== true) {
            return $validationResult;
        }

        return $this->shopRepository->save($shop);
    }
}
