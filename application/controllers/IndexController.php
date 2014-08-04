<?php
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $module = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action = strtolower($this->getRequest()->getActionName());
        if (
            file_exists(
                APPLICATION_PATH . '/modules/' . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml"
            )
        ) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/' . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }
        $this->view->banner = Signupmaxaccount::getHomepageImages();
        $this->viewHelperObject = new FrontEnd_Helper_viewHelper();
    }

    public function indexAction()
    {
        $this->view->canonical = '';
        $this->view->controllerName = $this->getRequest()->getControllerName();
        $this->view->action = $this->getRequest()->getActionName();
        $pageDetails = Page::getPageDetailsFromUrl($this->getRequest()->getActionName());
        $this->view->pageTitle = ucfirst(isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '');
        $this->viewHelperObject->getMetaTags(
            $this,
            isset($pageDetails->metaTitle) ? $pageDetails->metaTitle : '',
            ucfirst(trim(isset($pageDetails->metaTitle) ? $pageDetails->metaTitle :'')),
            trim(isset($pageDetails->metaDescription) ? $pageDetails->metaDescription : ''),
            '',
            FACEBOOK_IMAGE,
            isset($pageDetails->customHeader) ? $pageDetails->customHeader : ''
        );
        if (FrontEnd_Helper_HomePagePartialFunctions:: getFlipitHomePageStatus()) {
            $this->view->topOffers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                "10_popularShopsForHomePage_list",
                array('function' => 'Offer::getTopOffers', 'parameters' => array(10))
            );
            $this->view->newOffers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                "10_newOffers_list",
                array('function' => 'Offer::getNewestOffers', 'parameters' => array('newest', 10))
            );
            $topCategories = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                "10_popularCategories_list",
                array('function' => 'Category::getPopularCategories', 'parameters' => array(10))
            );
            $this->view->topCategories = $topCategories;
            $topCategoriesIds = $this->_helper->Index->getTopCategoriesIds($topCategories);
            $topCategoriesOffers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                "all_homeTopCategoriesOffers_list",
                array(
                    'function' => 'Category::getCategoryVoucherCodes',
                    'parameters' => array($topCategoriesIds, 0, 'home')
                )
            );
            $this->view->topCategoriesOffers = $this->_helper->Index->getCategoriesOffers($topCategoriesOffers);
            $specialListPages = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                "all_specialPages_list",
                array('function' => 'SpecialList::getSpecialPages', 'parameters' => array())
            );
            $this->view->specialListPages = $specialListPages;

            $specialListCountKey ="all_specialPages_count";
            $cacheStatus =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($specialListCountKey);
            if ($cacheStatus) {
                $specialPagesOffers = $this->_helper->Index->getSpecialListPagesOffers($specialListPages);
                FrontEnd_Helper_viewHelper::setInCache($specialListCountKey, $specialPagesOffers);
            } else {
                $specialPagesOffers  = FrontEnd_Helper_viewHelper::getFromCacheByKey($specialListCountKey);
            }

            $this->view->specialPagesOffers = $specialPagesOffers;
            $this->view->moneySavingGuides = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                "all_moneySaving_list",
                array('function' => 'Articles::getAllArticles', 'parameters' => array())
            );
            $this->view->topStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                "all_popularShops_list",
                array(
                    'function' => 'FrontEnd_Helper_viewHelper::getStoreForFrontEnd',
                    'parameters' => array("popular", 24)
                )
            );
            $this->view->seeninContents = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                "all_homeSeenIn_list",
                array('function' => 'SeenIn::getSeenInContent', 'parameters' => array(10))
            );
            $this->view->aboutTabs = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                "all_about_page",
                array('function' => 'About::getAboutContent', 'parameters' => array(1))
            );
            $this->view->pageCssClass = 'home-page';
        } else {
            $this->_helper->viewRenderer->setNoRender(TRUE);
        }
    }
}
