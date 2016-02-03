<?php

class Admin_CategoriesoffersController extends Application_Admin_BaseController
{
    public function preDispatch()
    {
        $dbConnection = \BackEnd_Helper_viewHelper::addConnection(); // connection
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        \BackEnd_Helper_viewHelper::closeConnection($dbConnection);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
    }
    
    public function indexAction()
    {
        $categoryId = $this->getRequest()->getParam('categoryId');
        $homePageCategoriesList = \KC\Repository\Category::getPopularCategories(10, 'home');
        if (!empty($categoryId)) {
            $categoryId = $this->getRequest()->getParam('categoryId');
        } else {
            $categoryId = isset($homePageCategoriesList[0]) ? $homePageCategoriesList[0]['category']['id'] : '';
        }
        $currentCategoryHomePageOffers = \KC\Repository\CategoriesOffers::getCategoryOffersById($categoryId);
        $categoryOffers = \KC\Repository\Category::getCategoryVoucherCodes(
            $categoryId,
            100,
            '',
            !empty($currentCategoryHomePageOffers) ? $currentCategoryHomePageOffers : ''
        );
        
        $this->view->currentCategoryHomePageOffers = $currentCategoryHomePageOffers;
        $this->view->categoryOffers = $categoryOffers;
        $this->view->homePageCategoriesList = $homePageCategoriesList;
        $this->view->categoryId = $categoryId;
    }


    public function addofferAction()
    {
        $offerId = $this->getRequest()->getParam('id');
        $categoryId = $this->getRequest()->getParam('categoryId');
        $categoryData = \KC\Repository\CategoriesOffers::addOfferInList($offerId, $categoryId);
        echo Zend_Json::encode($categoryData);
        exit();
    }
 
    public function deletecodeAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        $categoryId = $this->getRequest()->getParam('categoryId');
        $isUpdated = \KC\Repository\CategoriesOffers::deleteCode($id, $position, $categoryId);
        $categoryOffers = \KC\Repository\CategoriesOffers::getCategoryOffersById($categoryId);
        echo Zend_Json::encode($categoryOffers);
        exit();
    }

    public function savepositionAction()
    {
        $this->_helper->layout->disableLayout();
        $categoryId = $this->getRequest()->getParam('categoryId');
        \KC\Repository\CategoriesOffers::savePosition($this->getRequest()->getParam('offersIds'), $categoryId);
        $categoryOffers = \KC\Repository\CategoriesOffers::getcategoryOffersById($categoryId);
        $date = new DateTime();
        $newCategoryOffers = array();
        foreach($categoryOffers as $offer) {
            $expired = '';
            if($date > $offer['offers']['endDate']) {
                $expired = "<i class='pull-right icon icon-time'></i>";
            }
            $offer['offers']['expiredTxt'] = $expired;
            $newCategoryOffers[] = $offer;
        }
        echo Zend_Json::encode($newCategoryOffers);
        exit();
    }
}
