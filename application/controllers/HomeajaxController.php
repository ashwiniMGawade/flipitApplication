<?php
require_once 'Zend/Controller/Action.php';
class HomeajaxController extends Zend_Controller_Action
{
    public function getcategoryoffersAction()
    {
        $categoryId = $this->getRequest()->getParam('categoryid');
        $categoryPermalink = $this->getRequest()->getParam('permalink');
        $topCategoriesOffers = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            "all_hometocategoryoffers". $categoryId ."_list",
            array(
                'function' => 'KC\Repository\Category::getCategoryVoucherCodes',
                'parameters' => array($categoryId, 50, 'homePage')
            )
        );
        $offers = count($topCategoriesOffers) > 10
            ? $this->_helper->Index->removeDuplicateCode($topCategoriesOffers, 'homePage')
            :  $topCategoriesOffers;
        
        $homePagePartials = new \FrontEnd_Helper_HomePagePartialFunctions();
        $rightDivWithContent = $homePagePartials->getRightDivByAjax(
            $offers,
            $categoryPermalink,
            \FrontEnd_Helper_viewHelper::__form('form_All') . " " . $categoryPermalink. " "
            . \FrontEnd_Helper_viewHelper::__form('form_Code'),
            HTTP_PATH_LOCALE. \FrontEnd_Helper_viewHelper::__link('link_categorieen') .'/'. $categoryPermalink
        );
        echo \Zend_Json::encode($rightDivWithContent);
        die;
    }

    public function getnewestoffersAction()
    {
        $newOffers = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            "all_homenewoffer_list",
            array('function' => 'KC\Repository\Offer::getNewestOffers', 'parameters' => array('newest', 10, '', '', 'homePage'))
        );
 
        $homePagePartials = new \FrontEnd_Helper_HomePagePartialFunctions();
        $rightDivWithContent = $homePagePartials->getRightDivByAjax(
            $newOffers,
            'newOffers',
            \FrontEnd_Helper_viewHelper::__form('form_All New Codes'),
            HTTP_PATH_LOCALE.\FrontEnd_Helper_viewHelper::__link('link_nieuw')
        );
        echo Zend_Json::encode($rightDivWithContent);
        die;
    }

    public function getmoneysavingguidesAction()
    {
        $moneySavingGuidesList = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            "all_homemoneysaving_list",
            array('function' => 'KC\Repository\Articles::getAllArticlesForHomePage', 'parameters' => array(10))

        );
        $homePagePartials = new \FrontEnd_Helper_HomePagePartialFunctions();
        $guidesHtml = $homePagePartials->getMoneySavingGuidesRightForAjax(
            $moneySavingGuidesList,
            'moneysaving',
            \FrontEnd_Helper_viewHelper::__form('form_All Saving Guides'),
            HTTP_PATH_LOCALE.\FrontEnd_Helper_viewHelper::__link('link_plus')
        );
        echo \Zend_Json::encode($guidesHtml);
        die;
    }
}

