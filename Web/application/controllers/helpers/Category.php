<?php
class Zend_Controller_Action_Helper_Category extends Zend_Controller_Action_Helper_Abstract
{
    public static function getSpecialPageWithOffersCount($specialPages)
    {
        $specialPageWithOffersCount = '';
        foreach ($specialPages as $specialPage) {
            $specialPageWithOffersCount[] = array(
                'permaLink' =>$specialPage['permalink'],
                'name' =>$specialPage['pageTitle'],
                'totalCoupons'=> $specialPage['offersCount'],
                'logo'=>$specialPage['logo']
            );
        }
        return $specialPageWithOffersCount;
    }

    public static function getCategories($categories)
    {
        $categoryRemoveIndex = '';
        foreach ($categories as $category) {
            $category[0]['totalCoupons']  = $category['totalCoupons'];
            $categoryRemoveIndex[] = $category[0];
        }
        return $categoryRemoveIndex;
    }
}
