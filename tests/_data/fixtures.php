<?php

class fixtures
{
    protected $entityManager = '';

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute()
    {
        $image = new KC\Entity\User\ProfileImage();
        $image->ext = 'jpg';
        $image->path = 'images/upload/shop/';
        $image->name = '1409026126_Jellyfish.jpg';
        $image->deleted = 0;
        $image->created_at = new \DateTime('now');
        $image->updated_at = new \DateTime('now');
        $this->entityManager->persist($image);
        $this->entityManager->flush();

        $role = new KC\Entity\User\Role();
        $role->id = '4';
        $role->name = 'test';
        $role->deleted = 0;
        $role->created_at = new \DateTime('now');
        $role->updated_at = new \DateTime('now');
        $this->entityManager->persist($role);
        $this->entityManager->flush();

        $user = new KC\Entity\User\User();
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
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $category = new KC\Entity\Category();
        $category->name = 'test cat';
        $category->permalink = 'test-cat';
        $category->deleted = 0;
        $category->created_at = new \DateTime('now');
        $category->updated_at = new \DateTime('now');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $image = new KC\Entity\Logo();
        $image->ext = 'jpg';
        $image->path = 'images/upload/shop/';
        $image->name = '1409026126_Jellyfish.jpg';
        $image->deleted = 0;
        $image->created_at = new \DateTime('now');
        $image->updated_at = new \DateTime('now');
        $this->entityManager->persist($image);
        $this->entityManager->flush();

        $image = new KC\Entity\Logo();
        $image->ext = 'jpg';
        $image->path = 'images/upload/shop/';
        $image->name = '1409026126_Jellyfish.jpg';
        $image->deleted = 0;
        $image->created_at = new \DateTime('now');
        $image->updated_at = new \DateTime('now');
        $this->entityManager->persist($image);
        $this->entityManager->flush();

        $shop = new KC\Entity\Shop();
        $shop->name = 'acceptance shop';
        $shop->permalink = 'acceptance-shop';
        $shop->title = 'acceptance shop title';
        $shop->subTitle = 'acceptance shop title';
        $shop->contentmanagerid = '1';
        $shop->affliateprogram = 1;
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
        $this->entityManager->persist($shop);
        $this->entityManager->flush();

        $refShopCategory = new KC\Entity\RefShopCategory();
        $refShopCategory->shopid = 'jpg';
        $refShopCategory->categoryid = 'HTUB';
        $refShopCategory->deleted = 0;
        $refShopCategory->created_at = new \DateTime('now');
        $refShopCategory->updated_at = new \DateTime('now');
        $this->entityManager->persist($refShopCategory);
        $this->entityManager->flush();

        $routePermalink = new KC\Entity\RoutePermalink();
        $routePermalink->permalink = 'acceptance-shop';
        $routePermalink->type = 'SHP';
        $routePermalink->exactlink = 'store/storedetail/id/1';
        $routePermalink->deleted = 0;
        $routePermalink->created_at = new \DateTime('now');
        $routePermalink->updated_at = new \DateTime('now');
        $this->entityManager->persist($routePermalink);
        $this->entityManager->flush();

        $routePermalink = new KC\Entity\RoutePermalink();
        $routePermalink->permalink = 'how-to/acceptance-shop';
        $routePermalink->type = 'SHP';
        $routePermalink->exactlink = 'store/howtoguide/shopid/1';
        $this->entityManager->persist($routePermalink);
        $routePermalink->deleted = 0;
        $routePermalink->created_at = new \DateTime('now');
        $routePermalink->updated_at = new \DateTime('now');
        $this->entityManager->flush();

        $offer = new KC\Entity\Offer();
        $offer->shopOffers = $shop;
        $offer->couponCode = 'CD';
        $offer->title = 'test offer';
        $offer->Visability = 'DE';
        $offer->discountType = 'CD';
        $offer->startDate = new \DateTime('now');
        $offer->endDate = new \DateTime('now');
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

        $offerTiles = new KC\Entity\OfferTiles();
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

        // $offersTile = $this->entityManager->find('KC\Entity\Offer', 1);
        // $offersTile->offerTiles = $offerTiles;
        // $this->entityManager->persist($offersTile);
        // $this->entityManager->flush();

        $offerTiles = new KC\Entity\RefOfferCategory();
        $offerTiles->offers = $offer;
        $offerTiles->categories = $category;
        $offerTiles->deleted = 0;
        $offerTiles->created_at = new \DateTime('now');
        $offerTiles->updated_at = new \DateTime('now');
        $this->entityManager->persist($offerTiles);
        $this->entityManager->flush();
    }
}
