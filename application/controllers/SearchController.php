<?php

class SearchController extends Zend_Controller_Action
{
    public function init()
    {
        $module   = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());

        if (
            file_exists(
                APPLICATION_PATH . '/modules/'. $module . '/views/scripts/' . $controller . '/' . $action . ".phtml"
            )
        ) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/'  . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }
        $this->viewHelperObject = new \FrontEnd_Helper_viewHelper();
    }

    public function indexAction()
    {
        $searchPermalink = ltrim(\Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $splitSearchPermalink = explode('/', $searchPermalink);
        $pagePermalink = isset($splitSearchPermalink[2]) ? $splitSearchPermalink[1] : $splitSearchPermalink[0];

        $this->view->canonical = \FrontEnd_Helper_viewHelper::generateCononical($pagePermalink);
        $pageDetails = \KC\Repository\Page::getPageDetailsFromUrl(\FrontEnd_Helper_viewHelper::__link('link_zoeken'));
        $pageDetails = (object) $pageDetails;
        $this->view->pageHeaderImage = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'page_header'.$pageDetails->id.'_image',
            array(
                'function' => '\KC\Repository\Logo::getPageLogo',
                'parameters' => array($pageDetails->pageHeaderImageId['id'])
            ),
            ''
        );
        $this->view->pageTitle = isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '';
        $searchedKeywords = strtolower(
            \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialCharacterAndReplacedWithHyphen(
                $this->getRequest()->getParam('searchField')
            )
        );
        $shopIds = "";
        $shopIds =$this->_helper->Search->getExcludedShopIdsBySearchedKeywords($searchedKeywords);
        $shopsByShopIds = $this->_helper->Search->getshopsByExcludedShopIds($shopIds, 5);
        $popularShops = $this->_helper->Search->getPopularStores($searchedKeywords);
        $shopsForSearchPage = $this->_helper->Search->getStoresForSearchResults($shopsByShopIds, $popularShops);
        $popularStores = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            '10_popularShops_list',
            array('function' => '\KC\Repository\Shop::getAllPopularStores',
                'parameters' => array(10)),
            true
        );
        $offersBySearchedKeywords = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'offers_by_searchedkeywords'.\FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter(
                str_replace(" ", "", $searchedKeywords)
            ),
            array('function' => '\KC\Repository\Offer::searchOffers',
                'parameters' => array($this->_getAllParams(), $shopIds, 12)),
            true
        );

        if (empty($offersBySearchedKeywords) || $searchedKeywords == '') {
            $this->view->popularStores = $popularStores;
            $this->view->offers = array();
        } else {
            $this->view->popularStores = $shopsForSearchPage;
            $this->view->offers = $offersBySearchedKeywords;
        }

        $this->view->searchedKeyword = ($searchedKeywords !="" || $searchedKeywords != null) ? $searchedKeywords : '';
        $this->viewHelperObject->getMetaTags(
            $this,
            isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '',
            isset($pageDetails->metaTitle) ? $pageDetails->metaTitle : '',
            isset($pageDetails->metaDescription) ? $pageDetails->metaDescription : '',
            isset($pageDetails->permaLink) ? $pageDetails->permaLink : '',
            FACEBOOK_IMAGE,
            isset($pageDetails->customHeader) ? $pageDetails->customHeader : ''
        );
        $signUpFormSidebarWidget = \FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp '
        );
        \FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, '', $signUpFormSidebarWidget);
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $this->view->pageCssClass = 'page-store';
    }
}
