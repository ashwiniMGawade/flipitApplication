<?php
class Zend_Controller_Action_Helper_Category extends Zend_Controller_Action_Helper_Abstract
{
    public static function getSpecialPageWithOffersCount($specialPages)
    {
        $specialPageWithOffersCount = '';
        foreach ($specialPages as $specialPage) {
            $specialPageWithOffersCount[] = array(
                'permaLink' =>$specialPage['permaLink'],
                'name' =>$specialPage['pageTitle'],
                'totalCoupons'=> $specialPage['offersCount'],
                'logo'=>$specialPage['logo']
            );
        }
        return $specialPageWithOffersCount;
    }
}
