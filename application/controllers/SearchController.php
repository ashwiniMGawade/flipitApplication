<?php

class SearchController extends Zend_Controller_Action
{
    public function init()
    {
        $module   = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());

        if (file_exists (APPLICATION_PATH . '/modules/'  . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml")){
            $this->view->setScriptPath( APPLICATION_PATH . '/modules/'  . $module . '/views/scripts' );
        } else{
            $this->view->setScriptPath( APPLICATION_PATH . '/views/scripts' );
        }
        $this->viewHelperObject = new FrontEnd_Helper_viewHelper();
    }

    public function indexAction()
    {
        $searchPermalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $pagePermalink = FrontEnd_Helper_viewHelper::__link('zoeken');
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($pagePermalink);
        $pageAttributeId = Page::getPageAttributeByPermalink($pagePermalink);
        $pageDetail = Page::getPageFromPageAttribute($pageAttributeId);
        $this->view->pageTitle = $pageDetail->pageTitle;
        $searchedKeywords = $this->getRequest()->getParam('searchField');
        $shopIds = "";
        $shopIds =$this->_helper->Search->getExcludedShopIdsBySearchedKeywords($searchedKeywords);
        $shopsByShopIds = $this->_helper->Search->getshopsByExcludedShopIds($shopIds);
        $popularShops = $this->_helper->Search->getPopularStores($searchedKeywords);
        $shopsForSearchPage = $this->_helper->Search->getStoresForSearchResults($shopsByShopIds, $popularShops);
        $popularStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache('all_popularshop_list', Shop::getAllPopularStores(10), true);
        $offersBySearchedKeywords = Offer::searchOffers($this->_getAllParams(), $shopIds, 12);

        if($searchedKeywords == ''){
            $this->view->popularStores = $popularStores;
            $this->view->offers = array();
        } else { 
            $this->view->popularStores = $popularStores;
            if (!empty($offersBySearchedKeywords)) {
                $this->view->popularStores = $shopsForSearchPage; 
            }
            $this->view->offers = $offersBySearchedKeywords; 
        }

        $this->view->searchedKeyword = ($searchedKeywords !="" || $searchedKeywords != null) ? $searchedKeywords : '';
        $customHeader = isset($pageDetail->customHeader) ? $pageDetail->customHeader : '';
        $this->viewHelperObject->getFacebookMetaTags($this, $pageDetail->pageTitle, $pageDetail->metaTitle, trim($pageDetail->metaDescription), $pageDetail->permaLink, FACEBOOK_IMAGE, $customHeader);

        $this->view->pageLogo = '';
        if(isset($pageDetail->logo->path)) {
            $this->view->pageLogo = PUBLIC_PATH_CDN.$pageDetail->logo->path.$pageDetail->logo->name;
        }
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formSignupSidebarWidget', 'SignUp ');
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, '', $signUpFormSidebarWidget);
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;

    }
}
