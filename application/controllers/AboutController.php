<?php
/**
 * this class is used for about page
 * @author Raman
 */
class AboutController extends Zend_Controller_Action {

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
	}

	public function indexAction() {
			# get cononical link
			$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
			//$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononical($permalink);

		 	$slug = 14;
			$this->pageDetail = Page::getPageFromPageAttr(14);
    		$this->view->pageTitle = @$this->pageDetail->pageTitle;
    		$this->view->headTitle(@$this->pageDetail->metaTitle);
		 	$permalink = @$this->pageDetail->permaLink;


		 	if($this->pageDetail->customHeader)
		 	{
		 		$this->view->layout()->customHeader = "\n" . $this->pageDetail->customHeader;
		 	}


    		$this->view->headMeta()->setName('description', @trim($this->pageDetail->metaDescription));

    		//for facebook parameters
    		$this->view->fbtitle = @$this->pageDetail->pageTitle;
    		$this->view->fbshareUrl = HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('redactie');

    		if(LOCALE == '' )
			{
					$fbImage = 'logo_og.png';
			}else{
					$fbImage = 'flipit.png';

			}
			$this->view->fbImg = HTTP_PATH_CDN."images/" .$fbImage ;


		/*
		 * Example to cache a result of a query ()
		*/
		// get top categories (for now i just fetch the available category list)
		$cache = Zend_Registry::get('cache');
		$pagedatakey ="all_". "pagedata".$slug ."_list";
			// no cache available, lets query.
			$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($pagedatakey);
			//key not exist in cache
			if($flag){

				//get Page data from database and store in cache
				$page = Page::getPageDetailFromPermalink($permalink);
				FrontEnd_Helper_viewHelper::setInCache($pagedatakey, $page);
				//echo  'FROM DATABASE';

			} else {
				//get from cache
				$page = FrontEnd_Helper_viewHelper::getFromCacheByKey($pagedatakey);
				//echo 'The result is comming from cache!!';
			}

			#get the sitename with locale
			$site = explode("//", HTTP_PATH_LOCALE);

			$site_name1 = rtrim( $site[1], '/');

			if(strstr($site_name1,"www")){

				$site_name = "http://".$site_name1;
			}else {
				$site_name = "http://www.".$site_name1;
			}

			$alluserkey ="all_about_pages_users_list";
		    $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($alluserkey);
			//key not exist in cache
			if($flag){

				//get  data from database and store in cache
				$allUserDetails = User::getAllUserDetail($site_name);
				FrontEnd_Helper_viewHelper::setInCache($alluserkey, $allUserDetails);
				//echo  'FROM DATABASE';

			} else {
				//get from cache
				$allUserDetails = FrontEnd_Helper_viewHelper::getFromCacheByKey($alluserkey);
				//echo 'The result is comming from cache!!';
			}
		$limit = 20;
		$this->view->page = $page;
		$paginator = FrontEnd_Helper_viewHelper::renderPagination($allUserDetails,$this->_getAllParams(),$limit,7);
		$this->view->paginator = $paginator;
		$this->view->pageDetail = $this->pageDetail;

	}



	public function profileAction() {

		# get cononical link
		$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
		$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononical($permalink) ;

		/*
		 * Example to cache a result of a query ()
		*/
		// get top categories (for now i just fetch the available category list)
		$cache = Zend_Registry::get('cache');
		$slug = "moneysaving";
		$limit = 10;

		$name = $this->getRequest ()->getParam('slug');

		$cache_key = str_replace('-', '_', $name);


		$key ="all_". "users". $cache_key ."_list";

		// no cache available, lets query..
		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($key);

		//key not exist in cache
		if($flag){

			//get Page data from database and store in cache
			$uidArray = User::getUserId($name);

			$uid = $uidArray['id'];
			FrontEnd_Helper_viewHelper::setInCache($key, $uid);
			//echo  'FROM DATABASE';

		} else {
			//get from cache
			$uid = FrontEnd_Helper_viewHelper::getFromCacheByKey($key);
			//echo 'The result is comming from cache!!';
		}

	    $key ="all_". "users".$uid ."_list";

		// no cache available, lets query..
		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($key);

		//key not exist in cache
		if($flag){

			//get Page data from database and store in cache
			$userDetails = User::getUserprofileDetail($uid);

			FrontEnd_Helper_viewHelper::setInCache($key, $userDetails);
			//echo  'FROM DATABASE';

		} else {
			//get from cache
			$userDetails = FrontEnd_Helper_viewHelper::getFromCacheByKey($key);
			//echo 'The result is comming from cache!!';
		}


		$sinceKey ="all_". "since".$uid."_list";
		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($sinceKey);
		if($flag){

			//get Page data from database and store in cache
			$sinceDays = User::findEditorSince($uid);
			FrontEnd_Helper_viewHelper::setInCache($sinceKey, $sinceDays);
			//echo  'FROM DATABASE';

		} else {
			//get from cache
			$sinceDays = FrontEnd_Helper_viewHelper::getFromCacheByKey($sinceKey);
			//echo 'The result is comming from cache!!';
		}


		$interestkey ="all_". "interesting".$uid."_list";
		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($interestkey);
		if($flag){

			//get Page data from database and store in cache
			$interestCategory = User::getUserIntcategory($uid);
			FrontEnd_Helper_viewHelper::setInCache($interestkey, $interestCategory);
			//echo  'FROM DATABASE';

		} else {
			//get from cache
			$interestCategory = FrontEnd_Helper_viewHelper::getFromCacheByKey($interestkey);
			//echo 'The result is comming from cache!!';
		}

		$popularcodekey ="all_". "popularcode".$uid ."_list";
		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($popularcodekey);
		if($flag){

			//get Page data from database and store in cache
			$popularCode = FrontEnd_Helper_viewHelper::commonfrontendGetCode('popular', 4, 0, $uid);
			FrontEnd_Helper_viewHelper::setInCache($popularcodekey, $popularCode);
			//echo  'FROM DATABASE';

		} else {
			//get from cache
			$popularCode  = FrontEnd_Helper_viewHelper::getFromCacheByKey($popularcodekey);
			//echo 'The result is comming from cache!!';
		}


		$favouriteShopkey ="all_". "favouriteshop".$uid ."_list";
		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($favouriteShopkey);
		if($flag){

			//get Page data from database and store in cache
			$favouriteShop = User::getUserFavoritesStore($uid);
			FrontEnd_Helper_viewHelper::setInCache($favouriteShopkey, $favouriteShop);
			//echo  'FROM DATABASE';

		} else {
			//get from cache
			$favouriteShop  = FrontEnd_Helper_viewHelper::getFromCacheByKey($favouriteShopkey);
			//echo 'The result is comming from cache!!';
		}


		$newcodekey ="all_". "newestcode".$uid ."_list";
		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($newcodekey);
		if($flag){

			//get Page data from database and store in cache
			$newestCode = FrontEnd_Helper_viewHelper::commonfrontendGetCode('newest', 4, 0, $uid);
			FrontEnd_Helper_viewHelper::setInCache($newcodekey, $newestCode);
			//echo  'FROM DATABASE';

		} else {
			//get from cache
			$newestCode  = FrontEnd_Helper_viewHelper::getFromCacheByKey($newcodekey);
			//echo 'The result is comming from cache!!';
		}

		$mostreadkey ="all_". "mostread".$uid ."_list";
		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($mostreadkey);
		if($flag){

			//get Page data from database and store in cache
			$mostread = MoneySaving::generateMostReadArticle($slug, 6, $uid);
			FrontEnd_Helper_viewHelper::setInCache($mostreadkey, $mostread);
			//echo  'FROM DATABASE';

		} else {
			//get from cache
			$mostread = FrontEnd_Helper_viewHelper::getFromCacheByKey($mostreadkey);
			//echo 'The result is comming from cache!!';
		}
		$this->view->headTitle(@$userDetails[0]['firstName']." ". @$userDetails[0]['lastName']);
		//for facebook parameters
		$this->view->fbtitle = @$userDetails[0]['firstName']." ". @$userDetails[0]['lastName'];
		$this->view->fbshareUrl = HTTP_PATH_LOCALE.gettext("redactie") ."/".@$userDetails[0]['slug'];

		if(LOCALE == '' )
		{
				$fbImage = 'logo_og.png';
		}else{
				$fbImage = 'flipit.png';

		}
		$this->view->fbImg = HTTP_PATH_CDN."images/" .$fbImage ;


		$this->view->userDetails = $userDetails;
		$this->view->favouriteShops = $favouriteShop;
		$this->view->interestCategory = $interestCategory;
		$this->view->noOfTotalOffers = @$noOfTotalOffers;
		$this->view->sinceDays = $sinceDays;
		$this->view->popularCode = $popularCode;
		$this->view->newestCode = $newestCode;
		$this->view->mostread = $mostread;

		if(empty($userDetails)){

			throw new Zend_Controller_Action_Exception('', 404);
		}

	}

	public function clearcacheAction(){
		$cache = Zend_Registry::get('cache');
		$cache->clean();
		echo 'cache is cleared';
		exit;
	}

	/**
	 * Find if an email id exists allready for newsletter
	 * @author Raman
	 * @version 1.0
	 */
	public function checkuserAction(){
		$u =  new Newslettersub();
		$cnt  = intval($u->checkDuplicateUser($this->_getParam('email')));

		if($cnt > 0)
		{
			echo Zend_Json::encode(false);

		} else {

			echo Zend_Json::encode(true);
		}

		die();
	}

	/**
	 * Find if an email id exists allready for newsletter
	 * @author Raman
	 * @version 1.0
	 */
	public function registerAction(){

		$u =  new Newslettersub();
		//echo $this->_getParam('email');
		//die("Rajajk");
		$cnt  = intval($u->registerUser($this->_getParam('email')));
		if($cnt > 0)
		{
			echo Zend_Json::encode(false);

		} else {

			echo Zend_Json::encode(true);
		}

		die();
	}
}

