<?php
/**
 * this class is used for Money Saving Guides (Bespaar Wijzers)
 * get value from database and display on home page
 * 
 * @author Raman pending by Romy
 *
 */
class BespaarwijzerController extends Zend_Controller_Action {

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
		else {
			
			# set default module view script path
			$this->view->setScriptPath( APPLICATION_PATH . '/views/scripts' );
		}
	}

	public function indexAction() {
		
		# get cononical link
		$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
		$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononical($permalink) ;
		
        $permalink= FrontEnd_Helper_viewHelper::__link('bespaarwijzer');
        $this->pageDetail = Page::getdefaultPageProperties($permalink);
		$this->view->pageTitle = @$this->pageDetail[0]['pageTitle'];
		$this->view->permaLink = FrontEnd_Helper_viewHelper::__link('bespaarwijzer');
		$this->view->headTitle(@trim($this->pageDetail[0]['metaTitle']));
		$this->view->headMeta()->setName('description', @trim($this->pageDetail[0]['metaDescription']));
		
		if( @$this->pageDetail[0]['customHeader'])
		{
			$this->view->layout()->customHeader = "\n" . @$this->pageDetail[0]['customHeader'];
		}
		
		
		
		$limit = 10;
		/*
		 * Example to cache a result of a query ()
		*/
		// get top categories (for now i just fetch the available category list)
		$pageKey ="all_moneysavingpage".$permalink."_list";
		// no cache available, lets query.
		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($pageKey);
		//key not exist in cache
		if($flag){
			//get Page data from database and store in cache
			$page = MoneySaving::getPage($permalink);
			FrontEnd_Helper_viewHelper::setInCache($pageKey, $page);
      		//echo  'FROM DATABASE';
			
		} else {
				//get from cache
				$page = FrontEnd_Helper_viewHelper::getFromCacheByKey($pageKey);
				//echo 'The result is comming from cache!!';
				
			}
			$mspopularKey ="all_mspagepopularCodeAtTheMoment_list";
			$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($mspopularKey);
			//key not exist in cache
			if($flag){
					
				//get  data from database and store in cache
				$popularAtTheMoment = MoneySaving::getMostpopularArticles($permalink, 3);
				FrontEnd_Helper_viewHelper::setInCache($mspopularKey, $popularAtTheMoment);
				//echo  'FROM DATABASE';
					
			} else {
				//get from cache
				$popularAtTheMoment = FrontEnd_Helper_viewHelper::getFromCacheByKey($mspopularKey);
				//echo 'The result is comming from cache!!';
			}
			
			$msMRArticleKey ="all_mostreadMsArticlePage_list";
			$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($msMRArticleKey);
			//key not exist in cache
			if($flag){
					
				//get  data from database and store in cache
				$mostRead = MoneySaving::generateMostReadArticle($permalink, 6);
				FrontEnd_Helper_viewHelper::setInCache($msMRArticleKey, $mostRead);
				//echo  'FROM DATABASE';
					
			} else {
				//get from cache
				$mostRead = FrontEnd_Helper_viewHelper::getFromCacheByKey($msMRArticleKey);
				//echo 'The result is comming from cache!!';
			}
			$allMSArticleKey ="all_allMSArticle".$permalink."_list";
			$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($allMSArticleKey);
			//key not exist in cache
			if($flag){
					
				//get  data from database and store in cache
				$allMoneySavingArticles = MoneySaving::generateAllMoneySavingArticle($permalink);
				FrontEnd_Helper_viewHelper::setInCache($allMSArticleKey, $allMoneySavingArticles);
				//echo  'FROM DATABASE';
					
			} else {
				//get from cache
				$allMoneySavingArticles = FrontEnd_Helper_viewHelper::getFromCacheByKey($allMSArticleKey);
				//echo 'The result is comming from cache!!';
			}
			//die();
					
		//for facebook parameters
		$this->view->fbtitle = @$page[0]['pageTitle'];
		$this->view->fbshareUrl = FrontEnd_Helper_viewHelper::__link('bespaarwijzer');

		$this->view->fbImg = HTTP_PATH_CDN."images/bespaarwijzer_og.png";

		$this->view->popularAtTheMoment = $popularAtTheMoment;
		$this->view->mostRead = $mostRead;
		$this->view->allMoneySavingArticles = $allMoneySavingArticles;
		
		if(!empty($page)){
			$this->view->page = $page;
		}else{
			//$this->_redirect(HTTP_PATH . 'error');
			$error_404 = 'HTTP/1.1 404 Not Found';
			$this->getResponse()->setRawHeader($error_404);
		} 
	}
	
	
	public function categoryAction() {
		// get top categories (for now i just fetch the available category list)
		$perma = $this->getRequest ()->getParam ('permalink');
		$permalinkForCanonical = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
		//sdie($perma);
		$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononical($permalinkForCanonical) ;
		$this->pageDetail = Articlecategory::getArticleCategory($perma);
		//print_r($this->pageDetail); die;
		$id = $this->pageDetail[0]['id'];
		$this->view->permaLink = @$this->pageDetail[0]['permalink'];
		$this->view->headTitle(@trim($this->pageDetail[0]['metatitle']));
		$this->view->headMeta()->setName('description', @trim($this->pageDetail[0]['metadescription']));
		$cache = Zend_Registry::get('cache');
		
		$limit = 10;
		
		$key ="all_". "category".$id ."_list";
		// no cache available, lets query..
		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($key);
		//key not exist in cache
		if($flag){
		
			//get Page data from database and store in cache
			$category = Articlecategory::getArticleCategory($perma);
			FrontEnd_Helper_viewHelper::setInCache($key, $category);
			//echo  'FROM DATABASE';
		
		} else {
			//get from cache
			$category = FrontEnd_Helper_viewHelper::getFromCacheByKey($key);
			//echo 'The result is comming from cache!!';
		}
		if(empty($category)){
			
			throw new Zend_Controller_Action_Exception('', 404);
		}
		
		$mostreaddkey ="all_". "mostreadarticle".$id ."_list";
		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($mostreaddkey);
		if($flag){
				
			//get Page data from database and store in cache
			$mostreadArtOfCategory = MoneySaving::generateMostReadArticleOfcategory($id);
			FrontEnd_Helper_viewHelper::setInCache($mostreaddkey, $mostreadArtOfCategory);
			//echo  'FROM DATABASE';
				
		} else {
			//get from cache
			$mostreadArtOfCategory = FrontEnd_Helper_viewHelper::getFromCacheByKey($mostreaddkey);
			//echo 'The result is comming from cache!!';
		}
		
		$relatedcategorykey ="all_". "relatedcategory".$id ."_list";
		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($relatedcategorykey);
		if($flag){
		
			//get Page data from database and store in cache
			$relatedCategory = MoneySaving::generateRelatedCategory($id);
			FrontEnd_Helper_viewHelper::setInCache($relatedcategorykey, $relatedCategory);
			//echo  'FROM DATABASE';
		
		} else {
			//get from cache
			$relatedCategory = FrontEnd_Helper_viewHelper::getFromCacheByKey($relatedcategorykey);
			//echo 'The result is comming from cache!!';
		}
		
		$articlesofcategorykey ="all_". "articlesofcategory".$id ."_list";
		$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($articlesofcategorykey);
		if($flag){
		
			//get Page data from database and store in cache
			$allMoneySavingArticles = MoneySaving::generateAllMoneySavingArticlesOfcategory($id);
			FrontEnd_Helper_viewHelper::setInCache($articlesofcategorykey, $allMoneySavingArticles);
			//echo  'FROM DATABASE';
		
		} else {
			//get from cache
			$allMoneySavingArticles = FrontEnd_Helper_viewHelper::getFromCacheByKey($articlesofcategorykey);
			//echo 'The result is comming from cache!!';
		}
	
		//for facebook parameters
		$this->view->fbtitle = @$this->pageDetail[0]['name'];
		$this->view->fbshareUrl = HTTP_PATH_LOCALE.$this->pageDetail[0]['permalink'];;
		
		$this->view->fbImg = HTTP_PATH_CDN."images/bespaarwijzer_og.png";

		$this->view->category = $category;
		$this->view->mostReadArtOfcategory = $mostreadArtOfCategory;
	 	$this->view->allMoneySavingArticles = $allMoneySavingArticles;
		$this->view->relatedCategory = $relatedCategory;
		$this->view->pageDetail = $this->pageDetail;
	}
	
	public function clearcacheAction(){
		$cache = Zend_Registry::get('cache');
		//$cache->remove('top_categories_list');
		//$cache->remove('top_categories_output');
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
		
		$cnt  = intval($u->registerUser($this->_getParam('email')));
		if($cnt > 0)
		{
			echo Zend_Json::encode(false);
	
		} else {
	
			echo Zend_Json::encode(true);
		}
			
		die();
	}
	
	public function guidedetailAction(){
		
		# get cononical link
		$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
		$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononical($permalink) ;
		
		
		$params = $this->_getAllParams();

		$view = Articles :: getArticleDataFront($params);
		$getall = Articles :: getdynamicArticleDataFront($params);
		 
		$ArNew =  array();
		$uobj = new User();
		for($i= 0;$i<count($getall);$i++){
			
			$autherImage  = $uobj->getProfileImage($getall[$i]['authorid']);
					$img = PUBLIC_PATH_CDN.$autherImage['profileimage']['path'] .'thum_medium_'. $autherImage['profileimage']['name'];
		    
		    
			$ArNew[$i]['id']  = $getall[$i]['id'];
			$ArNew[$i]['title']  = $getall[$i]['title'];
			$ArNew[$i]['permalink']  = $getall[$i]['permalink'];
			$ArNew[$i]['chapters']  = $getall[$i]['chapters'];
			
			
			if(! $getall[$i]['thumbnail'])
			{
				$thumbnailSmall = $getall[$i]['ArtIcon'];
			} else
			{
				
				$thumbnailSmall = $getall[$i]['thumbnail'];
			}
			$ArNew[$i]['articleImageName']  = $thumbnailSmall['name'];
			$ArNew[$i]['articleImagePath']  = $thumbnailSmall['path'];
			$ArNew[$i]['authorId']  = $getall[$i]['authorid'];
			$ArNew[$i]['authorImage']  = $img;
		
		}
		

		Zend_Session::start();
		$guideDetail = new Zend_Session_Namespace('nextPrevious');
		for($key=0;$key<count($ArNew);$key++){
			 if($params['permalink'] == $ArNew[$key]['permalink']){
			  	    $despN = '';
				  	$despP = '';
					if(array_key_exists($key+1,$ArNew)){
							$imgNext = PUBLIC_PATH_CDN.$ArNew[$key+1]['articleImagePath'] .'thum_article_medium_'.  $ArNew[$key+1]['articleImageName'];
						
						$this->view->nextImage =  @$imgNext;
						$this->view->nextLink = $ArNew[$key+1]['permalink'];
						$this->view->nextTitle = $ArNew[$key+1]['title'];
						
						if(isset($ArNew[$key+1]['chapters'][0]))
						{
							if($ArNew[$key+1]['chapters'][0]['content']!=null  ||$ArNew[$key+1]['chapters'][0]['content']!=''){
						
								$despN = $ArNew[$key+1]['chapters'][0]['content'];
							}
						}
					}else{
						$firstElement = reset($ArNew);
							$imgNext = PUBLIC_PATH_CDN.$firstElement['articleImagePath'] .'thum_article_medium_'.  $firstElement['articleImageName'];
						
						$this->view->nextImage =  @$imgNext;
						$this->view->nextLink = $firstElement['permalink'];
						$this->view->nextTitle = $firstElement['title'];
						if(isset($firstElement['chapters'][0]))
						{
							if($firstElement['chapters'][0]['content']!=null  || $firstElement['chapters'][0]['content']!=''){
						
								$despN = $firstElement['chapters'][0]['content'];
							}
						}
					}
					$this->view->nextDesc = $despN;
					
					if(array_key_exists($key-1,$ArNew)){
							$imgPrev = PUBLIC_PATH_CDN. $ArNew[$key-1]['articleImagePath'] .'thum_article_medium_'.  $ArNew[$key-1]['articleImageName'];
						
						$this->view->prevImage = @$imgPrev;
						$this->view->prevLink = $ArNew[$key-1]['permalink'];
					    $this->view->prevTitle = $ArNew[$key-1]['title'];
					    
					    if(isset($ArNew[$key-1]['chapters'][0]))
					    {
					    	if($ArNew[$key-1]['chapters'][0]['content']!=null  ||$ArNew[$key-1]['chapters'][0]['content']!=''){
					    			
					    		$despP = $ArNew[$key-1]['chapters'][0]['content'];
					    	}
					    }
					 }
					else{
					     $lastElement = end($ArNew);
					     	$imgPrev = PUBLIC_PATH_CDN.$lastElement['articleImagePath'] .'thum_article_medium_'.  $lastElement['articleImageName'];
					     
					     
					     $this->view->prevImage = @$imgPrev;
					     $this->view->prevLink = $lastElement['permalink'];
					     $this->view->prevTitle = $lastElement['title'];
					     
					     if(isset($lastElement['chapters'][0]))
					     {
					     	if($lastElement['chapters'][0]['content']!=null  ||$lastElement['chapters'][0]['content']!=''){
					     
					     		$despP = $lastElement['chapters'][0]['content'];
					     	}
					     }
					}
					$this->view->prevDesc = $despP;
				}
			}
		
		if(count($view) > 0){
			$this->view->articleview = @$view[0];
			$this->view->headTitle(@trim($view[0]['metatitle']));
			$this->view->headMeta()->setName('description', @trim($view[0]['metadescription']));
		
		
		//for facebook parameters
		$this->view->fbtitle = @$view[0]['title'];
		$this->view->fbshareUrl = HTTP_PATH_LOCALE.@$view[0]['permalink'];
		 
		$this->view->fbImg = HTTP_PATH_CDN."images/bespaarwijzer_og.png";


		
		$uobj = new User();
		$this->view->udetails = $uobj->getProfileImage($this->view->articleview['authorid']);
		} else {
			  throw new Zend_Controller_Action_Exception('', 404);
		}
	}
	
	
}

