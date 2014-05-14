<?php
/**
 * this class is used for index (home ) of the site
 * get value from database and display on home page
 *
 * @author kraj
 *
 */

class IndexController extends Zend_Controller_Action
{
    #################################################################
    #################### REFACTORED CODE ##############################
    public function indexAction()
    {
        $this->view->canonical = '';
        $this->view->controllerName = $this->getRequest()->getControllerName();
        $this->view->action = $this->getRequest()->getActionName();
        $pageAttributeId = Page::getPageAttributeByPermalink($this->getRequest()->getActionName());
        $pageDetails = Page::getPageFromPageAttribute($pageAttributeId);
        
        if (!empty($pageDetails)) {
            if ($pageDetails->customHeader) {
                $this->view->layout()->customHeader = "\n" . $pageDetails->customHeader;
            }
            
            $this->view->pageTitle = ucfirst($pageDetails->pageTitle);
            $this->view->headTitle(ucfirst(trim($pageDetails->metaTitle)));
            $this->view->headMeta()->setName('description', trim($pageDetails->metaDescription));
            
            $this->view->facebookTitle = $pageDetails->metaTitle;
            $this->view->facebookShareUrl = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link($this->getRequest()->getActionName());
            $this->view->facebookImage = FACEBOOK_IMAGE;
            $this->view->facebookDescription = trim($pageDetails->metaDescription);
            $this->view->facebookLocale = FACEBOOK_LOCALE;
            $this->view->twitterDescription = trim($pageDetails->metaDescription);
        } else {
            throw new Zend_Controller_Action_Exception('', 404);
        }
        $this->view->topOffers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_popularvaouchercode_list", Offer::getTopCouponCodes(array(), 10));
        $this->view->newOffers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_homenewoffer_list", Offer::getNewestOffers('newest', 10));
        $topCategories = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_popularcategory_list", Category::getPopularCategories(10));
        $this->view->topCategories = $topCategories;
        $topCategoriesOffers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_hometocategoryoffers_list", Category::getCategoryVoucherCodes(self::getTopCategoriesIds($topCategories), 0, 'home'));
        $this->view->topCategoriesOffers =self::getCategoriesOffers($topCategoriesOffers);
        $specialListPages = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_speciallist_list", SpecialList::getSpecialPages(3));
        $this->view->specialListPages = $specialListPages;
        $this->view->specialPagesOffers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_speciallist_count", self::getSpecialListPageOffers($specialListPages));
        $this->view->moneySavingGuides = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_homemanisaving_list", Articles::getMoneySavingArticle());
        $this->view->topStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_popularshopForHomePage_list", FrontEnd_Helper_viewHelper::getStoreForFrontEnd("popular", 24));
        $this->view->seeninContents = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_homeseenin_list", SeenIn::getSeenInContent(10));
        $this->view->aboutTabs = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_about_page", About::getAboutContent(0));
    }
    public function getSpecialListPageOffers($specialListPages)
    {
        $splofferlists = '';
        foreach ($specialListPages as $specialListPage) {
            foreach ($specialListPage['page'] as $page) {
                $splofferlists[$page['permaLink']] = Offer::getSpecialPageOffers($page);
            }
        }
        return $splofferlists;
    }
    
    public static function getTopCategoriesIds($topCategories)
    {
        $categoriesIds = '';
        foreach ($topCategories as $topCategories) {
            $categoriesIds[] = $topCategories['categoryId'];
        }
        return $categoriesIds;
    }
    
    public static function getCategoriesOffers($topCategoriesOffers)
    {
        $topCategoriesOffersTest = '';
        foreach ($topCategoriesOffers as $topCategoriesOffer) {
            $topCategoriesOffersTest[$topCategoriesOffer['categoryPermalink']][] = $topCategoriesOffer['Offer'];
        }
        return $topCategoriesOffersTest;
    }
    public function clearcacheAction()
    {
        $cache = Zend_Registry::get('cache');
        $cache->clean();
        echo 'cache is cleared';
        exit;
    }
    #################################################################

    #################################################################
    #################### END REFACTOR CODE ##########################
    #################################################################
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

        $this->view->banner = Signupmaxaccount::getHomepageImages();


    }

  }
