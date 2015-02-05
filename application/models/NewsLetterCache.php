<?php
class NewsLetterCache extends BaseNewsLetterCache
{

    public static function saveNewLetterCacheContent()
    {
        self::truncateNewletterCacheTable();
        $newLetterHeaderAndFooter = Signupmaxaccount::getEmailHeaderFooter();
        self::saveValueInDatebase('email_header', $newLetterHeaderAndFooter['email_header']);
        self::saveValueInDatebase('email_footer', $newLetterHeaderAndFooter['email_footer']);
        $topCategories = FrontEnd_Helper_viewHelper::gethomeSections('category', 1);
        self::saveValueInDatebase('top_category_id', $topCategories[0]['categoryId']);
        $topOfferIds = implode(',', self::getOfferIds(Offer::getTopOffers(10)));
        self::saveValueInDatebase('top_offers_ids', $topOfferIds);
        $topCategoryOffersIds = implode(',', self::getOfferIds(
            Category::getCategoryVoucherCodes($topCategories[0]['categoryId'], 3)
        ));
        self::saveValueInDatebase('top_category_offers_ids', $topCategoryOffersIds);
        return true;
    }

    public static function truncateNewletterCacheTable()
    {
        $databaseConnection = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS=0');
        $databaseConnection->query('TRUNCATE TABLE news_letter_cache');
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS=1');
        unset($databaseConnection);
    }

    public static function getOfferIds($offers)
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

    public static function saveValueInDatebase($newsLetterCacheColumnName, $newsLetterCacheColumnValue)
    {
        $newsLetterCache = new NewsLetterCache();
        $newsLetterCache->name = $newsLetterCacheColumnName;
        $newsLetterCache->value = $newsLetterCacheColumnValue;
        $newsLetterCache->status = false;
        $newsLetterCache->save();
        return true;
    }
}
