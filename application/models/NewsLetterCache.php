<?php
class NewsLetterCache extends BaseNewsLetterCache
{

    public static function saveData()
    {
        $newLetterHeaderAndFooter = Signupmaxaccount::getEmailHeaderFooter();
        $newLetterHeaderAndFooter['email_header'];
        $topCategories = FrontEnd_Helper_viewHelper::gethomeSections('category', 1);
        $topVouchercodes = Offer::getTopOffers(10);
        $topCategoryOffers = Category::getCategoryVoucherCodes($topCategories[0]['categoryId'], 3);

        echo "<pre>";
        $last_names = array_column($topVouchercodes, 'id');
        print_r($last_names);
        print_r($categoryVouchers);
        die;
    }

    public static function getNewsLetterCache($newsLetterCacheName)
    {
        $newsLetterCache = Doctrine_Query::create()->select('newsLetCac.name,newsLetCac.value')
        ->from("NewsLetterCache newsLetCac")
        ->andWhere("newsLetCac.name LIKE ?", "$newsLetterCacheName%")
        ->fetchArray();
        return $newsLetterCache ;
    }

    public static function getAllSettings()
    {
        $getAll = Doctrine_Core::getTable("NewsLetterCache")
            ->findAll(Doctrine::HYDRATE_ARRAY);
        $data = array();
        foreach ($getAll as $val) {
            $data[$val['name']] = $val['value'];
        }
        return $data ;
    }

    public static function setNewsLetterCache($name, $value)
    {
        $Q = Doctrine_Query::create()
            ->update('NewsLetterCache')
            ->set("value", $value)
            ->where('name = ?', $name);
        $Q->execute();
    }

    public static function saveValueInDatebase($fieldName, $fieldValue)
    {
        $newsLetterCache = new NewsLetterCache();
        $newsLetterCache->name = $fieldName;
        $newsLetterCache->value = $fieldValue;
        $newsLetterCache->status = false;
        $newsLetterCache->save();
        return true;
    }
}
