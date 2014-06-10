<?php

class StoreController extends Zend_Controller_Action
{
    #################################################
    ######## REFACTORED CODE ########################
    #################################################
    public $viewHelperObject = '';
    public function addshopinfevoriteAction()
    {
        $shopId = $this->getRequest()->getParam("shopid");
        $userId = $this->getRequest()->getParam("uId");
        $shopInformation = Shop::shopAddInFavourite($shopId, $userId);
        echo Zend_Json::encode($shopInformation);
        die();
    }
    /**
    * show all details of one store
    * @version 1.0
    */
    public function storedetailAction()
    {
        $shopPermalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($shopPermalink);
        $shopRecordsLimit = 10;
        $shopParams = $this->_getAllParams();
        $currentShopId = $shopParams['id'];
        $shopId = $this->getRequest()->getParam('id');

        if ($shopId) {
            $currentDate = date('Y-m-d H:i:s');
            $ShopList = $shopId.'_list';
            $allShopDetailKey = 'all_shopdetail'.$ShopList;
            $shopInformation = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                $allShopDetailKey,
                array('function' => 'Shop::getStoreDetails', 'parameters' => array($shopId))
            );
            $allOffersInStoreKey = 'all_offerInStore'.$ShopList;
            $offers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                $allOffersInStoreKey,
                array(
                    'function' => 'FrontEnd_Helper_viewHelper::commonfrontendGetCode',
                    'parameters' => array("all", 10, $shopId, 0)
                )
            );
            $allExpiredOfferKey = 'all_expiredOfferInStore'.$ShopList;
            $expiredOffers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                $allExpiredOfferKey,
                array(
                    'function' => 'FrontEnd_Helper_viewHelper::getShopCouponCode',
                    'parameters' => array("expired", 12, $shopId)
                )
            );
            $allLatestUpdatesInStoreKey = 'all_latestupdatesInStore'.$ShopList;
            $latestShopUpdates = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                $allLatestUpdatesInStoreKey,
                array(
                    'function' => 'FrontEnd_Helper_viewHelper::getShopCouponCode',
                    'parameters' => array('latestupdates', 4, $shopId)
                )
            );
            $expiredOffersInStoreKey = 'all_msArticleInStore'.$ShopList;
            $moneySavingGuideArticle = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                'all_msArticleInStore'.$ShopList,
                array(
                    'function' => 'FrontEnd_Helper_viewHelper::generateShopMoneySavingGuideArticle',
                    'parameters' => array('moneysaving', 6, $shopId)
                )
            );

            if (sizeof($shopInformation) >0) {
            } else {
                $LocaleUrl = HTTP_PATH_LOCALE;
                $this->_helper->redirector->setCode(301);
                $this->_redirect($LocaleUrl);
            }

            if ($shopInformation[0]['showChains']) {
                $frontEndViewHelper = new FrontEnd_Helper_viewHelper();
                $shopChains = $frontEndViewHelper->sidebarChainWidget(
                    $shopInformation[0]['id'],
                    $shopInformation[0]['name'],
                    $shopInformation[0]['chainItemId']
                );
                $logDirectoryPath = APPLICATION_PATH . "/../logs/test";
                if (isset($shopChains['headLink'])) {
                    $this->view->layout()->customHeader = "\n" . $shopChains['headLink'];
                }
                if ($shopChains['hasShops'] && isset($shopChains['string'])) {
                    $this->view->shopChain = $shopChains['string'] ;
                }
            }

            $ShopImage = PUBLIC_PATH_CDN.ltrim($shopInformation[0]['logo']['path'], "/")
                .'thum_medium_store_'.$shopInformation[0]['logo']['name'];
            $this->view->shopBranding = Shop::getShopBranding($shopId);
        } else {
            $urlToRedirect = HTTP_PATH_LOCALE. 'store/index';
            $this->_redirect($urlToRedirect);
        }

        $this->view->currentStoreInformation = $shopInformation;
        $this->view->moneySavingGuideArticle = $moneySavingGuideArticle;
        $this->view->latestShopUpdates = $latestShopUpdates;
        $this->view->offers = $offers;

        if ($this->view->currentStoreInformation[0]['affliateProgram']==0 && count($this->view->offers) <=0):
            $offers = self::topStorePopularOffers($shopId, $offers);
            $this->view->topPopularOffers = $offers;
        endif;

        $this->view->expiredOffers = $expiredOffers;
        $similarShopsAndSimilarCategoriesOffers = FrontEnd_Helper_viewHelper::getShopCouponCode(
            'similarStoresAndSimilarCategoriesOffers',
            4,
            $shopId
        );
        $this->view->similarShopsAndSimilarCategoriesOffers = $similarShopsAndSimilarCategoriesOffers;
        $this->view->countPopularOffers = count(
            FrontEnd_Helper_viewHelper::commonfrontendGetCode('popular', $shopRecordsLimit, $currentShopId)
        );
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->storeImage = $ShopImage;
        $this->view->shareUrl = HTTP_PATH_LOCALE . $shopInformation[0]['permaLink'];
        $this->view->shopEditor = User::getProfileImage($shopInformation[0]['contentManagerId']);

        $customHeader = isset($shopInformation[0]['customHeader']) ? $shopInformation[0]['customHeader'] : '';
        $this->viewHelperObject->getMetaTags(
            $this,
            $shopInformation[0]['overriteTitle'],
            '',
            trim($shopInformation[0]['metaDescription']),
            $shopInformation[0]['permaLink'],
            $ShopImage,
            $customHeader
        );

        if ($shopInformation[0]['showSimliarShops']) {
            $this->view->similarShops = Shop::getSimilarShops($shopId, 11);
        }
        $frontEndViewHelper = new FrontEnd_Helper_viewHelper();
        $this->view->popularStoresList = $frontEndViewHelper->PopularShopWidget();

        $signUpFormForStorePage = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'largeSignupForm',
            'SignUp'
        );
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp '
        );
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm(
            $this,
            $signUpFormForStorePage,
            $signUpFormSidebarWidget
        );
        $this->view->form = $signUpFormForStorePage;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $this->view->pageCssClass = 'author-page';
    }

    public function topStorePopularOffers($shopId, $offers)
    {
        $voucherCacheKeyCheck =
            FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularvouchercode_list_shoppage');
        $shopCategories = Shop::returnShopCategories($shopId);
        if ($voucherCacheKeyCheck) {
            $shopCategories = Shop::returnShopCategories($shopId);
            FrontEnd_Helper_viewHelper::setInCache('all_categories_of_shoppage_'. $shopId, $shopCategories);
            $topVoucherCodes = Offer::getTopCouponCodes($shopCategories, 100);
            FrontEnd_Helper_viewHelper::setInCache('all_popularvouchercode_list_shoppage', $topVoucherCodes);
        } else {
            $shopCategories = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_categories_of_shoppage_'. $shopId);
            $topVoucherCodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularvouchercode_list_shoppage');
        }
        $offers = array();
        $storeOfferIds = array();
        foreach ($topVoucherCodes as $topVouchercodeskey => $topVoucherCode) {
            $offers[] = $topVoucherCode['offer'];
        }
        return $offers;
    }

    public function indexAction()
    {
        $permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink);
        $pageAttributes =  Page::getPageFromFilteredPageAttribute(7);
        $this->view->pageTitle = $pageAttributes['pageTitle'];
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $allStoresList = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'all_shops_list',
            array('function' => 'Shop::getallStoresForFrontEnd', 'parameters' => array('all', null)),
            true
        );
        $popularStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'all_popularshop_list',
            array('function' => 'Shop::getAllPopularStores', 'parameters' => array(10)),
            true
        );
        $storeSearchByAlphabet = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'all_searchpanle_list',
            array('function' => 'FrontEnd_Helper_viewHelper::alphabetList', 'parameters' => array()),
            true
        );

        $customHeader = isset($pageAttributes['customHeader']) ? $pageAttributes['customHeader'] : '';
        $this->viewHelperObject->getMetaTags(
            $this,
            $pageAttributes['pageTitle'],
            $pageAttributes['metaTitle'],
            trim($pageAttributes['metaDescription']),
            $pageAttributes['permaLink'],
            FACEBOOK_IMAGE,
            $customHeader
        );

        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp '
        );
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm(
            $this,
            '',
            $signUpFormSidebarWidget
        );
        
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $this->view->storesInformation = $allStoresList;
        $this->view->storeSearchByAlphabet = $storeSearchByAlphabet;
        $this->view->popularStores = $popularStores;
        $this->view->pageCssClass = 'all-stores-page';
    }

    public function howtoguideAction()
    {
        $howToGuidePermalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($howToGuidePermalink);
        $parameters = $this->_getAllParams();
        $howToGuides=Shop::getshopDetails($parameters['permalink']);
        $ShopList = $howToGuides[0]['id'].'_list';
        $allShopDetailKey = 'all_shopdetail'.$ShopList;
        $shopInformation = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            $allShopDetailKey,
            array('function' => 'Shop::getStoreDetails', 'parameters' => array($howToGuides[0]['id']))
        );

        if ($shopInformation[0]['showChains']) {
            $frontEndViewHelper = new FrontEnd_Helper_viewHelper();
            $shopChains = $frontEndViewHelper->sidebarChainWidget(
                $shopInformation[0]['id'],
                $shopInformation[0]['name'],
                $shopInformation[0]['chainItemId']
            );
            $logDirectoryPath = APPLICATION_PATH . "/../logs/test";
            if (isset($shopChains['headLink'])) {
                $this->view->layout()->customHeader = "\n" . $shopChains['headLink'];
            }
            if ($shopChains['hasShops'] && isset($shopChains['string'])) {
                $this->view->shopChain = $shopChains['string'] ;
            }
        }

        $allLatestUpdatesInStoreKey = 'all_latestupdatesInStore'.$ShopList;
        $latestShopUpdates = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            $allLatestUpdatesInStoreKey,
            array('function' => 'FrontEnd_Helper_viewHelper::getShopCouponCode', 'parameters' => array(
                'latestupdates',
                4,
                $howToGuides[0]['id'])
            )
        );
        $allOffersInStoreKey = 'all_offerInStore'.$ShopList;
        $offers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            $allOffersInStoreKey,
            array('function' => 'FrontEnd_Helper_viewHelper::commonfrontendGetCode',
                'parameters' => array('topSixOffers', 6, $howToGuides[0]['id'], 0)
            )
        );
        $offers = array_chunk($offers, 3);
        $this->view->shopEditor = User::getProfileImage($shopInformation[0]['contentManagerId']);
        $this->view->offers = $offers;
        $this->view->currentStoreInformation = $shopInformation;
        $frontEndViewHelper = new FrontEnd_Helper_viewHelper();
        $this->view->popularStoresList = $frontEndViewHelper->PopularShopWidget();
        $this->view->latestShopUpdates = $latestShopUpdates;
        $this->view->howToGuides=$howToGuides;

        $customHeader = '';
        $this->viewHelperObject->getMetaTags(
            $this,
            $howToGuides[0]['howtoTitle'],
            '',
            trim($howToGuides[0]['howtoMetaDescription']),
            $howToGuides[0]['permaLink'],
            FACEBOOK_IMAGE,
            $customHeader
        );

        $signUpFormForStorePage = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'largeSignupForm',
            'SignUp'
        );
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp'
        );
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm(
            $this,
            $signUpFormForStorePage,
            $signUpFormSidebarWidget
        );
        $this->view->form = $signUpFormForStorePage;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
    }

    public function howToUseGuideLightboxAction()
    {
        $this->_helper->layout->disableLayout();
        $howToUseGuideChapters = '';
        $ShopList = $this->getRequest()->getParam('id').'_list';
        $allShopDetailsKey = 'all_shopdetail'.$ShopList;
        $shopInformation = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            $allShopDetailsKey,
            array('function' => 'Shop::getStoreDetails', 'parameters' => array($this->getRequest()->getParam('id')))
        );

        $howToGuide = Shop::getshopDetails($shopInformation[0]['permaLink']);
        if (!empty($howToGuide[0]['howtochapter'])) :
            $howToUseGuideChapters = $howToGuide[0]['howtochapter'];
        endif;

        $this->view->shopInformation = $shopInformation;
        $this->view->howToUseGuideChapters = $howToUseGuideChapters;
    }
    ######################################################
    ############### END REFACTORED CODE ##################
    ######################################################

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

        $params = $this->_getAllParams ();


        $id = $this->getRequest()->getParam('id');
        $shopdetail = Shop::getshopStatus($id);


        if($shopdetail){

            # triiger error controller
            $request = $this->getRequest();
            $request->setControllerName('error');
            $request->setActionName('error');

        }
    $this->viewHelperObject = new FrontEnd_Helper_viewHelper();
    }

    public function searchshopbycharAction()
    {


    }
    /**
     * search top ten shops from database based on search text
     * @author kraj
     * @version 1.0
     */
    public function searchtoptenshopforusergeneratedAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $msg = $this->view->translate('No Record Found');
        $limit = 10;
        $data =Shop::commonSearchStoreForUserGenerated($srh, $limit);
        //new array for autocomplete
        $ar = array();
        if (sizeof($data) > 0) {

            foreach ($data as $d) {
                //create array according to autocomplete standard
                $ar[] = array("label"=>$d['name'],'value'=>$d['name'],'id'=>$d['id'],'permalink' => $d['permalink']);
            }

        } else {
            //if record not found
            $ar[] = array("label"=>$msg,'value'=>$msg,'id'=>0);
        }
        echo Zend_Json::encode($ar);
        die;

    }
    /**
     * search top ten shops from database based on search text
     * @author kraj
     * @version 1.0
     */
    public function searchtoptenshopAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        //echo $srh . 'key';
        $msg = $this->view->translate('No Record Found');
        $limit = 8;

        $data =Shop::commonSearchStore($srh,$limit,$msg);
        //new array for autocomplete
        $ar = array();
        if (sizeof($data) > 0) {

                foreach ($data as $d) {
                    //create array according to autocomplete standard
                    $ar[] = array("label"=>$d['name'],'value'=>$d['name'],'id'=>$d['id'],'permalink' => $d['permalink']);
                }

            } else {
                //if record not found
                $ar[] = array("label"=>$msg,'value'=>$msg,'id'=>0);
            }
        echo Zend_Json::encode($ar);
        die;

    }


  /**
   * search by char
   *
   */
  public function searchshopbykeyAction()
  {
            $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_store_list');
            //key not exist in cache
            if($flag){

                //get from database and store all Store in cache
                $chc = Shop::getallStoresForFrontEnd('all',null);
                FrontEnd_Helper_viewHelper::setInCache('all_store_list', $chc);
            } else {
                //get from cache
                $chc = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_store_list');
            }
          $NewAr = array();//Get and store from array according to search key like A-Z
          //first above get search by cat and store then apply filter by char
          if($this->getRequest()->getParam('char')) {

                    foreach ($chc as $key=>$s) {

                            if(strtoupper($key)==$this->getRequest()->getParam('char')) {
                            $NewAr[$key] = $s;
                         }
                    }
                    $chc = $NewAr;

                }
        echo Zend_Json::encode($NewAr);
        die();
    }
 public function threecodesAction()
 {
    $params=$this->_request->getParams();
    $this->view->shopId=$params["storeid"];

    $this->view->shopoffer=FrontEnd_Helper_viewHelper::getShopCouponCode("newest",3,$params["storeid"]);
    $shopdetail=Shop::getStoreDetails($params["storeid"]);

    $expiredcoupons=FrontEnd_Helper_viewHelper::getShopCouponCode("expired",'all',$params["storeid"]);
    $this->view->expiredOffers=$expiredcoupons;

    $relatedshops=FrontEnd_Helper_viewHelper::getShopCouponCode('relatedshops','all',$params["storeid"]);
    $this->view->relatedshops=$relatedshops;
    $latestupdates=FrontEnd_Helper_viewHelper::getShopCouponCode('latestupdates',10,$params["storeid"]);
    $this->view->latestupdates=$latestupdates;
    foreach($shopdetail as $v) {
    $this->view->shopdata=$v;
    $userdetail=User::getUserDetail($v["accoutManagerId"]);
    $this->view->userdetail=$userdetail;
    }

 }

 public function abcAction()
 {
 }

 /**
  * Import data from excel sheet for images
  * @author Raman
  * @version 1.0
  */

 public function importshopimageAction()
 {
        $handle = opendir(ROOT_PATH . '/Logo/Logo');
        $rootpath = ROOT_PATH . '/Logo/Logo/';
        $pathToUpload = ROOT_PATH . '/images/upload/shop/';
        $pathUpload = 'images/upload/shop/';

        //Screen Shots
        $siteHandle = opendir(ROOT_PATH . '/Logo/Screenshot');
        $rootSitePath = ROOT_PATH . '/Logo/Screenshot/';
        $pathToUploadSiteImg = ROOT_PATH . '/images/upload/screenshot/';
        $sitePathUpload = 'images/upload/screenshot/';



        $image_array =  array(); // Array for all image names
        $siteimage_array =  array(); // Array for all site image names

        // Get all the images from the folder and store in an array-$image_array
        while($file = readdir($handle)){
            if($file !== '.' && $file !== '..'){

                $image_array[] = $file;

            }
        }

        while($fileSite = readdir($siteHandle)){
            if($fileSite !== '.' && $fileSite !== '..'){

                $siteimage_array[] = $fileSite;

            }
        }

        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(ROOT_PATH."/shopsdata.xlsx");

        $data =  array();
        $worksheet = $objPHPExcel->getActiveSheet();

        foreach ($worksheet->getRowIterator() as $row) {

            $i=  0;

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                //$data[2]['A'] = $cell->getValue();
                $data[$cell->getRow()][$cell->getColumn()] = $cell->getValue();

            }

            $name =  $data[$cell->getRow()]['A'];
            $logo =  $data[$cell->getRow()]['B'];
            $websiteScreen =  $data[$cell->getRow()]['C'];
            $shop_text = $data[$cell->getRow()]['D'];
            $freeDel = $data[$cell->getRow()]['E'];
            $delCost = $data[$cell->getRow()]['F'];
            $returnPol = $data[$cell->getRow()]['G'];
            $delTime = $data[$cell->getRow()]['H'];

            //find by name if exist in database
            if(!empty($name)){

                $shopList = Doctrine_Core::getTable('Shop')->findOneBy('name', $name);

                if(!empty($shopList)){


                    if($shop_text != ""){
                        //$shopList->shopText = $shop_text;
                    }else{
                        //echo "lege desc voor ".$shopList['id']."\r\n";
                        //echo $shop_text."\n\r";
                    }
                    if($freeDel == 0 || $freeDel=='0'||$freeDel == 1||$freeDel == '1'){

                        //$shopList->freeDelivery = intval($freeDel);
                        //$shopList->deliveryCost = $delCost;

                    }else {

                        //$shopList->freeDelivery = intval($freeDel);
                        //$shopList->deliveryCost = " ";

                    }

                    if($returnPol != " "){
                        //$shopList->returnPolicy=$returnPol;
                    }

                    if($returnPol != " "){
                        //$shopList->Deliverytime= $delTime;
                    }

                    $key = array_search(strtolower($logo), array_map('strtolower', $image_array));


                    if(!empty($key)){

                        $file = $image_array[$key];
                        $newName = time() . "_" . $file;

                        $ext = BackEnd_Helper_viewHelper :: getImageExtension($file);
                        $originalpath = $rootpath.$file;

                        if($ext=='jpg' || $ext == 'png' || $ext =='JPEG'|| $ext =='PNG' || $ext =='gif'){


                            $thumbpath = $pathToUpload . "thum_large_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 200, 150, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_small_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 84, 42, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_medium_store_" . $newName;
                            BackEnd_Helper_viewHelper::resizeImageFromFolder($originalpath, 200, 100, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_medium_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 100, 50, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_big_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 234, 117, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_expired_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 100, 50, $thumbpath, $ext);

                            $shopList->logo->ext = $ext;
                            $shopList->logo->path = $pathUpload;
                            $shopList->logo->name = $newName;

                        } else{
                            echo $logo." This is an Invalid image";
                        }
                    }

                    //Website Screen shots

                    $keySite = array_search(strtolower($websiteScreen), array_map('strtolower', $siteimage_array));
                    if(!empty($keySite)){

                        $sitefile = $siteimage_array[$keySite];
                        $sitenewName = time() . "_" . $sitefile;

                        $siteExt = BackEnd_Helper_viewHelper :: getImageExtension($sitefile);
                        $originalpath = $rootSitePath.$sitefile;

                        if($siteExt=='jpg' || $siteExt == 'png' || $siteExt =='JPEG'|| $siteExt =='PNG' || $siteExt =='gif'){

                            $thumbpath = $pathToUploadSiteImg . "thum_large_" . $sitenewName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 450,0, $thumbpath, $siteExt);
                            $shopList->screenshot->ext = $siteExt;
                            $shopList->screenshot->path = $sitePathUpload;
                            $shopList->screenshot->name = $sitenewName;

                        } else{
                            echo $websiteScreen." This is an Invalid image";
                        }
                    }

                    $shopList->save();

                }
            } else {
                echo "The Shop Images Data has been imported Successfully!!";
                exit;
            }

        }
 }

 /**
  * Import data from excel sheet for shop
  *
  * @author kraj
  * @version 1.0
  */
 public function importshopsAction()
 {
        $handle = opendir(ROOT_PATH . '/Logo/Logo');
        $rootpath = ROOT_PATH . '/Logo/Logo/';
        $pathToUpload = ROOT_PATH . '/images/upload/shop/';
        $pathUpload = 'images/upload/shop/';

        //Screen Shots
        $siteHandle = opendir(ROOT_PATH . '/Logo/Screenshot');
        $rootSitePath = ROOT_PATH . '/Logo/Screenshot/';
        $pathToUploadSiteImg = ROOT_PATH . '/images/upload/screenshot/';
        $sitePathUpload = 'images/upload/screenshot/';

        $image_array =  array(); // Array for all image names
        $siteimage_array =  array(); // Array for all site image names

        // Get all the images from the folder and store in an array-$image_array
        while($file = readdir($handle)){
            if($file !== '.' && $file !== '..'){

                $image_array[] = $file;

            }
        }

        while($fileSite = readdir($siteHandle)){
            if($fileSite !== '.' && $fileSite !== '..'){

                $siteimage_array[] = $fileSite;

            }
        }

        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(ROOT_PATH."/shopsdata1.xlsx");

        $data =  array();
        $worksheet = $objPHPExcel->getActiveSheet();

        foreach ($worksheet->getRowIterator() as $row) {

            $i=  0;

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                //$data[2]['A'] = $cell->getValue();
                $data[$cell->getRow()][$cell->getColumn()] = $cell->getValue();

            }

            $name =  $data[$cell->getRow()]['A'];
            $logo =  $data[$cell->getRow()]['B'];
            $websiteScreen =  $data[$cell->getRow()]['C'];
            $shop_text = $data[$cell->getRow()]['D'];
            $freeDel = $data[$cell->getRow()]['E'];
            $delCost = $data[$cell->getRow()]['F'];
            $returnPol = $data[$cell->getRow()]['G'];
            $delTime = $data[$cell->getRow()]['H'];

            //find by name if exist in database
            if(!empty($name)){

                $shopList = Doctrine_Core::getTable('Shop')->findOneBy('name', $name);

                if(!empty($shopList)){

                    if($shop_text != ""){
                        $shopList->shopText = $shop_text;
                    }else{

                    }

                    $key = array_search(strtolower($logo), array_map('strtolower', $image_array));

                    if(!empty($key)){

                        $file = $image_array[$key];
                        $newName = time() . "_" . $file;

                        $ext = BackEnd_Helper_viewHelper :: getImageExtension($file);
                        $originalpath = $rootpath.$file;

                        if($ext=='jpg' || $ext == 'png' || $ext =='JPEG'|| $ext =='PNG' || $ext =='gif'){

                            $thumbpath = $pathToUpload . "thum_large_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 200, 150, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_small_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 84, 42, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_medium_store_" . $newName;
                            BackEnd_Helper_viewHelper::resizeImageFromFolder($originalpath, 200, 100, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_medium_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 100, 50, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_big_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 234, 117, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_expired_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 100, 50, $thumbpath, $ext);

                            $shopList->logo->ext = $ext;
                            $shopList->logo->path = $pathUpload;
                            $shopList->logo->name = $newName;

                        } else {
                            echo $logo." This is an Invalid image";
                        }
                    }

                    # Website Screen shots
                    $keySite = array_search(strtolower($websiteScreen), array_map('strtolower', $siteimage_array));

                    if(!empty($keySite)){

                        $sitefile = $siteimage_array[$keySite];
                        $sitenewName = time() . "_" . $sitefile;

                        $siteExt = BackEnd_Helper_viewHelper :: getImageExtension($sitefile);
                        $originalpath = $rootSitePath.$sitefile;

                        if($siteExt=='jpg' || $siteExt == 'png' || $siteExt =='JPEG'|| $siteExt =='PNG' || $siteExt =='gif'){

                            $thumbpath = $pathToUploadSiteImg . "thum_large_" . $sitenewName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 450,0, $thumbpath, $siteExt);
                            $shopList->screenshot->ext = $siteExt;
                            $shopList->screenshot->path = $sitePathUpload;
                            $shopList->screenshot->name = $sitenewName;

                        } else {
                            echo $websiteScreen." This is an Invalid image";
                        }
                    }
                    $shopList->save();

                } else {

                    # ADD SHOPS DATAB IN DATABASE
                    $shopList = new Shop();
                    if($shop_text != ""){
                        $shopList->shopText = $shop_text;
                    }else{
                            //echo "lege desc voor ".$shopList['id']."\r\n";
                            //echo $shop_text."\n\r";
                    }

                    $key = array_search(strtolower($logo), array_map('strtolower', $image_array));
                    if(!empty($key)){

                            $file = $image_array[$key];
                            $newName = time() . "_" . $file;

                            $ext = BackEnd_Helper_viewHelper :: getImageExtension($file);
                            $originalpath = $rootpath.$file;

                            if($ext=='jpg' || $ext == 'png' || $ext =='JPEG'|| $ext =='PNG' || $ext =='gif'){

                                $thumbpath = $pathToUpload . "thum_large_" . $newName;
                                BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 200, 150, $thumbpath, $ext);

                                $thumbpath = $pathToUpload . "thum_small_" . $newName;
                                BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 84, 42, $thumbpath, $ext);

                                $thumbpath = $pathToUpload . "thum_medium_store_" . $newName;
                                BackEnd_Helper_viewHelper::resizeImageFromFolder($originalpath, 200, 100, $thumbpath, $ext);

                                $thumbpath = $pathToUpload . "thum_medium_" . $newName;
                                BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 100, 50, $thumbpath, $ext);

                                $thumbpath = $pathToUpload . "thum_big_" . $newName;
                                BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 234, 117, $thumbpath, $ext);

                                $thumbpath = $pathToUpload . "thum_expired_" . $newName;
                                BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 100, 50, $thumbpath, $ext);

                                $shopList->logo->ext = $ext;
                                $shopList->logo->path = $pathUpload;
                                $shopList->logo->name = $newName;

                            } else{
                                echo $logo." This is an Invalid image";
                            }
                        }

                        # Website Screen shots
                        $keySite = array_search(strtolower($websiteScreen), array_map('strtolower', $siteimage_array));
                        if(!empty($keySite)){

                            $sitefile = $siteimage_array[$keySite];
                            $sitenewName = time() . "_" . $sitefile;

                            $siteExt = BackEnd_Helper_viewHelper :: getImageExtension($sitefile);
                            $originalpath = $rootSitePath.$sitefile;

                            if($siteExt=='jpg' || $siteExt == 'png' || $siteExt =='JPEG'|| $siteExt =='PNG' || $siteExt =='gif'){

                                $thumbpath = $pathToUploadSiteImg . "thum_large_" . $sitenewName;
                                BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 450,0, $thumbpath, $siteExt);
                                $shopList->screenshot->ext = $siteExt;
                                $shopList->screenshot->path = $sitePathUpload;
                                $shopList->screenshot->name = $sitenewName;

                            } else{
                                echo $websiteScreen." This is an Invalid image";
                            }
                        }
                    $shopList->save();
                }
            } else {
                echo "The Shop Images Data has been imported Successfully!!";
                exit;
            }

        }
   }

   public function addVoteAction()
   {

        $offer = $this->getRequest()->getParam('offer' , null);
        $vote = $this->getRequest()->getParam('vote' , 0);
        if(intVal($vote) > 0) {
            $vote = 'positive' ;
        } else {
            $vote = 'negative' ;
        }

        $data = Vote::addVote($offer, $vote);

        if($data) {
            self::updateVarnish($offer);
        }
        $votes = Vote::getofferVoteList($offer);

        $positive = $negative = $percentage = 0  ;
        foreach ($votes as $vote) {
            if($vote['vote']  == 'positive') {
                ++ $positive ;
            } else {
                ++ $negative ;
            }
        }

        $percentage = round(100 * $positive / ( $positive + $negative ) ). "%"  ;

        $this->_helper->json(array('flag' => $data , 'succes' => $percentage));

   }

   /**
    *  updateVarnish
    *
    *  update varnish table when an offer is created , updated and deleted
    *  @param integer $id offer id
    */
   public function updateVarnish($id)
   {
        // Add urls to refresh in Varnish
        $varnishObj = new Varnish();

        # get all the urls related to an offer
        $varnishUrls = Offer::getAllUrls( $id );

        # check $varnishUrls has atleast one url
        if(isset($varnishUrls) && count($varnishUrls) > 0) {
            foreach($varnishUrls as $value) {
                $varnishObj->addUrl( HTTP_PATH_LOCALE . $value);
            }
        }

    }

   // Returns the right favorite heart status by fetching the partial.
   public function addfavoriteviewAction()
   {
     $this->view->shopid = $this->getRequest()->getParam('shopId');
     $this->view->shopname = $this->getRequest()->getParam('shopName');
     $this->_helper->layout()->disableLayout();
   }

   public function signupAction()
   {
        $this->_helper->layout()->disableLayout();
   }
   public function signupwidgetAction()
   {
    $this->_helper->layout()->disableLayout();
   }
   public function discountcodewidgetAction()
   {
        $this->_helper->layout()->disableLayout();
   }
}
