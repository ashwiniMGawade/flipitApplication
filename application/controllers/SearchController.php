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
        $this->pageDetail = Page::getPageFromPageAttribute(36);
        $this->view->pageTitle = $this->pageDetail->pageTitle;

        if ($this->pageDetail->customHeader) {
            $this->view->layout()->customHeader = "\n" . $this->pageDetail->customHeader;
        }

        $this->view->headTitle($this->pageDetail->metaTitle);
        $this->view->headMeta()->setName('description', trim($this->pageDetail->metaDescription));
        $searchedKeyword = $this->getRequest()->getParam('searchField');
        
        if ($searchedKeyword !="" || $searchedKeyword != null) {
            $this->view->searchedKeyword = $searchedKeyword;
        }

        $shopsIds = "";
        $shopsForSearchPage = '';
        $shopsIds = $this->getExcludedShopIdsBySearchedKeyword($searchedKeyword);
        $exclusiveShops = self::getExclusiveShopsByExcludedShopIds($shopsIds);
        $popularShops = self::getPopularStores($searchedKeyword);
        $shopsForSearchPage = self::getStoresForSearchResults($exclusiveShops, $popularShops);
        $popularStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache('all_popularshop_list', Shop::getAllPopularStores(10), true);
        $offers = Offer::searchOffers($this->_getAllParams(), $shopsIds, 12);
        $this->view->offers = $offers;
        $this->view->popularStores = $popularStores;
        if (!empty($offers)) {
            $this->view->popularStores = $shopsForSearchPage; 
        }  
        
        $this->view->facebookTitle =$this->pageDetail->pageTitle;
        $this->view->facebookShareUrl = HTTP_PATH_LOCALE . $this->pageDetail->permaLink;
        $this->view->facebookImage = FACEBOOK_IMAGE;
        $this->view->facebookDescription =  trim($this->pageDetail->metaDescription);
        $this->view->facebookLocale = FACEBOOK_LOCALE;
        $this->view->twitterDescription =  trim($this->pageDetail->metaDescription);
        $this->view->pageLogo = HTTP_PATH_LOCALE.'public/'.$this->pageDetail->logo->path.$this->pageDetail->logo->name;
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formSignupSidebarWidget', 'SignUp ');
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, '', $signUpFormSidebarWidget);
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;

    }

    public function getExcludedShopIdsBySearchedKeyword($searchedKeyword)
    {
        $searchBarExcludedKeywords = ExcludedKeyword::getExcludedKeywords($searchedKeyword);
        $shopsIds = '';

        if (!empty($searchBarExcludedKeywords)) :
            if($searchBarExcludedKeywords[0]['action'] == 0):
                $storeUrl = $searchBarExcludedKeywords[0]['url'];
                $this->_redirect($storeUrl);
                exit();
            else:
                $shopsIds = array();
                foreach ($searchBarExcludedKeywords[0]['shops'] as $shops) :
                    $shopsIds[] = $shops['shopsofKeyword'][0]['id'];
                endforeach;
            endif;
        endif;

        return $shopsIds;
    }

    public static function getExclusiveShopsByExcludedShopIds($shopsIds)
    {
        $exclusiveShopsForSearchPage = array();
        $exclusiveShops = Shop::getExclusiveShops($shopsIds);

        foreach ($exclusiveShops as $exclusiveShop) :
            $exclusiveShopsForSearchPage[$exclusiveShop['id']] = $exclusiveShop;
        endforeach;

        return $exclusiveShopsForSearchPage;
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

    public static function getStoresForSearchResults($exclusiveShops, $popularShops)
    {
        $shopsForSearchPage = '';
        
        if (!empty($exclusiveShops) && !empty($popularShops)) :
            $shopsForSearchPage = array_merge($exclusiveShops, $popularShops);
        else:
            if (!empty($popularShops)) :
                $shopsForSearchPage = $popularShops;
            else:
                $shopsForSearchPage = $exclusiveShops; 
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
