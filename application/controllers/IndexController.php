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

    public function indexAction()
    {
        # get cononical link
        $this->view->canonical = '';

        //$this->view->abcd = 'aaaaaaa';
        $this->view->controllerName = 'index';
        $pageId = $this->getRequest ()->getParam ('attachedpage');
        $get = Page::getPageFromPageAttribute(4);



        if(!empty($get)){
            $this->view->pageTitle = @ucfirst($get->pageTitle);
            $this->view->headTitle(@ucfirst(trim($get->metaTitle)));
            $this->view->headMeta()->setName('description', @trim($get->metaDescription));

            if($get->customHeader) {
                $this->view->layout()->customHeader = "\n" . $get->customHeader;
            }
            //for facebook parameters
            $this->view->fbtitle = @ucfirst(trim($get->metaTitle));
            $this->view->fbshareUrl = HTTP_PATH_LOCALE;

            if(LOCALE == '' ) {
                $fbImage = 'logo_og.png';
            }else{
                $fbImage = 'flipit.png';

            }
            $this->view->fbImg = HTTP_PATH."public/images/" .$fbImage ;

        }

        $this->view->action = $this->getRequest()->getParam('action');

        /**********fetch Popular voucher offers KORTINGSCODES list ***************/
        $voucherflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularvaouchercode_list');
        //key not exist in cache
        if($voucherflag){
            $topVouchercodes = FrontEnd_Helper_viewHelper::gethomeSections("popular", 10);
            FrontEnd_Helper_viewHelper::setInCache('all_popularvaouchercode_list', $topVouchercodes);
        } else {

            $topVouchercodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularvaouchercode_list');
        }
        $this->view->topCode = $topVouchercodes;

        /***************** fetch Newest Offers list **********************/
        $newestflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_homenewoffer_list');
        //key not exist in cache
        if($newestflag){
            $newestCodes = FrontEnd_Helper_viewHelper::gethomeSections("newest", 10);
            FrontEnd_Helper_viewHelper::setInCache('all_homenewoffer_list', $newestCodes);
        } else {
            $newestCodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_homenewoffer_list');
        }


        $this->view->NewestOffer = $newestCodes;

        /***************** fetch category list  **********************/
        $categoryflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularcategory_list');
        //key not exist in cache
        if($categoryflag){
            $topCategories = FrontEnd_Helper_viewHelper::gethomeSections("category", 10);
            FrontEnd_Helper_viewHelper::setInCache('all_popularcategory_list', $topCategories);
        } else {
            $topCategories = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularcategory_list');
        }


        $this->view->topCategories = $topCategories;

        /*****************  fetch Special list offer with shop information **********************/
        $specialflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_speciallist_list');
        $specialOfferCount =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_speciallist_count');
        //key not exist in cache
        $splofferlists = '';
        if($specialflag && $specialOfferCount){

            $specialList = FrontEnd_Helper_viewHelper::gethomeSections("specialList", 3);
            foreach($specialList as $sl){

                foreach ($sl['page'] as $page) {
                    $reqiurevaule['pageid'] = $page['id'];
                    $reqiurevaule['pagetype'] = $page['pageType'];
                    $reqiurevaule['couponregular'] = $page['couponRegular'];
                    $reqiurevaule['couponeditorpick'] = $page['couponEditorPick'];
                    $reqiurevaule['couponexclusive'] = $page['couponExclusive'];
                    $reqiurevaule['saleregular'] = $page['saleRegular'];
                    $reqiurevaule['saleeditorpick'] = $page['saleEditorPick'];
                    $reqiurevaule['saleexclusive'] = $page['saleExclusive'];
                    $reqiurevaule['printableregular'] = $page['printableRegular'];
                    $reqiurevaule['printableeditorpick'] = $page['printableEditorPick'];
                    $reqiurevaule['printableexclusive'] = $page['printableExclusive'];
                    $reqiurevaule['showpage'] = $page['showPage'];
                    $reqiurevaule['maxOffers'] = $page['maxOffers'];
                    $reqiurevaule['oderOffers'] = $page['oderOffers'];
                    $reqiurevaule['timeType'] = $page['timeType'];
                    $reqiurevaule['enableTimeConst'] = $page['enableTimeConstraint'];
                    $reqiurevaule['timenumOfDays'] = $page['timenumberOfDays'];
                    $reqiurevaule['enableWordConstraint'] = $page['enableWordConstraint'];
                    $reqiurevaule['wordTitle'] = $page['wordTitle'];
                    $reqiurevaule['awardConst'] = $page['awardConstratint'];
                    $reqiurevaule['enableclickconst'] = $page['enableClickConstraint'];
                    $reqiurevaule['numberofclicks'] = $page['numberOfClicks'];
                    $reqiurevaule['publishdate'] = $page['publishDate'];
                    $reqiurevaule['awardConstratint'] = $page['awardConstratint'];
                    $reqiurevaule['awardType'] = $page['awardType'];
                    $splofferlists[] = count (Offer::getSpecialPageOffers($reqiurevaule) );
                }
            }
            FrontEnd_Helper_viewHelper::setInCache('all_speciallist_list',$specialList);
            FrontEnd_Helper_viewHelper::setInCache('all_speciallist_count',$splofferlists);
        } else {

            $specialList = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_speciallist_list');
            $splofferlists = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_speciallist_count');
        }

        $this->view->specialList = $specialList;
        $this->view->splofferlists = $splofferlists;

        /*****************  fetch money saving article list **********************/
        $moneyflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_homemanisaving_list');
        //key not exist in cache
        if($moneyflag){
            $moneySaving = FrontEnd_Helper_viewHelper::gethomeSections("moneySaving", 2);
            FrontEnd_Helper_viewHelper::setInCache('all_homemanisaving_list', $moneySaving);
        } else {
            $moneySaving = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_homemanisaving_list');
        }



        $this->view->moneySaving = $moneySaving;

        /*****************  fetch Popular Shops for homepage list **********************/
        $topstoreflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularshopForHomePage_list');
        //key not exist in cache
        if($topstoreflag){
            $topPopularStores = FrontEnd_Helper_viewHelper::getStoreForFrontEnd("popular", 16);
            FrontEnd_Helper_viewHelper::setInCache('all_popularshopForHomePage_list', $topPopularStores);
        } else {
            $topPopularStores = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularshopForHomePage_list');
        }
        $this->view->topStores = $topPopularStores;

        /*****************  fetch Recent Shops for homepage list **********************/
        $recentstoreflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_shop_list');
        //key not exist in cache
        if($recentstoreflag){

            $recentStoreList = FrontEnd_Helper_viewHelper::getStoreForFrontEnd("recent", 4);
            FrontEnd_Helper_viewHelper::setInCache('all_shop_list', $recentStoreList);
        } else {
            $recentStoreList = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_shop_list');
        }
        $this->view->recentStore = $recentStoreList;

        /***************** fetch As seen IN  for homepage list **********************/
        $seeninflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_homeseenin_list');
        //key not exist in cache
        if($seeninflag){

            $seenin = FrontEnd_Helper_viewHelper::gethomeSections("asseenin", 10);
            FrontEnd_Helper_viewHelper::setInCache('all_homeseenin_list', $seenin);

        } else {

            $seenin = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_homeseenin_list');
        }

        $this->view->seenin = $seenin;

        /***************** fetch About tabs for homepage section **********************/
        $seeninflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_about_page');
        //key not exist in cache
        if($seeninflag){

            $aboutTabs = FrontEnd_Helper_viewHelper::gethomeSections("about", 0);
            FrontEnd_Helper_viewHelper::setInCache('all_about_page', $aboutTabs);

        } else {

            $aboutTabs = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_about_page');
            //The result is comming from cache!!
        }
        $this->view->aboutTabs = $aboutTabs;

    }

    public function clearcacheAction()
    {
        $cache = Zend_Registry::get('cache');
        $cache->clean();
        echo 'cache is cleared';
        exit;
    }

  }
