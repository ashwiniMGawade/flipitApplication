<?php

class SearchController extends Zend_Controller_Action
{
    ##################################################################################
    ################## REFACTORED CODE ###############################################
    ##################################################################################
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
    }

    public function indexAction()
    {
        $searchPermalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $pagePermalink = FrontEnd_Helper_viewHelper::__link('zoeken');
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($pagePermalink);
        $pageAttributeId = Page::getPageAttributeByPermalink($pagePermalink);
        $pageDetail = Page::getPageFromPageAttribute($pageAttributeId);
        $this->view->pageTitle = $pageDetail->pageTitle;

        if ($pageDetail->customHeader) {
            $this->view->layout()->customHeader = "\n" . $pageDetail->customHeader;
        }

        $searchedKeywords = $this->getRequest()->getParam('searchField');
        $shopIds = "";
        $shopIds = $this->getExcludedShopIdsBySearchedKeywords($searchedKeywords);
        $shopsByShopIds = self::getshopsByExcludedShopIds($shopIds);
        $popularShops = self::getPopularStores($searchedKeywords);
        $shopsForSearchPage = self::getStoresForSearchResults($shopsByShopIds, $popularShops);
        $popularStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache('all_popularshop_list', Shop::getAllPopularStores(10), true);
        $offersBySearchedKeywords = Offer::searchOffers($this->_getAllParams(), $shopIds, 12);
        $this->view->offers = $offersBySearchedKeywords;
        $this->view->popularStores = $popularStores;
        if (!empty($offersBySearchedKeywords)) {
            $this->view->popularStores = $shopsForSearchPage; 
        }  
        
        $this->view->headTitle($pageDetail->metaTitle);
        $this->view->headMeta()->setName('description', trim($pageDetail->metaDescription));
        $this->view->searchedKeyword = ($searchedKeywords !="" || $searchedKeywords != null) ? $searchedKeywords : '';
        $this->view->facebookTitle =$pageDetail->pageTitle;
        $this->view->facebookShareUrl = HTTP_PATH_LOCALE . $pageDetail->permaLink;
        $this->view->facebookImage = FACEBOOK_IMAGE;
        $this->view->facebookDescription =  trim($pageDetail->metaDescription);
        $this->view->facebookLocale = FACEBOOK_LOCALE;
        $this->view->twitterDescription =  trim($pageDetail->metaDescription);
        $this->view->pageLogo = HTTP_PATH_LOCALE.'public/'.$pageDetail->logo->path.$pageDetail->logo->name;
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formSignupSidebarWidget', 'SignUp ');
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, '', $signUpFormSidebarWidget);
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;

    }

    public function getExcludedShopIdsBySearchedKeywords($searchedKeywords)
    {
        $excludedKeywords = ExcludedKeyword::getExcludedKeywords($searchedKeywords);
        $shopIds = '';

        if (!empty($excludedKeywords[0])) :
            if($excludedKeywords[0]['action'] == 0):
                $this->getRedirectUrlForStore($excludedKeywords[0]);
                exit();
            else:
                $shopIds = self::getShopIdsByExcludedKeywords($excludedKeywords[0]);
            endif;
        endif;

        return $shopIds;
    }

    public static function getShopIdsByExcludedKeywords($excludedKeywords)
    {
        $shopIds = array();
        foreach ($excludedKeywords['shops'] as $shops) :
            $shopIds[] = $shops['shopsofKeyword'][0]['id'];
        endforeach;
        return $shopIds;
    }

    public function getRedirectUrlForStore($excludedKeywords)
    {
        $storeUrl = $excludedKeywords['url'];
        return $this->_redirect($storeUrl);      
    }

    public static function getshopsByExcludedShopIds($shopIds)
    {
        $shopsForSearchPage = array();
        $shopsByShopIds = Shop::getShopsByShopIds($shopIds);

        foreach ($shopsByShopIds as $shopsByShopId) :
            $shopsForSearchPage[$shopsByShopId['id']] = $shopsByShopId;
        endforeach;

        return $shopsForSearchPage;
    }

    public static function getPopularStores($searchedKeywords)
    {
        $popularStores = Shop::getStoresForSearchByKeyword($searchedKeywords, 8);
        $popularStoresForSearchPage = self::getPopularStoresForSearchPage($popularStores);
        return $popularStoresForSearchPage;
    }

    public static function getPopularStoresForSearchPage($popularStores)
    {
        $popularStoresForSearchPage = array();

        foreach ($popularStores as $popularStore) :
            $popularStoresForSearchPage[$popularStore['id']] = $popularStore;
        endforeach;

        return $popularStoresForSearchPage;
    }

    public static function getStoresForSearchResults($shopsByShopIds, $popularShops)
    {        
        if (!empty($shopsByShopIds) && !empty($popularShops)) :
            $shopsForSearchPage = array_merge($shopsByShopIds, $popularShops);
        else:
            $shopsForSearchPage = !empty($popularShops) ? $popularShops : $shopsByShopIds;
        endif;

        return $shopsForSearchPage;
    }
    ##################################################################################
    ################## END REFACTORED CODE ###########################################
    ##################################################################################
}
