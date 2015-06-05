<?php
class Application_Service_Offer_SimilarOffer extends Application_Service_Offer_OfferListing
{
    protected $offerRepository = '';
    protected $shopId = '';
    protected $affiliateProgram = '';
    public function __construct($offerRepository, $shopId, $affiliateProgram)
    {
        $this->offerRepository = $offerRepository;
        $this->shopId = $shopId;
        $this->affiliateProgram = $affiliateProgram;
    }

    public function fetchSimilarShopOffers()
    {
        $getSimilarShopOffers = $this->getSimilarShopOffers();
        if (!empty($getSimilarShopOffers)) {
            if ($this->affiliateProgram != 0) {
                $getSimilarShopOffers = $this->getSlicedNumberOfShopSimilarOffers($getSimilarShopOffers, 3);
            } else {
                $getSimilarShopOffers = $this->getSlicedNumberOfShopSimilarOffers($getSimilarShopOffers, 10);
            }
        } else {
            $getSimilarShopOffers = '';
        }

        return $getSimilarShopOffers;
    }

    protected function getSimilarShopOffers()
    {
        $similarShopsAndSimilarCategoriesOffers = $this->getMergedSimilarShopOffers(30);
        return $similarShopsAndSimilarCategoriesOffers;
    }

    public function getMergedSimilarShopOffers($limit)
    {
        $date = date("Y-m-d H:i");
        $similarOffersFromShops = $this->getOffersBySimilarShops($date, $limit);
        $similarOffersFromShopCategories = $this->getOffersBySimilarCategories($date, $limit);
        $similarShopsAndSimilarCategoriesOffers = $this->mergeSimilarShopsOffersAndSimilarCategoriesOffers(
            $similarOffersFromShops,
            $similarOffersFromShopCategories
        );
        return $similarShopsAndSimilarCategoriesOffers;
    }

    protected static function mergeSimilarShopsOffersAndSimilarCategoriesOffers(
        $similarShopsOffers,
        $similarCategoriesOffers
    ) {
        $shopsOffers = self::createShopsArrayAccordingToOfferHtml($similarShopsOffers);
        $categoriesOffers = self::createCategoriesArrayAccordingToOfferHtml($similarCategoriesOffers);
        $mergedOffers = array_merge($shopsOffers, $categoriesOffers);
        return $mergedOffers;
    }

    public static function createShopsArrayAccordingToOfferHtml($similarShopsOffers)
    {
        $newOffersOfRelatedShops = array();
        foreach ($similarShopsOffers as $shopIndex => $shopOffer):
            if (isset($shopOffer[0]['shopOffers'][0])) {
                $categoryShops = $shopOffer[0]['shopOffers'][0];
                unset($shopOffer[0]['shopOffers'][0]);
                $shopOffer[0]['shopOffers'] = $shopOffer[0]['shopOffers'] + $categoryShops;
            }
            $newOffersOfRelatedShops[$shopIndex] = $shopOffer[0];
        endforeach;
        return $newOffersOfRelatedShops;
    }

    public static function createCategoriesArrayAccordingToOfferHtml($similarCategoriesOffers)
    {
        $newOfferOfRelatedCategories = array();
        foreach ($similarCategoriesOffers as $categoryOffer):
            $newOfferOfRelatedCategories["'".$categoryOffer['id']."'"] = $categoryOffer;
        endforeach;
        return $newOfferOfRelatedCategories;
    }

    protected function getOffersBySimilarShops($date, $limit)
    {
        return $this->offerRepository->getOffersBySimilarShops($date, $limit, $this->shopId);
    }

    protected function getOffersBySimilarCategories($date, $limit)
    {
        return $this->offerRepository->getOffersBySimilarCategories($date, $limit, $this->shopId);
    }

    protected function getSlicedNumberOfShopSimilarOffers($offers, $limit)
    {
        $uniqueOffers = $this->removeDuplicateShopsOffers($offers);
        $slicedOffers = array_slice($uniqueOffers, 0, $limit);
        return $slicedOffers;
    }
    
    protected function removeDuplicateShopsOffers($similarShopsOffers)
    {
        $removeDuplicateShop = '';
        foreach ($similarShopsOffers as $offerIndex => $offer) {
            $removeDuplicateShop[$offer['shopOffers']['id']] = $similarShopsOffers[$offerIndex];
        }
        $offersUnique = '';
        foreach ($removeDuplicateShop as $shopIndex => $offer) {
            $offersUnique[] = $removeDuplicateShop[$shopIndex];
        }
        return $offersUnique;
    }
}
