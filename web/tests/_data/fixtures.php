<?php

class fixtures
{
    protected $entityManager = '';
    protected $entityManagerUser = '';
    public function __construct($entityManager, $entityManagerUser = '')
    {
        $this->entityManager = $entityManager;
        $this->entityManagerUser = $entityManager;
        if (!empty($entityManagerUser)) {
            $this->entityManagerUser = $entityManagerUser;
        }
    }

    public function execute()
    {
        //$user = new \KC\Repository\User();
        //$user->truncateTables();
        $locale = new \Core\Domain\Entity\LocaleSettings();
        $locale->locale = 'nl_NL';
        $locale->timezone = 'Europe/Amsterdam';
        $this->entityManager->persist($locale);
        $this->entityManager->flush();

        $image = new \Core\Domain\Entity\User\ProfileImage();
        $image->ext = 'jpg';
        $image->path = 'images/upload/shop/';
        $image->name = '1409026126_Jellyfish.jpg';
        $image->deleted = 0;
        $image->created_at = new \DateTime('now');
        $image->updated_at = new \DateTime('now');
        $this->entityManagerUser->persist($image);
        $this->entityManagerUser->flush();

        $role = new \Core\Domain\Entity\User\Role();
        $role->id = '4';
        $role->name = 'test';
        $role->deleted = 0;
        $role->created_at = new \DateTime('now');
        $role->updated_at = new \DateTime('now');
        $this->entityManagerUser->persist($role);
        $this->entityManagerUser->flush();
        $user = new \Core\Domain\Entity\User\User();
        $user->firstname = 'test';
        $user->lastname = 'user';
        $user->email = 'test@flipit.com';
        $user->password = md5('password');
        $user->status = 1;
        $user->roleid = '4';
        $user->slug = 'test-user';
        $user->mainText = 'test';
        $user->deleted = 0;
        $user->addtosearch = 0;
        $user->profileimage = $image ;
        $user->currentLogIn = new \DateTime('now');
        $user->lastLogIn = new \DateTime('now');
        $user->created_at = new \DateTime('now');
        $user->updated_at = new \DateTime('now');
        $this->entityManagerUser->persist($user);
        $this->entityManagerUser->flush();

        $ipaddress = new \Core\Domain\Entity\User\IpAddresses();
        $ipaddress->ipaddress = '192.168.56.1';
        $ipaddress->name = 'test';
        $ipaddress->deleted = 0;
        $ipaddress->created_at = new \DateTime('now');
        $ipaddress->updated_at = new \DateTime('now');
        $this->entityManagerUser->persist($ipaddress);
        $this->entityManagerUser->flush();

        for ($i=1; $i < 5; $i++) {
            $category = new \Core\Domain\Entity\Category();
            $category->name = 'test cat'.$i;
            $category->permalink = 'test-cat'.$i;
            $category->deleted = 0;
            $category->created_at = new \DateTime('now');
            $category->updated_at = new \DateTime('now');
            $this->entityManager->persist($category);
            $this->entityManager->flush();
        }

        $image = new \Core\Domain\Entity\Logo();
        $image->ext = 'jpg';
        $image->path = 'images/upload/shop/';
        $image->name = '1409026126_Jellyfish.jpg';
        $image->deleted = 0;
        $image->created_at = new \DateTime('now');
        $image->updated_at = new \DateTime('now');
        $this->entityManager->persist($image);
        $this->entityManager->flush();

        $image = new \Core\Domain\Entity\Logo();
        $image->ext = 'jpg';
        $image->path = 'images/upload/shop/';
        $image->name = '1409026126_Jellyfish.jpg';
        $image->deleted = 0;
        $image->created_at = new \DateTime('now');
        $image->updated_at = new \DateTime('now');
        $this->entityManager->persist($image);
        $this->entityManager->flush();

        for ($i=1; $i <= 20; $i++) {
            $shop = new \Core\Domain\Entity\Shop();
            $shop->name = 'acceptance shop'.$i;
            $shop->permalink = 'acceptance-shop'.$i;
            $shop->title = 'acceptance shop title'.$i;
            $shop->subTitle = 'acceptance shop title';
            $shop->contentmanagerid = '1';
            $shop->affliateProgram = 1;
            $shop->refurl = 'http://www.kortingscode.nl/';
            $shop->actualurl = 'http://www.kortingscode.nl/';
            $shop->howToUse = '1';
            $shop->howtoTitle = 'acceptance shop title';
            $shop->howtoSubtitle = 'acceptance shop title';
            $shop->howtoMetaTitle = 'acceptance shop title';
            $shop->howtoMetaDescription = 'acceptance shop title';
            $shop->howtousesmallimageid = 1;
            $shop->howtousebigimageid = 2;
            $shop->status = 1;
            $shop->usergenratedcontent = 0;
            $shop->deleted = 0;
            $shop->created_at = new \DateTime('now');
            $shop->updated_at = new \DateTime('now');
            $shop->displayExtraProperties = 0;
            $shop->showSignupOption = 0;
            $shop->addtosearch = 0;
            $shop->showSimliarShops = 0;
            $shop->showChains = 0;
            $shop->strictConfirmation = 0;
            $shop->screenshotId = 1;
            $shop->showcustomtext = 'test';
            $shop->customtext = 'test';
            $shop->customtextposition = '1';
            $this->entityManager->persist($shop);
            $this->entityManager->flush();
        }

        for ($i=21; $i <= 40; $i++) {
            $shop = new \Core\Domain\Entity\Shop();
            $shop->name = 'acceptance shop'.$i;
            $shop->permalink = 'acceptance-shop'.$i;
            $shop->title = 'acceptance shop title'.$i;
            $shop->subTitle = 'acceptance shop title';
            $shop->contentmanagerid = '1';
            $shop->affliateProgram = 0;
            $shop->refurl = 'http://www.kortingscode.nl/';
            $shop->actualurl = 'http://www.kortingscode.nl/';
            $shop->howToUse = '1';
            $shop->howtoTitle = 'acceptance shop title';
            $shop->howtoSubtitle = 'acceptance shop title';
            $shop->howtoMetaTitle = 'acceptance shop title';
            $shop->howtoMetaDescription = 'acceptance shop title';
            $shop->howtousesmallimageid = 1;
            $shop->howtousebigimageid = 2;
            $shop->status = 1;
            $shop->usergenratedcontent = 0;
            $shop->deleted = 0;
            $shop->created_at = new \DateTime('now');
            $shop->updated_at = new \DateTime('now');
            $shop->displayExtraProperties = 0;
            $shop->showSignupOption = 0;
            $shop->addtosearch = 0;
            $shop->showSimliarShops = 0;
            $shop->showChains = 0;
            $shop->strictConfirmation = 0;
            $shop->screenshotId = 1;
            $shop->showcustomtext = 'test';
            $shop->customtext = 'test';
            $shop->customtextposition = '1';
            $this->entityManager->persist($shop);
            $this->entityManager->flush();
        }

        for ($i=1; $i < 5; $i++) {
            $refShopCategory = new \Core\Domain\Entity\RefShopCategory();
            $refShopCategory->category = $this->entityManager->find('\Core\Domain\Entity\Shop', $i);
            $refShopCategory->shop = $this->entityManager->find('\Core\Domain\Entity\Category', 2);
            $refShopCategory->deleted = 0;
            $refShopCategory->created_at = new \DateTime('now');
            $refShopCategory->updated_at = new \DateTime('now');
            $this->entityManager->persist($refShopCategory);
            $this->entityManager->flush();
        }
        
        $routePermalink = new \Core\Domain\Entity\RoutePermalink();
        $routePermalink->permalink = 'acceptance-shop';
        $routePermalink->type = 'SHP';
        $routePermalink->exactlink = 'store/storedetail/id/1';
        $routePermalink->deleted = 0;
        $routePermalink->created_at = new \DateTime('now');
        $routePermalink->updated_at = new \DateTime('now');
        $this->entityManager->persist($routePermalink);
        $this->entityManager->flush();

        $routePermalink = new \Core\Domain\Entity\RoutePermalink();
        $routePermalink->permalink = 'how-to/acceptance-shop';
        $routePermalink->type = 'SHP';
        $routePermalink->exactlink = 'store/howtoguide/shopid/1';
        $this->entityManager->persist($routePermalink);
        $routePermalink->deleted = 0;
        $routePermalink->created_at = new \DateTime('now');
        $routePermalink->updated_at = new \DateTime('now');
        $this->entityManager->flush();

        $futureDate = new \DateTime();
        $futureDate->modify('+1 week');
        $futureDate = $futureDate->format('Y-m-d H:i:s');

        $pastDate = new \DateTime();
        $pastDate->modify('-1 week');
        $pastDate = $pastDate->format('Y-m-d H:i:s');

        for ($i=1; $i <= 20; $i++) {
            $offer = new \Core\Domain\Entity\Offer();
            $offer->shopOffers = $this->entityManager->find('\Core\Domain\Entity\Shop', $i);
            $offer->couponCode = 'CD';
            $offer->title = 'test offer'.$i;
            $offer->Visability = 'DE';
            $offer->discountType = 'CD';
            $offer->startDate = new \DateTime($pastDate);
            $offer->endDate = new \DateTime($futureDate);
            $offer->authorId = 1;
            $offer->shopExist = 1;
            $offer->couponCodeType = 'GN';
            $offer->discountvalueType = 2;
            $offer->maxcode = 0;
            $offer->userGenerated = 0;
            $offer->approved = 0;
            $offer->offline = 0;
            $offer->deleted = 0;
            $offer->created_at = new \DateTime('now');
            $offer->updated_at = new \DateTime('now');
            $this->entityManager->persist($offer);
            $this->entityManager->flush();
        }

        $offerTiles = new \Core\Domain\Entity\OfferTiles();
        $offerTiles->label = 'test';
        $offerTiles->type = 'CD';
        $offerTiles->ext = 'png';
        $offerTiles->path = 'images/upload/offertiles';
        $offerTiles->name = 'test.png';
        $offerTiles->deleted = 0;
        $offerTiles->created_at = new \DateTime('now');
        $offerTiles->updated_at = new \DateTime('now');
        $this->entityManager->persist($offerTiles);
        $this->entityManager->flush();

        // $offersTile = $this->entityManager->find('\Core\Domain\Entity\Offer', 1);
        // $offersTile->offerTiles = $offerTiles;
        // $this->entityManager->persist($offersTile);
        // $this->entityManager->flush();

        /*$offerTiles = new \Core\Domain\Entity\RefOfferCategory();
        $offerTiles->offers = $offer;
        $offerTiles->categories = $this->entityManager->find('\Core\Domain\Entity\Category', 45);
        $offerTiles->deleted = 0;
        $offerTiles->created_at = new \DateTime('now');
        $offerTiles->updated_at = new \DateTime('now');
        $this->entityManager->persist($offerTiles);
        $this->entityManager->flush();*/
    }
}
