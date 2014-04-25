<?php
 
class StoreController extends Zend_Controller_Action
{
	
	/**
	 * override views based on modules if exists
	 * @see Zend_Controller_Action::init()
	 * @author Bhart
	 */
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
		
		$params = $this->_getAllParams ();
			
		
		$id = $this->getRequest()->getParam('id');
		$shopdetail = Shop::getshopStatus($id);
		
		
		if($shopdetail){
			
			# triiger error controller
			$request = $this->getRequest();
		 	$request->setControllerName('error');
			$request->setActionName('error');
			
		}
		
	}
	
	/**
	 * get all store,popular store ,popular category and display in page
	 * @author kraj
	 * @version 1.0
	 */
    public function indexAction() {
    	
    	# get cononical link
    	$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
    	$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononical($permalink) ;
    	
    	$relatedPage =  Page::getPageFromPageAttrFiltered(7);
    	
    	$this->view->pageTitle = @$relatedPage['pageTitle'];
    	$this->view->headTitle(@$relatedPage['metaTitle']);
    	$this->view->headMeta()->setName('description', @trim($relatedPage['metaDescription']));
		
    	
    	if(@$relatedPage['customHeader'])
    	{
    		$this->view->layout()->customHeader = "\n" . @$relatedPage['customHeader'];
    	}
    	
    	
    	$this->view->controllerName = $this->getRequest()->getParam('controller');
    	$this->view->action 		= $this->getRequest()->getParam('action');
    	//get store by search pass st(storeId) cat($categoryId)
    	$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_shops_list');
    	//key not exist in cache
    	//FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_shop_list');
    	if($flag){
    		//get from database and store all Store in cache
    		$chc = Shop::getallStoreForFrontEnd('all',null);
    		FrontEnd_Helper_viewHelper::setInCache('all_shops_list', $chc);
    		//echo  'FROM DATABASE';
    	} else {
    		//get from cache
    		$chc = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_shops_list');
    		//echo 'The result is comming from cache!!';
    	}
    	
    	
    	//check in cache of popularshop
    	$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularshop_list');
    	//key not exist in cache
    	if($flag){
    	
    		//get from database and store popularStore in cache
    		$popularStore = Shop::getPopularStoreAll(10);
    		FrontEnd_Helper_viewHelper::setInCache('all_popularshop_list', $popularStore);
    		//echo  'FROM DATABASE';
    	
    	} else {
    		//get from cache
    		$popularStore = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularshop_list');
    		//echo 'The result is comming from cache!!';
    	}
    	
    	
    	//Get and store searchPanel  in cache like A-Z
    	//FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_searchpanle_list');
    	$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_searchpanle_list');
    	//key not exist in cache
    	if($flag){
    		//Get and store searchPanel  in cache like A-Z
    		$searchPanel = FrontEnd_Helper_viewHelper::storeSearchPanel();
    		FrontEnd_Helper_viewHelper::setInCache('all_searchpanle_list', $searchPanel);
    		//echo  'FROM DATABASE';
    		 
    	} else {
    		//get from cache
    		$searchPanel = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_searchpanle_list');
    		//echo 'The result is comming from cache!!';
    	}
    	
    	//check category list exist in cache or not
    	$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_category_list');
      	//key not exist in cache
      	if($flag){
      		//Get and store Category  in cache like A-Z
      		$allCat = Category::getCategoryIcons();
      		FrontEnd_Helper_viewHelper::setInCache('all_category_list', $allCat);
      		//echo  'FROM DATABASE';
      		 
      	} else {
      		 //get from cache
      		$allCat = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_category_list');
      		//echo 'The result is comming from cache!!';
      	}
		//for facebook parameters
      	
      	$this->view->fbtitle = @$relatedPage['pageTitle'];
      	$this->view->fbshareUrl = HTTP_PATH_LOCALE . $relatedPage['permaLink'];
      	
      	if(LOCALE == '' )
		{
				$fbImage = 'logo_og.png';
		}else{
				$fbImage = 'flipit.png';
					
		}
		$this->view->fbImg = HTTP_PATH."public/images/" .$fbImage ;
      	 
      	
      	$this->view->stores 		= $chc;
      	$this->view->searchPanel 	= $searchPanel;//show search panle like A-z
      	$this->view->popularstores  =$popularStore;//show popular store
      	$this->view->allCategory 	= $allCat;
   	 
      	 
    }
    public function searchshopbycharAction()
    {
    	
    	 
    }
    /**
     * search top ten shops from database based on search text
     * @author kraj
     * @version 1.0
     */
    public function searchtoptenshopforusergeneratedAction(){
    	 
    	$srh = $this->getRequest()->getParam('keyword');
    	$msg = $this->view->translate('No Record Found');
    	$limit = 10;
    	$data =Shop::commonSearchStoreForUserGenerated($srh,$limit);
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
    public function searchtoptenshopAction(){
    	
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
     * show all details of one store
     * @author kraj
     * @version 1.0
     */
  public function storedetailAction() {
  	
  	# get cononical link
  	$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
  	$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononical($permalink) ;
  	  	
  	$lim=10;
  	$params = $this->_getAllParams ();
  	$shopId = $params ['id'];
  	$id = $this->getRequest()->getParam('id');

  	if($id) {
  		
	  	 	$nowDate = date('Y-m-d H:i:s');
	  	  	# get all newest offer related currect shop
	  	  	$key = 'all_shopdetail'  . $id . '_list';
	  	  	$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($key);
	  	  	# key not exist in cache
	  	  	if($flag){
	  	  		$shopdetail=FrontEnd_Helper_viewHelper::replaceStringArray(Shop::getStoredetail($id));
	  	  		FrontEnd_Helper_viewHelper::setInCache($key, $shopdetail);
	  	  	} else {
	  	  		# get from cache
	  	  		$shopdetail = FrontEnd_Helper_viewHelper::getFromCacheByKey($key);
	  	  		# echo 'The result is comming from cache!!';
	  	  	}
	  	  	
	  	  	# if show permalink not exist then shop show 404 error
	  	  	if(sizeof($shopdetail) >0){
	  	  	}else{
	  	  		$url  =  HTTP_PATH_LOCALE;
	  	  		$this->_helper->redirector->setCode(301);
	  	  		$this->_redirect($url);
	  	  	}
				
	  	  	
  	  		# render shop chain if it is allowed to diplay on store page
	  	  	if($shopdetail[0]['showChains'])
	  	  	{
	  	  		
		  	  	$chains = FrontEnd_Helper_viewHelper::sidebarChainWidget($shopdetail[0]['id'],$shopdetail[0]['name'],$shopdetail[0]['chainItemId']);
		  	  	
		  	  	
		  	  	# log directory path
		  	  	$logDir = APPLICATION_PATH . "/../logs/test";
		  	  	FrontEnd_Helper_viewHelper::writeLog($chains , $logDir ) ;
		  	  	
		  	  	
		  	  	# show langaueg link of related shop countries
		  	  	if(isset($chains['headLink']))
		  	  	{
		  	  		$this->view->layout()->customHeader = "\n" . $chains['headLink'];
		  	  	}

		  	  	# render shop chain
		  	  	if($chains['hasShops'] && isset($chains['string'])){
		  	  		$this->view->chain = $chains['string'] ;
		  	  	}
	  	  	
	  	  	}
	  	  	
	  	  	if(@$shopdetail[0]['customHeader'])
	  	  	{
	  	  		$this->view->layout()->customHeader =  $this->view->layout()->customHeader . 
  	  									   @$shopdetail[0]['customHeader'] . "\n" ;
	  	  	}
	  	  		
	  	  	
	  	  	if(count($shopdetail[0]['logo']) > 0):
		    		$img = PUBLIC_PATH_CDN.ltrim($shopdetail[0]['logo']['path'], "/").'thum_medium_store_'. $shopdetail[0]['logo']['name'];
		    	
		    else:
		    	$img = HTTP_PATH."public/images/NoImage/NoImage_200x100.jpg";
		    endif;
		    
		    
	  	  	# get all  offer related currect shop
	  	  	$key = 'all_offerInStore'.$id.'_list';
			FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
			
	  	  	$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($key);
	  	  	# key not exist in cache 
	  	  	
	  	  	if($flag){
 			$offers =  FrontEnd_Helper_viewHelper::commonfrontendGetCode("all",'unlimited',$id,0)  ;
		    	$offers = FrontEnd_Helper_viewHelper::replaceStringArray($offers);
	  	  		FrontEnd_Helper_viewHelper::setInCache($key, $offers);
	  	  	} else {
	  	  		//get from cache
	  	  		$offers = FrontEnd_Helper_viewHelper::getFromCacheByKey($key);
	  	  		//echo 'The result is comming from cache!!';
	  	  	}
	 		//get all expired offer related currect shop
	 		$key = 'all_expiredOfferInStore'. $id .'_list';
	 		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($key);
	 		//key not exist in cache
	 		if($flag){
	 			$expiredOffer =  FrontEnd_Helper_viewHelper::replaceStringArray(FrontEnd_Helper_viewHelper::shopfrontendGetCode("expired",12,$id));
	 			FrontEnd_Helper_viewHelper::setInCache($key, $expiredOffer);
	 		} else {
	 			//get from cache
	 			$expiredOffer = FrontEnd_Helper_viewHelper::getFromCacheByKey($key);
	 		}
	 		//echo "<pre>"; print_r($expiredOffer); die;
	 		//get all expired offer related currect shop
	 		$key = 'all_latestupdatesInStore'  . $id . '_list';
	 		//FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
	 		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($key);
	 		//key not exist in cache
	 		if($flag){
	 			$latestupdates =  FrontEnd_Helper_viewHelper::replaceStringArray(FrontEnd_Helper_viewHelper::shopfrontendGetCode('latestupdates',4,$id));
	 			FrontEnd_Helper_viewHelper::setInCache($key, $latestupdates);
	 		} else {
	 			//get from cache
	 			$latestupdates = FrontEnd_Helper_viewHelper::getFromCacheByKey($key);
	 		}
	 		
	 		$slug = 'moneysaving';
	 		$limit = 6;
	 		
	 		//get all money seving guide related currect shop
	 		$key = 'all_msArticleInStore'  . $id . '_list';
	 		
	 		//FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
	 		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($key);
	 		//key not exist in cache
	 		if($flag){
	 			$msArticle =   FrontEnd_Helper_viewHelper::replaceStringArray(FrontEnd_Helper_viewHelper::generateMSArticleShop($slug, $limit, $id));
	 			FrontEnd_Helper_viewHelper::setInCache($key, $msArticle);
	 		} else {
	 			//get from cache
	 			$msArticle = FrontEnd_Helper_viewHelper::getFromCacheByKey($key);
	 		}
	 		
 		} else {
  	  		$url  =  HTTP_PATH_LOCALE. 'store/index';
  	  		$this->_redirect($url);
  	  }
  	  
  	 // check this sho is a popular store or not 
  	 $isPopular =  Shop::getPopularStore( 0 , $shopdetail[0]['id']);
  	  
  	  if(count($isPopular) >  0) {
  	  	
  	  		$this->view->isPopular = true ;
  	  		
  	  } else {
  	  	
  	  	$this->view->isPopular = false ;
  	  }
  	  
  	  // check This shop is hot at the moment or not
  	  $__onlineOffers =  Offer::getShopCharacteristics($shopdetail[0]['id'] , 5) ;
  	  
  	  if(count($__onlineOffers) >  4) {
  	  	$this->view->isHotShop = true ;
  	  } else  {
  	  	$this->view->isHotShop = false ;
  	  }
  	  // check This shop is hot at the moment or not
  	  $__hasExclusiveOffers =  Offer::getShopCharacteristics($shopdetail[0]['id'] , 7 , true , true) ;
  	  
  	  if(count($__hasExclusiveOffers) >  6 )  {
  	  	$this->view->isSuperPartner = true ;
  	  } else  {
  	  	$this->view->isSuperPartner = false ;
  	  }
  	  if( $shopdetail[0]['displayExtraProperties'] ) {
  	  	
  	    	  # if $displayExtraPropertiesWidget is true then display shop extra properteis widget otherwise hide
		  	  $displayExtraPropertiesWidget = true ;
		  	  
		  	  #check is all shop extra properties are false or not  
		  	  if(!$this->view->isHotShop && !$this->view->isSuperPartner && ! $this->view->isPopular  &&
		  	  			!$shopdetail[0]['ideal'] && ! $shopdetail[0]['qShops'] && !$shopdetail[0]['freeReturns'] &&
		  	  		! $shopdetail[0]['pickupPoints'] &&  !$shopdetail[0]['mobileShop'] &&  !$shopdetail[0]['service'])
		  	  {
		  	  	
		  	  	  $displayExtraPropertiesWidget = false ;
		  	  }
		  	  
  	  } else  {
  	  	
  	  	$displayExtraPropertiesWidget = false ;
  	  }
  	  $this->view->data = $shopdetail;

  	  $this->view->msArticle = $msArticle;
  	  $this->view->latestupdates = $latestupdates;
  	  $this->view->offers = $offers;
  	  
  	
  	  
 
  	  #  if no shop and no offer
  	  if($this->view->data[0]['affliateProgram']==0 && count($this->view->offers) <=0):
  	  	
			# fetch 5 Popular voucher offers KORTINGSCODES list display them for a particular shop
  	  	
  	 		$voucherflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularvaouchercode_list_shoppage');

  	    	$shopCtegories = Shop::returnShopCategories($id) ;
		
  	    	
  	    	$voucherflag2 =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_categories_of_shoppage_'. $id );
  	    	
			//key not exist in cache
			if($voucherflag){
					  	  	
				# GET TOP 5 POPULAR CODE
				$shopCtegories = Shop::returnShopCategories($id) ;
				FrontEnd_Helper_viewHelper::setInCache('all_categories_of_shoppage_'. $id , $shopCtegories);
						  	  	
			} else {
					  	  	
				$shopCtegories = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_categories_of_shoppage_'. $id);
			}
  	    	
  	    		
		
  	  	   	 //key not exist in cache
		  	  if($voucherflag){
		  	  	
		  	  	  # GET TOP 5 POPULAR CODE
			  	  $topVouchercodes = Offer::getTopKortingscodeForShopPage($shopCtegories);
			  	  FrontEnd_Helper_viewHelper::setInCache('all_popularvaouchercode_list_shoppage', $topVouchercodes);
			  	  	
		  	  } else {
		  	  	
		  		  $topVouchercodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularvaouchercode_list_shoppage');
		  	  }
  	  
  	   		# traverse  $topVouchercodes array to make required array of offers
	  	  $offers = array();
		  	  
		 $offerIDs = array();
		  	  
	  	  foreach ($topVouchercodes as $key => $value) {
	  	  
	  	  
	  	  		 
	  	 	 $offers[] = 	 $value['offer'] ;
	  	 	 
	  	 	 $offerIDs[] = $value['offer']['id'];
	  	 	 
	  	 	 
	  	  
	  	  }
	  	  
	  	 
	  	  # if top korting who has same category as like its shop is less than 10 then display rests of the kortings   
	  	 if(count($offers) < 5 )
	  	 {
	  	 	# the limit of popular oces 
	  	 	$additionalShop = 5 - count($offers) ;
	  	 	
	  	 	# GET TOP 5 POPULAR CODE
	  	 	$additionalTopVouchercodes = Offer::getAdditionalTopKortingscodeForShopPage($shopCtegories,$offerIDs,$additionalShop);
 
	  	 	
	  	 	# GET TOP 5 POPULAR CODE
	  	 	$totalPopularCOdes = array_merge($topVouchercodes,$additionalTopVouchercodes) ;
	  	 	FrontEnd_Helper_viewHelper::setInCache('all_popularvaouchercode_list_shoppage', $totalPopularCOdes);
	  	 	
	  	 	
	  	 	foreach ($additionalTopVouchercodes as $key => $value) {
	  	 	
	  	 		$offers[] = 	 $value['offer'] ;
	  	 	}
	  	 	
	  	 }
 
  	  $this->view->topPopularOffers = $offers;
  	  
	  endif;
  	  		
  	  		
  	  $this->view->expiredOffers = $expiredOffer;
  	  
  	  if ($shopdetail[0]['affliateProgram'] == 0) {
  	  		$limit = 10;
  	  } else {
  	  		$limit = 4;
  	  }
  	  $relatedStoteByCat =  FrontEnd_Helper_viewHelper::replaceStringArray(FrontEnd_Helper_viewHelper::shopfrontendGetCode('relatedshopsbycat',$limit,$id));
 
  	  $this->view->relatedshops = $relatedStoteByCat ;
  	  $this->view->relatedshops8 = $relatedStoteByCat;
 
  	  $this->view->cntPopular = count(FrontEnd_Helper_viewHelper::commonfrontendGetCode ('popular',$lim, $shopId));
  	  
  	  $this->view->controllerName = $this->getRequest()->getParam('controller');
  	  $this->view->img = $img;//image for the shop
  	  $shareUrl = HTTP_PATH_LOCALE . $shopdetail[0]['permaLink'];
  	  $this->view->shareUrl = $shareUrl;//share to facebook
  	  # get editor picture from second server
  	  $this->view->profileImage =  User::getProfileImage($shopdetail[0]['contentManagerId']);
  	  
  	  $this->view->displayExtraPropertiesWidget = $displayExtraPropertiesWidget ;
  	  $this->view->headTitle(@$shopdetail[0]['overriteTitle']);
  	  $this->view->headMeta()->setName('description', @trim($shopdetail[0]['metaDescription']));
  	  
  	  # for facebook parameters
  	  $this->view->fbtitle = @$shopdetail[0]['overriteTitle'];
  	  $this->view->fbshareUrl = HTTP_PATH_LOCALE . $shopdetail[0]['permaLink'];
  	  $this->view->fbImg = $img;  	 

  	  
  	  # check display similar shops is enabled or not
  	  if($shopdetail[0]['showSimliarShops'])
  	  {
	  	 $this->view->similarShops =  Shop::getSimilarShop($id,11);
  	  }
  	  

  
  }
  /**
   * search by char
   * 
   */
  public function searchshopbykeyAction() {
  	
		  	$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_store_list');
		  	//key not exist in cache
		  	if($flag){
		  	
		  		//get from database and store all Store in cache
		  		$chc = Shop::getallStoreForFrontEnd('all',null);
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
 public function threecodesAction(){
 	$params=$this->_request->getParams();
 	$this->view->shopId=$params["storeid"];
 	
 	$this->view->shopoffer=FrontEnd_Helper_viewHelper::shopfrontendGetCode("newest",3,$params["storeid"]);
 	$shopdetail=Shop::getStoredetail($params["storeid"]);
 	
 	$expiredcoupons=FrontEnd_Helper_viewHelper::shopfrontendGetCode("expired",'all',$params["storeid"]);
 	$this->view->expiredOffers=$expiredcoupons;
 	
 	$relatedshops=FrontEnd_Helper_viewHelper::shopfrontendGetCode('relatedshops','all',$params["storeid"]);
 	$this->view->relatedshops=$relatedshops;
 	$latestupdates=FrontEnd_Helper_viewHelper::shopfrontendGetCode('latestupdates',10,$params["storeid"]);
 	$this->view->latestupdates=$latestupdates;
 	foreach($shopdetail as $v)
 	{
 	$this->view->shopdata=$v;
 	$userdetail=User::getUserDetail($v["accoutManagerId"]);
 	$this->view->userdetail=$userdetail;
 	}
 	
 }
  /**
  * get shop details and popular offers of a shop
  * @version 1.0
  * @author blal
  */
    public function howtoguideAction(){
    	
    	# get cononical link
    	$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
    	$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononical($permalink) ;
    	
    	$lim=10;
    	$params = $this->_getAllParams ();
    	$shopId = $params ['permalink'];
    	$cntPopular=count(FrontEnd_Helper_viewHelper::commonfrontendGetCode ('allcouponshowtoguide',$lim, $shopId));
    	$params = $this->_getAllParams ();
    	$shopId = $params ['permalink'];
    	$params=$this->getRequest()->getParam('permalink');
    	if($cntPopular==1){
    		
    		$this->view->showguide="howtoguide1";
    		$limit=1;
    		$popularoffers = FrontEnd_Helper_viewHelper::commonfrontendGetCode ('allcouponshowtoguide',$limit, $params);
    		
    		$this->view->coupons=$popularoffers;
    		
    		$getShopDetail=Shop::getshopDetails($params); 
    		
    		$this->view->headTitle(@$getShopDetail[0]['howtoMetaTitle']);
    		$this->view->headMeta()->setName('description', @trim($getShopDetail[0]['howtoMetaDescription']));
    		
    		
    		foreach($getShopDetail as $v){
    			$this->view->shopdetail=$v;
    		}
    		//get editor picture from second server
    		$this->view->profileImage =  User::getProfileImage($getShopDetail[0]['contentManagerId']);
    	}
    	else if($cntPopular==2){
    		
    		$this->view->showguide="howtoguide2";
    		$limit=2;
    		$popularoffers = FrontEnd_Helper_viewHelper::commonfrontendGetCode ('allcouponshowtoguide',$limit, $params);
    		$this->view->coupons=$popularoffers;
    		$getShopDetail=Shop::getshopDetails($params);
    		$this->view->headTitle(@$getShopDetail[0]['howtoMetaTitle']);
    		$this->view->headMeta()->setName('description', @trim($getShopDetail[0]['howtoMetaDescription']));
    		
    		foreach($getShopDetail as $v){
    			$this->view->shopdetail=$v;
    		}
    		//get editor picture from second server
    		$this->view->profileImage =  User::getProfileImage($getShopDetail[0]['contentManagerId']);
    	}
    	else if($cntPopular>2){
    		$this->view->showguide="howtoguide";
    		$getShopDetail = Shop::getshopDetails($shopId);
    	
    		foreach($getShopDetail as $v){
    			
    				$this->view->shopdetail=$v;
    			
    		}
    		$this->view->headTitle(@$getShopDetail[0]['howtoMetaTitle']);
    		$this->view->headMeta()->setName('description', @trim($getShopDetail[0]['howtoMetaDescription']));
    		
    		// call to common function from Helper to get popular offers of a shop on id basis
            $res = FrontEnd_Helper_viewHelper::commonfrontendGetCode ( 'allcouponshowtoguide',3, $shopId);
            foreach ($res as $key => $r){
            	if($key < 4){
            		$newResult[$key] = $r;
            		$newResult[$key]['marginCounter'] = 4;
            	} 
            }
        	$this->view->coupons = $newResult;

    	}
    	else{
    		 
    		$this->view->showguide="howtoguideno";
    		$params=$this->getRequest()->getParam('permalink');
    		$getShopDetail=Shop::getshopDetails($params);
    		
    		$this->view->headTitle(@$getShopDetail[0]['howtoMetaTitle']);
    		$this->view->headMeta()->setName('description', @trim($getShopDetail[0]['howtoMetaDescription']));
    		
    		foreach($getShopDetail as $v){
    			$this->view->shopdetail=$v;
    		}
    		//get editor picture from second server
    		$this->view->profileImage =  User::getProfileImage($getShopDetail[0]['contentManagerId']);
    	}
    	
    	if($getShopDetail[0]['howToUse'] == 0){
    	
    		$this->_redirect(HTTP_PATH_LOCALE.'error');
    		exit();
    	}
   	
    	if(count($getShopDetail[0]['logo']) > 0):
    			$img = PUBLIC_PATH_CDN.ltrim($getShopDetail[0]['logo']['path'],"/").'thum_medium_store_'. $getShopDetail[0]['logo']['name'];
    		
    	else:
    		$img = HTTP_PATH."public/images/NoImage/NoImage_200x100.jpg";
    	endif;
    	
    	//for facebook parameters
    	$this->view->fbtitle = @$getShopDetail[0]['howtoTitle'];
    	$this->view->fbshareUrl = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link("how-to")."/".$getShopDetail[0]['permaLink'];
    	$this->view->fbImg = $img;
    }
	
 public function addshopinfevoriteAction() {
 	
 	$shopid=$this->getRequest()->getParam("shopid");
 	$userid=$this->getRequest()->getParam("uId");
 	$data = Shop::shopAddInFavoriteInShopDetails($userid,$shopid);
 	echo Zend_Json::encode($data);
 	die();

 }
 public function abcAction() {
 
 }
 
 /**
  * Import data from excel sheet for images 
  * @author Raman
  * @version 1.0
  */
 
 public function importshopimageAction(){
 	 
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
		
						}
						else{
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
		
						}
						else{
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
 public function importshopsAction(){
 	
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
					
							}
							else{
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
					
							}
							else{
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
   		if(intVal($vote) > 0)
   		{
   			$vote = 'positive' ;
   		} else
   		{
   			$vote = 'negative' ;
   		}

   		$data = Vote::addVote($offer, $vote);
   		
   		if($data)
   		{
   			self::updateVarnish($offer);   
   		}
   		$votes = Vote::getofferVoteList($offer);
   		
   		$positive = $negative = $percentage = 0  ;
   		foreach ($votes as $vote)
   		{
   			if($vote['vote']  == 'positive')
   			{
   				++ $positive ;
   			} else
   			{
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
	   	if(isset($varnishUrls) && count($varnishUrls) > 0)
	   	{
		   	foreach($varnishUrls as $value)
		   	{
			   	$varnishObj->addUrl( HTTP_PATH_LOCALE . $value);
		   	}
	   	}
   
   	}
   
   // Returns the right favorite heart status by fetching the partial.
   public function addfavoriteviewAction(){
   	 $this->view->shopid = $this->getRequest()->getParam('shopId'); 
   	 $this->view->shopname = $this->getRequest()->getParam('shopName');
   	 $this->_helper->layout()->disableLayout();
   }
   
   public function signupAction(){
   		$this->_helper->layout()->disableLayout();
   }
   
   public function discountcodewidgetAction(){
   		$this->_helper->layout()->disableLayout();
   }
}

