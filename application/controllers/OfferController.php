<?php

class OfferController extends Zend_Controller_Action
{
    #####################################################
    ############# REFACORED CODE ########################
    #####################################################
    public function top20Action()
    {
        $pageName = 'top-20';
        $pageAttributeId = Page::getPageAttributeByPermalink($pageName);
        $page = Page::getPageFromPageAttribute($pageAttributeId);
        if ($page->customHeader) {
            $this->view->layout()->customHeader = "\n" . $page->customHeader;
        }
        if (LOCALE == '') {
            $facebookImage = 'logo_og.png';
            $facebookLocale = '';
        } else {
            $facebookImage = 'flipit.png';
            $facebookLocale = LOCALE;
        }
        $offers= Offer::getTop20Offers();

        $this->view->content = $page->content;
        $this->view->pageLogo = PUBLIC_PATH_CDN.ltrim($page->logo['path'].$page->logo['name']);
        $this->view->pageTitle = $page->pageTitle;
        $this->view->headTitle($page->metaTitle);
        $this->view->headMeta()->setName('description', trim($page->metaDescription));

        $this->view->facebookTitle = $page->pageTitle;
        $this->view->facebookShareUrl = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link($pageName);
        $this->view->facebookImage = HTTP_PATH."public/images/" .$facebookImage ;
        $this->view->controllerName = $this->getRequest()->getControllerName();
        $this->view->top20PopularOffers = $offers;
        $this->view->facebookDescription = trim($page->metaDescription);
        $this->view->facebookLocale = $facebookLocale;
        $this->view->twitterDescription = trim($page->metaDescription);

        // zend form for sign up news letter and validate form
        $signUpNewsLetterform = new Application_Form_SignUp();
        $this->view->form = $signUpNewsLetterform;
        FrontEnd_Helper_viewHelper::signUpNewsLetter($signUpNewsLetterform, $this);
    }

    public function extendedofferAction()
    {
        $permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $parameters = $this->_getAllParams();
        $extendedUrl = $parameters['permalink'];
        $currentDate = date('Y-m-d');
        $couponDetails = Offer::getCouponDetails($extendedUrl);
        $shopImage = PUBLIC_PATH_CDN.$couponDetails[0]['shop']['logo']['path'].'thum_medium_store_'. $couponDetails[0]['shop']['logo']['name'];

        if (count($couponDetails)==0) {
            $this->_redirect(HTTP_PATH_LOCALE.'error');
        }

        if (LOCALE == '') {
            $facebookImage = 'logo_og.png';
            $facebookLocale = '';
        } else {
            $facebookImage = 'flipit.png';
            $facebookLocale = LOCALE;
        }
        
        $currentDate = date('Y-m-d');
        $topOfferFromStore = Offer::getrelatedOffers($couponDetails[0]['shopId'], $currentDate);
        $this->view->topOfferFromStore = $topOfferFromStore;
        $this->view->couponDetails = $couponDetails;
        
        $this->view->headTitle(trim($couponDetails[0]['extendedTitle']));
        $this->view->headMeta()->appendName('description', trim($couponDetails[0]['extendedMetaDescription']));
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink);
        
        $this->view->facebookTitle = $couponDetails[0]['title'];
        $this->view->facebookShareUrl = HTTP_PATH_LOCALE .FrontEnd_Helper_viewHelper::__link('deals') .'/'. $couponDetails[0]['extendedUrl'];
        $this->view->facebookImage = $facebookImage;

        $this->view->facebookDescription = trim($couponDetails[0]['extendedMetaDescription']);
        $this->view->facebookLocale = $facebookLocale;
        $this->view->twitterDescription = trim($couponDetails[0]['extendedMetaDescription']);
    }

    /**
     * override views based on modules if exists
     * 
     */
    public function offerDetailAction()
    {
        $this->_helper->layout->disableLayout();
        $offerParameters = $this->_getAllParams();
        $this->view->params = $offerParameters;
        $offerObject = new Offer();
        
        if (isset($offerParameters['imagePath']) && !empty($offerParameters['imagePath'])) {
            $offerImagePath = $offerParameters['imagePath'];
            $this->view->offerImagePath = $offerImagePath;
        } else {
            $this->view->offerImagePath = '';
        }
        $offerId = $offerParameters['id'];
        $offerDetail = $offerObject->getOfferInfo($offerParameters['id']);
        $this->view->offerdetail = $offerDetail;
        $this->view->vote = $offerParameters['vote'];
        $this->view->votepercentage = 0;
        $this->view->headTitle($offerDetail[0]['title']);
        $shopImage = PUBLIC_PATH_CDN.$offerDetail[0]['shop']['logo']['path'].'thum_medium_store_'.
        $offerDetail[0]['shop']['logo']['name'];
        $this->view->facebookTitle = $offerDetail[0]['title'];
        $this->view->facebookShareUrl = HTTP_PATH_LOCALE . $offerDetail[0]['shop']['permaLink'];
        $this->view->facebookImage = $shopImage;
        if ($offerDetail[0]['couponCodeType']  == 'UN') {
            $getOfferUniqueCode = CouponCode::returnAvailableCoupon($offerId);
            if ($getOfferUniqueCode) {
                $this->view->couponCode = $getOfferUniqueCode['code'] ;
            }
        } else {
            $this->view->couponCode = $offerDetail[0]['couponCode']  ;
        }
    
    }
    /**
     * Get offer records from the database of by cache using backend key.
     *
     * @version 1.0
     */
    public function indexAction()
    {
        $offerPage = Page::getPageFromPageAttribute(6);
        if ($offerPage->customHeader) {
            $this->view->layout()->customHeader = "\n" . $offerPage->customHeader;
        }
        $params = $this->_getAllParams();
        
        if (LOCALE == '') {
            $facebookImage = 'logo_og.png';
            $facebookLocale = '';
        } else {
            $facebookImage = 'flipit.png';
            $facebookLocale = LOCALE;
        }
        $cacheKeyForNewsOffer =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_newoffer_list');
        if ($cacheKeyForNewsOffer) {
            $offers = Offer::getCommonNewestOffers('newest', 40, $this->view->shopId);
            FrontEnd_Helper_viewHelper::setInCache('all_newoffer_list', $offers);
        } else {
            $offers = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_newoffer_list');
        }
        $this->view->content = $offerPage->content;
        $this->view->pageTitle = $offerPage->pageTitle;
        $this->view->headTitle($offerPage->metaTitle);
        $this->view->headMeta()->setName('description', trim($offerPage->metaDescription));
 
        $this->view->facebookTitle = $offerPage->pageTitle;
        $this->view->facebookShareUrl = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('nieuw');
        $this->view->facebookImage = HTTP_PATH."public/images/" .$facebookImage ;
        $this->view->controllerName = $this->getRequest()->getControllerName();
        $this->view->top20PopularOffers = $offers;
        $this->view->facebookDescription = trim($offerPage->metaDescription);
        $this->view->facebookLocale = $facebookLocale;
        $this->view->twitterDescription = trim($offerPage->metaDescription);

        $this->view->shopId = '';
        $this->view->controllerName = $params['controller'];
        $this->view->offers = $offers;
        $this->view->offersType = 'top20';
        $this->view->shopName = 'top20';
        $paginator = FrontEnd_Helper_viewHelper::renderPagination($offers, $this->_getAllParams(), 20, 3);
        $this->view->paginator = $paginator;
        
        // zend form for sign up news letter and validate form
        $signUpNewsLetterform = new Application_Form_SignUp();
        $this->view->form = $signUpNewsLetterform;
        FrontEnd_Helper_viewHelper::signUpNewsLetter($signUpNewsLetterform, $this);
    }
    ##################################################################################
    ################## END REFACTORED CODE ###########################################
    ##################################################################################
    
    public function init() {
    
        $module   = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());
    
        # check module specific view exists or not
        if (file_exists (APPLICATION_PATH . '/modules/'  . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml")){
    
            # set module specific view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/modules/'  . $module . '/views/scripts' );
        }
        else{
    
            # set default module view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/views/scripts' );
        }
    }
    
    public function feedbackAction(){
        $params = $this->_getAllParams();
        $vote = new Vote();
        echo $votepercentage =  $vote->addfeedback($params);
        die;
    }
 /**
   * addOffer
   *
   * Add userGenerate offer
   *
   * @author kraj
   * @version 1.0
   */
    public function sendiscountcouponAction() {
        $params = $this->_getAllParams ();
        $obj = new UserGeneratedOffer ();
        $obj->addOffer($params );
        die();
    }

    public function mostpopularofferAction() {

        ViewCount::generatePopularCode ();

        $popularOffer = FrontEnd_Helper_viewHelper::commonfrontendGetPopularCode ();

        die ();

    }

/**
 * Get Popular offer from database of by cache using admin key.
 *
 * @author mkaur updated by kraj
 * @version 1.0
 */
    public function popularofferAction() {

        $page = Page::getPageFromPageAttrInOffer(5);
        $this->view->pageTitle = @$page->pageTitle;

        if(@$page->customHeader)
        {
            $this->view->layout()->customHeader = "\n" . @$page->customHeader;
        }

        $this->view->headTitle(@$page->metaTitle);
        $this->view->headMeta()->setName('description', @trim($page->metaDescription));
        $params = $this->_getAllParams ();

        //for facebook parameters
        $this->view->fbtitle = @$page->pageTitle;
        $this->view->fbshareUrl = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('populair');

        if(LOCALE == '' )
        {
                $fbImage = 'logo_og.png';
        }else{
                $fbImage = 'flipit.png';

        }
        $this->view->fbImg = HTTP_PATH."public/images/" .$fbImage ;

        //for facebook parameters
        $this->view->fbtitle = @$page->pageTitle;
        $this->view->fbshareUrl = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('populair');

        $this->view->shopId = '';
        $shopId = '';
        $this->view->controllerName = $params['controller'];
        $getPermLinkNewOffer = Page::getPageFromPageAttrInOfferPop(6);//get only one permalink

        //get widget and set in caching
        $key = 'all_widget5_list';
        $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($key);
        if($flag){

            $widget =  FrontEnd_Helper_viewHelper::getSidebarWidgetViaId(5);
            FrontEnd_Helper_viewHelper::setInCache($key, $widget);
        }else{

            $widget = FrontEnd_Helper_viewHelper::getFromCacheByKey($key);
        }
        $this->view->widget = $widget;

        if (isset ( $params ['id'] )) :

            $shopId = $params ['id'];
            $this->view->newshoplink = HTTP_PATH_LOCALE.'offer/index/id/'.$params ['id'];
            $this->view->popularshoplink = HTTP_PATH_LOCALE.'offer/popularoffer/id/'.$params ['id'];
            $this->view->shopId = $params ['id'];

        endif;
        $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_newpopularcode_list');

        if ($flag) {

            $offers = Offer::commongetpopularOffers('popular', 71, $shopId);
            FrontEnd_Helper_viewHelper::setInCache('all_newpopularcode_list', $offers);

        } else {

            $offers = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_newpopularcode_list');
        }

     $paginator = FrontEnd_Helper_viewHelper::renderPagination ($offers, $this->_getAllParams (), 27, 3);
     $this->view->paginator = $paginator;

     $paginator = FrontEnd_Helper_viewHelper::renderPagination ($offers, $this->_getAllParams (), 27, 3);
     $this->view->paginator = $paginator;

    }

/**
 * Varify existence of favorite shop in the table.
 * @author mkaur
 */
    public function countshopAction(){
        $params = $this->_getAllParams ();
    if($params){
        $countShop = Offer::countFavShop($params['id']);
    }
    echo Zend_Json::encode($countShop);
    die();
    }

/**
 * Add the favorite shop in database
 * @author mkaur
 */
    public function addtofavoriteAction(){
        $params = $this->_getAllParams ();
        $add = Offer::addFavoriteShop($params['shopId'],$params['flag']);
        echo Zend_Json::encode($add);
        die();
    }
/**
 * Count Vote Percentage
 * @author mkaur
 */
    public function countvotesAction(){
        $id = $this->getRequest()->getParam('id');
        $countVote = Offer::countVotes($id);
        echo Zend_Json::encode($countVote);
        die();
    }

    public function clearcacheAction(){
        $cache = Zend_Registry::get('cache');
        $cache->clean();
        echo 'cache is cleared';
        exit;
    }

    public function deleteexpiredAction(){

        set_time_limit(10000);
        $current_date = date('Y-m-d h:m:s',strtotime('1-1-2011'));
        $data = Doctrine_Query::create()
        ->select('o.id')
        ->from("Offer o")
        ->where('o.enddate <'."'$current_date'")
        ->fetchArray();
        foreach($data as $arr):

            Offer::deleteOffer($arr['id']);
            echo "Offer Id: ".$arr['id']."Deleted"; echo "<br>";

        endforeach;

        die('done');
    }
}
