<?php
namespace KC\Repository;
class NewsLetterCache extends \Core\Domain\Entity\NewsLetterCache
{
    public static function saveNewsLetterCacheContent()
    {
        self::truncateNewsletterCacheTable();
        $newLetterHeaderAndFooter = \KC\Repository\Signupmaxaccount::getEmailHeaderFooter();
        self::saveValueInDatebase('email_header', $newLetterHeaderAndFooter[0]['email_header']);
        self::saveValueInDatebase('email_footer', $newLetterHeaderAndFooter[0]['email_footer']);
        $topCategory = \FrontEnd_Helper_viewHelper::gethomeSections('category', 1);
        self::saveValueInDatebase('top_category_id', $topCategory[0]['category']['id']);
        $topOfferIds = implode(',', self::getOfferIds(\BackEnd_Helper_viewHelper::getTopOffers(10)));
        self::saveValueInDatebase('top_offers_ids', $topOfferIds);
        $topCategoryOffersIds = self::getCategoryOffers($topCategory[0]['category']['id']);
        self::saveValueInDatebase('top_category_offers_ids', $topCategoryOffersIds);
        return true;
    }

    public static function truncateNewsletterCacheTable()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->delete('\Core\Domain\Entity\NewsLetterCache', 'nlc')
                ->where('nlc.id > 0')
                ->getQuery();
        $query->execute();
    }

    protected static function getOfferIds($offers)
    {
        $offersIds = array();
        foreach ($offers as $offer) {
            $offersIds[] = $offer['id'];
        }
        return $offersIds;
    }

    public static function getAllNewsLetterCacheContent()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('nlc')
            ->from('\Core\Domain\Entity\NewsLetterCache', 'nlc');
        $getAllNewsLetterCache = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $allNewsLetterCacheContent = array();
        foreach ($getAllNewsLetterCache as $newsLetterCacheColumnValue) {
            $allNewsLetterCacheContent[$newsLetterCacheColumnValue['name']] = $newsLetterCacheColumnValue['value'];
        }
        return $allNewsLetterCacheContent;
    }

    protected static function saveValueInDatebase($newsLetterCacheColumnName, $newsLetterCacheColumnValue)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $newsLetterCache = new \Core\Domain\Entity\NewsLetterCache();
        $newsLetterCache->name = $newsLetterCacheColumnName;
        $newsLetterCache->value = $newsLetterCacheColumnValue;
        $newsLetterCache->status = false;
        $newsLetterCache->deleted = 0;
        $newsLetterCache->created_at = new \DateTime('now');
        $newsLetterCache->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($newsLetterCache);
        $entityManagerLocale->flush();
        return true;
    }

    public static function getCategoryByFallBack($categoryId)
    {
        if (\KC\Repository\Category::categoryExistOrNot($categoryId)) {
            $topCategory = \KC\Repository\Category::getCategoryInformationForNewsLetter($categoryId);
        } else {
            $topCategoryId = \FrontEnd_Helper_viewHelper::gethomeSections('category', 1);
            $topCategory = \KC\Repository\Category::getCategoryInformationForNewsLetter($topCategoryId[0]['categoryId']);
        }
        return $topCategory;
    }

    public static function getTopOffersByFallBack($topOffers)
    {
        $offersExist = true;
        if(!empty($topOffers)) {
            $topOffersIds = explode(',', $topOffers);
            foreach ($topOffersIds as $topOffersId) {
                if (is_null(\KC\Repository\Offer::offerExistOrNot($topOffersId))) {
                    $offersExist = false;
                }
            }
        } else {
            $offersExist = false;
        }
        if ($offersExist) {
            $topVouchercodes = \KC\Repository\Offer::getOffersForNewsletter($topOffersIds);
        } else {
            $topVouchercodes = \KC\Repository\Offer::getTopOffers(10);
        }
        return $topVouchercodes;
    }

    public static function getTopCategoryOffersByFallBack($topCategoryOffersIds, $topCategoryId)
    {
        $categoryOffersIds =  explode(',', $topCategoryOffersIds);
        $categoryOffersExist = true;
        foreach ($categoryOffersIds as $categoryOffersId) {
            if (is_null(\KC\Repository\Offer::offerExistOrNot($categoryOffersId))) {
                $categoryOffersExist = false;
            }
        }
        if ($categoryOffersExist) {
            $categoryVouchers = \KC\Repository\Offer::getOffersForNewsletter($categoryOffersIds);
        } else {
            $categoryVouchers = \KC\Repository\Category::getCategoryVoucherCodes($topCategoryId, 3);
        }
        return $categoryVouchers;
    }

    public static function getEmailHeaderByFallBack($newsLetterCacheHeader, $settingHeader)
    {
        if (!empty($newsLetterCacheHeader)) {
            $emailHeader = $newsLetterCacheHeader;
        } else {
            $emailHeader = $settingHeader;
        }
        return $emailHeader;
    }

    public static function getEmailFooterByFallBack($newsLetterCacheFooter, $settingFooter)
    {
        if (!empty($newsLetterCacheFooter)) {
            $emailFooter = $newsLetterCacheFooter;
        } else {
            $emailFooter = $settingFooter;
        }
        return $emailFooter;
    }

    public static function getCategoryOffers($categoryId) {
        $categoryOrderedOffers = self::getOfferIds(
            \KC\Repository\CategoriesOffers::getCategoryOffersByCategoryIdForFrontEnd($categoryId)
        );
        $categoryAllOffers = array();
        if(count($categoryOrderedOffers) < 3) {
            $categoryAllOffers = self::getOfferIds(
                \KC\Repository\Category::getCategoryVoucherCodes($categoryId, 3)
            );
        }
        return implode(',', array_slice(array_unique(array_merge($categoryOrderedOffers, $categoryAllOffers)), 0, 3));
    }
}
