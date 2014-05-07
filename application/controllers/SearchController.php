<?php

class SearchController extends Zend_Controller_Action
{
    ##################################################################################
    ################## REFACTORED CODE ###############################################
    ##################################################################################
    public function indexAction()
    {
        $searchPermalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($searchPermalink);
        $pageName = LOCALE == '' ? 'zoeken' : 'search';
        $pageAttributeId = Page::getPageAttributeByPermalink($pageName);
        $pageDetail = Page::getPageFromPageAttribute($pageAttributeId);
        $this->view->pageTitle = $pageDetail->pageTitle;

        if ($pageDetail->customHeader) {
            $this->view->layout()->customHeader = "\n" . $pageDetail->customHeader;
        }

        $searchedKeyword = $this->getRequest()->getParam('searchField');
        $shopIds = "";
        $shopIds = $this->getExcludedShopIdsBySearchedKeyword($searchedKeyword);
        $shopsByShopIds = self::getshopsByExcludedShopIds($shopIds);
        $popularShops = self::getPopularStores($searchedKeyword);
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
        $this->view->searchedKeyword = ($searchedKeyword !="" || $searchedKeyword != null) ? $searchedKeyword : '';
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

    public function getExcludedShopIdsBySearchedKeyword($searchedKeyword)
    {
        $searchBarExcludedKeywords = ExcludedKeyword::getExcludedKeywords($searchedKeyword);
        $shopIds = '';

        if (!empty($searchBarExcludedKeywords)) :
            if($searchBarExcludedKeywords[0]['action'] == 0):
                $storeUrl = $searchBarExcludedKeywords[0]['url'];
                $this->_redirect($storeUrl);
                exit();
            else:
                $shopIds = array();
                foreach ($searchBarExcludedKeywords[0]['shops'] as $shops) :
                    $shopIds[] = $shops['shopsofKeyword'][0]['id'];
                endforeach;
            endif;
        endif;

        return $shopIds;
    }

    public static function getshopsByExcludedShopIds($shopIds)
    {
        $shopsForSearchPage = array();
        $shopsByShopIds = Shop::getExclusiveShops($shopIds);

        foreach ($shopsByShopIds as $shopsByShopId) :
            $shopsForSearchPage[$shopsByShopId['id']] = $shopsByShopId;
        endforeach;

        return $shopsForSearchPage;
    }

    public static function getPopularStores($searchedKeyword)
    {
        $popularStores = Shop::getStoresForSearchByKeyword($searchedKeyword, 8);
        $popularStoresForSearchPage = array();

        foreach ($popularStores as $popularStore) :
            $popularStoresForSearchPage[$popularStore['id']] = $popularStore;
        endforeach;

        return $popularStoresForSearchPage;
    }

    public static function getStoresForSearchResults($shopsByShopIds, $popularShops)
    {
        $shopsForSearchPage = '';
        
        if (!empty($shopsByShopIds) && !empty($popularShops)) :
            $shopsForSearchPage = array_merge($shopsByShopIds, $popularShops);
        else:
            if (!empty($popularShops)) :
                $shopsForSearchPage = $popularShops;
            else:
                $shopsForSearchPage = $shopsByShopIds; 
            endif;
        endif;

        return $shopsForSearchPage;
    }
    ##################################################################################
    ################## END REFACTORED CODE ###########################################
    ##################################################################################
    /**
     * override views based on modules if exists
     * @see Zend_Controller_Action::init()
     * @author Bhart
     */
    public function init()
    {
        $module   = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());

        # check module specific view exists or not
        if (file_exists (APPLICATION_PATH . '/modules/'  . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml")){
             # set module specific view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/modules/'  . $module . '/views/scripts' );
        } else{
             # set default module view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/views/scripts' );
        }
    }

/**

* This is the suggestion action and is used to retrieve four search results from database

* according to given search query

* @return $suggestions array

* @author cbhopal

* @version 1.0

*/

public function suggestionAction()
{
    # get cononical link
    $permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
    $this->view->canonical = FrontEnd_Helper_viewHelper::generatCononicalForSearch($permalink) ;


    $this->view->suggestion = Offer::searchRelatedOffers($this->_getAllParams());

    $this->view->controllerName = $this->getRequest()->getControllerName();
}

}
