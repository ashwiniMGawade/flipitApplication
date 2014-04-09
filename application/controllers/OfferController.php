<?php

class OfferController extends Zend_Controller_Action
{
    #####################################################
    ############# REFACORED CODE ########################
    #####################################################
    public function top20Action()
    {
        $page = Page::getPageFromPageAttr(37);
        if (@$page->customHeader) {
            $this->view->layout()->customHeader = "\n" . @$page->customHeader;
        }
        if (LOCALE == '') {
            $fbImage = 'logo_og.png';
        } else {
            $fbImage = 'flipit.png';
        }
        $offers= Offer::getTop20Offers();
        $params = $this->_getAllParams();
        
        $this->view->pageTitle = @$page->pageTitle;
        $this->view->headTitle(@$page->metaTitle);
        $this->view->headMeta()->setName('description', @trim($page->metaDescription));
        $this->view->fbtitle = @$page->pageTitle;
        $this->view->fbshareUrl = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('top-20');
        $this->view->fbImg = HTTP_PATH."public/images/" .$fbImage ;
        $this->view->controllerName = $params['controller'];
        $this->view->topPopularOffers = $offers;
    }
    ############################################################
    ################### END REFACOTED CODE #####################
    ############################################################
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
        if (file_exists (APPLICATION_PATH . '/modules/'  . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml")) {

            # set module specific view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/modules/'  . $module . '/views/scripts' );
        } else {

            # set default module view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/views/scripts' );
        }
    }

    /**
     * Get offer records from the database of by cache using backend key.
     *
     * @author mkaur updated by kraj
     * @version 1.0
     */
    public function indexAction()
    {
       $page = Page::getPageFromPageAttr(6);

        $this->view->pageTitle = @$page->pageTitle;
        $this->view->headTitle(@$page->metaTitle);

        if (@$page->customHeader) {
            $this->view->layout()->customHeader = "\n" . @$page->customHeader;
        }

        $this->view->headMeta()->setName('description', @trim($page->metaDescription));
        $params = $this->_getAllParams ();

        //for facebook parameters
        $this->view->fbtitle = @$page->pageTitle;
        $this->view->fbshareUrl = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('nieuw');

        if (LOCALE == '') {
            $fbImage = 'logo_og.png';
        } else {
            $fbImage = 'flipit.png';
        }

        $this->view->fbImg = HTTP_PATH."public/images/" .$fbImage ;

        $this->view->shopId = '';
        $this->view->controllerName = $params['controller'];

        $getPermLinkPopOffer = Page::getPageFromPageAttrInOfferPop(5);

        //get widget and set in caching
        $key = 'all_widget6_list';
        $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($key);

        if ($flag) {

            $widget =  FrontEnd_Helper_viewHelper::getSidebarWidgetViaId(6);
            FrontEnd_Helper_viewHelper::setInCache($key, $widget);

        } else {
            $widget = FrontEnd_Helper_viewHelper::getFromCacheByKey($key);
        }

        $this->view->widget = $widget;
        if (isset ( $params ['id'] )) :
            $this->view->shopId = $params ['id'];
               $this->view->newshoplink = HTTP_PATH_LOCALE.'offer/index/id/'.$params ['id'];
            $this->view->popularshoplink = HTTP_PATH_LOCALE.'offer/popularoffer/id/'.$params['id'];
        endif;

        $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_newoffer_list');

        if ($flag) {
            $offers = Offer::commongetnewestOffers('newest', 71, $this->view->shopId);
            FrontEnd_Helper_viewHelper::setInCache('all_newoffer_list', $offers);
        } else {
            //get from cache
            $offers = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_newoffer_list');
        }

        $paginator = FrontEnd_Helper_viewHelper::renderPagination($offers,$this->_getAllParams(),27,3);
        $this->view->paginator = $paginator;

      }

    /**
     * get offer detail from database
     *
     * @author kkumar1
     */
    public function offerdetailAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();
        $this->view->params = $params;
        $obj = new Offer();
        $offerId = $params['id'];

        $offerDetail = $obj->getOfferInfo(@$params['id']);
        $this->view->offerdetail = $offerDetail;
        $this->view->vote = @$params['vote'];
        $this->view->votepercentage = 0;
        $this->view->headTitle(@$offerDetail[0]['title']);

        if(count($offerDetail[0]['shop']['logo']) > 0):
                $img = PUBLIC_PATH_CDN.$offerDetail[0]['shop']['logo']['path'].'thum_medium_store_'. $offerDetail[0]['shop']['logo']['name'];

        else:
            $img = HTTP_PATH."public/images/NoImage/NoImage_200x100.jpg";
        endif;

        //for facebook parameters
        $this->view->fbtitle = @$offerDetail[0]['title'];
        $this->view->fbshareUrl = HTTP_PATH_LOCALE . $offerDetail[0]['shop']['permaLink'];
        $this->view->fbImg = $img;
        if (isset($params['vote']) && $params['vote']!= '0') {
            $vote = new Vote();
            $votepercentage =  $vote->doVote($params);
            $this->view->votepercentage = $votepercentage['vote'];
            $this->view->voteId = $votepercentage['voteId'];
        }

        # if code type is unique then a uniq code will be displayed otherwise displaye general code
        if ($offerDetail[0]['couponCodeType']  == 'UN') {
            $getUniqueCode = CouponCode::returnAvailableCoupon($offerId);

            if ($getUniqueCode) {
                $this->view->code = $getUniqueCode['code'] ;
            }
        } else {
               $this->view->code = $offerDetail[0]['couponCode']  ;

        }
    }
    public function feedbackAction()
    {
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
    public function sendiscountcouponAction()
    {
        $params = $this->_getAllParams ();
        $obj = new UserGeneratedOffer ();
        $obj->addOffer($params );
        die();
    }

    public function mostpopularofferAction()
    {
        ViewCount::generatePopularCode ();

        $popularOffer = FrontEnd_Helper_viewHelper::commonfrontendGetPopularCode ();

        die ();

    }

    /**
     * get coupon information
     *
     * @version 1.0
     * @author blal
     */
    public function couponinfoAction()
    {
        # get cononical link
        $permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');

        $this->view->canonical = FrontEnd_Helper_viewHelper::generatCononical($permalink) ;

        $params = $this->_getAllParams ();
        $permalink = $params ['permalink'];
        $currentDate = date ( 'Y-m-d' );
        // call to function from model to get offer details
        $cpnDetails = Offer::getCouponDetails ( $permalink );

        if(count($cpnDetails[0]['logo']) > 0):
                $img = PUBLIC_PATH_CDN.$cpnDetails[0]['shop']['logo']['path'].'thum_medium_store_'. $cpnDetails[0]['shop']['logo']['name'];

        else:
            $img = HTTP_PATH ."public/images/NoImage/NoImage_200x100.jpg";
        endif;

        //for facebook parameters
        $this->view->fbtitle = @$cpnDetails[0]['title'];
        $this->view->fbshareUrl = HTTP_PATH_LOCALE .FrontEnd_Helper_viewHelper::__link('deals') .'/'. $cpnDetails[0]['extendedUrl'];
        $this->view->fbImg = $img;

        $this->view->cpndetail = $cpnDetails;
        if (count($cpnDetails)==0) {
            $this->_redirect(HTTP_PATH_LOCALE.'error');
        }
        if(count($cpnDetails)>0):
        $shopId = $cpnDetails[0]['shopId'];
        else:
        $shopId = "";
        endif;

        // call to function from model to get popular offers
        $popularOffers = FrontEnd_Helper_viewHelper::gethomeSections("popular", 3);

        $this->view->popoffer = $popularOffers;
        $this->view->headTitle(@trim($this->view->cpndetail[0]['extendedTitle']));
        $this->view->headMeta()->appendName('description', @trim($this->view->cpndetail[0]['extendedMetaDescription']));

        if($shopId !=""):

        $relatedOffers = Offer::getrelatedOffers ( $shopId, $currentDate );
        $this->view->reloffer = $relatedOffers;
        endif;
    }
/**
 * Get Popular offer from database of by cache using admin key.
 *
 * @author mkaur updated by kraj
 * @version 1.0
 */
    public function popularofferAction()
    {
        $page = Page::getPageFromPageAttrInOffer(5);
        $this->view->pageTitle = @$page->pageTitle;

        if (@$page->customHeader) {
            $this->view->layout()->customHeader = "\n" . @$page->customHeader;
        }

        $this->view->headTitle(@$page->metaTitle);
        $this->view->headMeta()->setName('description', @trim($page->metaDescription));
        $params = $this->_getAllParams ();

        //for facebook parameters
        $this->view->fbtitle = @$page->pageTitle;
        $this->view->fbshareUrl = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('populair');

        if (LOCALE == '') {
                $fbImage = 'logo_og.png';
        } else {
                $fbImage = 'flipit.png';
        }
        $this->view->fbImg = HTTP_PATH."public/images/" .$fbImage ;

        $this->view->shopId = '';
        $shopId = '';
        $this->view->controllerName = $params['controller'];
        $getPermLinkNewOffer = Page::getPageFromPageAttrInOfferPop(6);//get only one permalink

        //get widget and set in caching
        $key = 'all_widget5_list';
        $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($key);
        if ($flag) {

            $widget =  FrontEnd_Helper_viewHelper::getSidebarWidgetViaId(5);
            FrontEnd_Helper_viewHelper::setInCache($key, $widget);
        } else {

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

    }

/**
 * Varify existence of favorite shop in the table.
 * @author mkaur
 */
    public function countshopAction()
    {
        $params = $this->_getAllParams ();
    if ($params) {
        $countShop = Offer::countFavShop($params['id']);
    }
    echo Zend_Json::encode($countShop);
    die();
    }

/**
 * Add the favorite shop in database
 * @author mkaur
 */
    public function addtofavoriteAction()
    {
        $params = $this->_getAllParams ();
        $add = Offer::addFavoriteShop($params['shopId'],$params['flag']);
        echo Zend_Json::encode($add);
        die();
    }
/**
 * Count Vote Percentage
 * @author mkaur
 */
    public function countvotesAction()
    {
        $id = $this->getRequest()->getParam('id');
        $countVote = Offer::countVotes($id);
        echo Zend_Json::encode($countVote);
        die();
    }

    public function clearcacheAction()
    {
        $cache = Zend_Registry::get('cache');
        $cache->clean();
        echo 'cache is cleared';
        exit;
    }

    public function deleteexpiredAction()
    {
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
