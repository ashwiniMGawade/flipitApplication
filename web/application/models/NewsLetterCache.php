<?php
class NewsLetterCache extends BaseNewsLetterCache
{

    public static function saveNewsLetterCacheContent()
    {
        self::truncateNewsletterCacheTable();
        $newLetterHeaderAndFooter = Signupmaxaccount::getEmailHeaderFooter();
        self::saveValueInDatebase('email_header', $newLetterHeaderAndFooter['email_header']);
        self::saveValueInDatebase('email_footer', $newLetterHeaderAndFooter['email_footer']);
        $topCategory = FrontEnd_Helper_viewHelper::gethomeSections('category', 1);
        self::saveValueInDatebase('top_category_id', $topCategory[0]['categoryId']);
        $topOfferIds = implode(',', self::getOfferIds(Offer::getTopOffers(10)));
        self::saveValueInDatebase('top_offers_ids', $topOfferIds);
        $topCategoryOffersIds = implode(',', self::getOfferIds(
            Category::getCategoryVoucherCodes($topCategory[0]['categoryId'], 3)
        ));
        self::saveValueInDatebase('top_category_offers_ids', $topCategoryOffersIds);
        return true;
    }

    public static function truncateNewsletterCacheTable()
    {
        $databaseConnection = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS=0');
        $databaseConnection->query('TRUNCATE TABLE news_letter_cache');
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS=1');
        unset($databaseConnection);
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
        $getAllNewsLetterCache = Doctrine_Core::getTable("NewsLetterCache")->findAll(Doctrine::HYDRATE_ARRAY);
        $allNewsLetterCacheContent = array();
        foreach ($getAllNewsLetterCache as $newsLetterCacheColumnValue) {
            $allNewsLetterCacheContent[$newsLetterCacheColumnValue['name']] = $newsLetterCacheColumnValue['value'];
        }
        return $allNewsLetterCacheContent;
    }

    protected static function saveValueInDatebase($newsLetterCacheColumnName, $newsLetterCacheColumnValue)
    {
        $newsLetterCache = new NewsLetterCache();
        $newsLetterCache->name = $newsLetterCacheColumnName;
        $newsLetterCache->value = $newsLetterCacheColumnValue;
        $newsLetterCache->status = false;
        $newsLetterCache->save();
        return true;
    }

    public static function getCategoryByFallBack($categoryId)
    {
        if (Category::categoryExistOrNot($categoryId)) {
            $topCategory = Category::getCategoryInformationForNewsLetter($categoryId);
        } else {
            $topCategoryId = FrontEnd_Helper_viewHelper::gethomeSections('category', 1);
            $topCategory = Category::getCategoryInformationForNewsLetter($topCategoryId[0]['categoryId']);
        }
        return $topCategory;
    }

    public static function getTopOffersByFallBack($topOffers)
    {
        $topOffersIds = explode(',', $topOffers);
        $offersExist = true;
        foreach ($topOffersIds as $topOffersId) {
            if (!Offer::offerExistOrNot($topOffersId)) {
                $offersExist = false;
            }
        }
        if ($offersExist) {
            $topVouchercodes = Offer::getOffersForNewsletter(array_unique($topOffersIds));
        } else {
            $topVouchercodes = Offer::getTopOffers(10);
        }
        return $topVouchercodes;
    }

    public static function getTopCategoryOffersByFallBack($topCategoryOffersIds, $topCategoryId)
    {
        $categoryOffersIds =  explode(',', $topCategoryOffersIds);
        $categoryOffersExist = true;
        foreach ($categoryOffersIds as $categoryOffersId) {
            if (!Offer::offerExistOrNot($categoryOffersId)) {
                $categoryOffersExist = false;
            }
        }
        if ($categoryOffersExist) {
            $categoryVouchers = Offer::getOffersForNewsletter($categoryOffersIds);
        } else {
            $categoryVouchers = Category::getCategoryVoucherCodes($topCategoryId, 3);
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
