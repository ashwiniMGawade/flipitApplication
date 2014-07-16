<?php
class Zend_Controller_Action_Helper_Category extends Zend_Controller_Action_Helper_Abstract
{
    public static function getSpecialPageWithOffersCount($specialPages)
    {
        $specialPageWithOffersCount = '';
        foreach ($specialPages as $specialPage) {
            $specialPageOffersCount = count(Offer::getSpecialPageOffers($specialPage));
            $specialPageWithOffersCount[] = array(
                'permaLink' =>$specialPage['permaLink'],
                'name' =>$specialPage['pageTitle'],
                'totalCoupons'=> $specialPageOffersCount ,
                'logo'=>$specialPage['logo']
            );
        }
        return $specialPageWithOffersCount;
    }
}
