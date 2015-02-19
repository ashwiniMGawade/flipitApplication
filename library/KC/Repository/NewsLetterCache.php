<?php
namespace KC\Repository;
class NewsLetterCache Extends \KC\Entity\newsLetterCache
{
    public static function saveNewsLetterCacheContent()
    {
        self::truncateNewsletterCacheTable();
        $newLetterHeaderAndFooter = \KC\Repository\Signupmaxaccount::getEmailHeaderFooter();
        self::saveValueInDatebase('email_header', $newLetterHeaderAndFooter['email_header']);
        self::saveValueInDatebase('email_footer', $newLetterHeaderAndFooter['email_footer']);
        $topCategory = \FrontEnd_Helper_viewHelper::gethomeSections('category', 1);
        self::saveValueInDatebase('top_category_id', $topCategory[0]['categoryId']);
        $topOfferIds = implode(',', self::getOfferIds(\KC\Repository\Offer::getTopOffers(10)));
        self::saveValueInDatebase('top_offers_ids', $topOfferIds);
        $topCategoryOffersIds = implode(',', self::getOfferIds(
            \KC\Repository\Category::getCategoryVoucherCodes($topCategory[0]['categoryId'], 3)
        ));
        self::saveValueInDatebase('top_category_offers_ids', $topCategoryOffersIds);
        return true;
    }

    public static function truncateNewsletterCacheTable()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->delete('KC\Entity\newsLetterCache', 'nlc')
                ->where('nlc.id > 0')
                ->getQuery();
        $query->execute();
    }

    protected static function getOfferIds($offers)
    {
        $offersIds = '';
        foreach ($offers as $offer) {
            $offersIds[] = $offer['id'];
        }
        return $offersIds;
    }

    public static function getAllNewsLetterCacheContent()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('nlc')
            ->from('\KC\Entity\newsLetterCache', 'nlc');
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
        $newsLetterCache = new \KC\Entity\newsLetterCache();
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
        $topOffersIds = explode(',', $topOffers);
        $offersExist = true;
        foreach ($topOffersIds as $topOffersId) {
            if (!\KC\Repository\Offer::offerExistOrNot($topOffersId)) {
                $offersExist = false;
            }
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
            if (!\KC\Repository\Offer::offerExistOrNot($categoryOffersId)) {
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
}
