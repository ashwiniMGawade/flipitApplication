<?php
class CategoryController extends Zend_Controller_Action {

    #####################################################
    ############# REFACORED CODE ########################
    #####################################################
    /**
     * Function show.
     * 
     * get offer related to category.
     */
    public function showAction()
    {
        $categoryPermalink = $this->getRequest()->getParam('permalink');
        $categoryDetail = Category::getCategoryforFrontend($categoryPermalink);
        if (count($categoryDetail)> 0) {
            $categoryVoucherCodes = Category::getCategoryVoucherCodes($categoryDetail[0]['id'], 71);
            $offersWithPagination = FrontEnd_Helper_viewHelper::renderPagination($categoryVoucherCodes, $this->_getAllParams(), 27, 3);
            $this->view->offersWithPagination = $offersWithPagination;
            $this->view->categoryDetail = $categoryDetail;
            $this->view->offersType = 'offerWithPagenation';
            $this->view->headTitle(trim($categoryDetail[0]['metatitle']));
            $this->view->headMeta()->setName('description', trim($categoryDetail[0]['metaDescription']));

            if (LOCALE == '') {
                $facebookImage = 'logo_og.png';
                $facebookLocale = LOCALE;
            } else {
                $facebookImage = 'flipit.png';
                $facebookLocale = LOCALE;
            }

            $this->view->facebookTitle = $categoryDetail[0]['name'];
            $this->view->facebookShareUrl = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('categorieen') . '/' .$categoryDetail[0]['permaLink'];
            $this->view->facebookImage = HTTP_PATH."public/images/" .$facebookImage ;
            $this->view->facebookDescription = trim($categoryDetail[0]['metaDescription']);
            $this->view->facebookLocale = $facebookLocale;
            $this->view->twitterDescription = trim($categoryDetail[0]['metaDescription']);
        } else {
            throw new Zend_Controller_Action_Exception('', 404);
        }
        $signUpFormForStorePage = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('largeSignupForm', 'SignUp');
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formSignupSidebarWidget', 'SignUp ');
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, $signUpFormForStorePage, $signUpFormSidebarWidget);
        $this->view->form = $signUpFormForStorePage;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
    }
    #####################################################
    ############ END REFACORED CODE #####################
    #####################################################
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
	
	/**
	 * get all category icons and special category list
	 * @author blal
	 */
	 public function indexAction() {
	 	
	 	$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
    	$this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink) ;
	 	
		//get category icons from database
        $this->pageDetail = Page::getPageFromPageAttribute(9);
    	$this->view->pageTitle = @$this->pageDetail->pageTitle;
    	$this->view->headTitle(@$this->pageDetail->metaTitle);
    	$this->view->headMeta()->setName('description', @trim($this->pageDetail->metaDescription));
    	$this->view->desc = @$this->pageDetail->content;
    	$this->view->pageDetail = $this->pageDetail;
	 	$cache = Zend_Registry::get('cache');
	 	$pageKey ="all_category_list";
	 	
	 	
	 	if( @$this->pageDetail->customHeader)
	 	{
	 		$this->view->layout()->customHeader = "\n" . @$this->pageDetail->customHeader;
	 	}
	 	
	 	
	 	// no cache available, lets query.
	 	$flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($pageKey);
	 	//key not exist in cache
	 	if($flag){
	 			
	 		//get Page data from database and store in cache
	 		$categoryIcons = Category::getCategoryIcons();  //function call from model to show category icons 
	 		FrontEnd_Helper_viewHelper::setInCache($pageKey, $categoryIcons);
	 		//echo  'FROM DATABASE';
	 			
	 	} else {
	 		//get from cache
	 		$categoryIcons = FrontEnd_Helper_viewHelper::getFromCacheByKey($pageKey);
	 		//echo 'The result is comming from cache!!';
	 	}
	 	
	 	$this->view->catIcons = $categoryIcons;
	 	
	 	
	 	/******************* show offer list pages****************/
	 	 
		$pageKey ="all_categoryspeciallist_list";
		$specialflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($pageKey);
		//key not exist in cache
		if($specialflag){
		    $specialList = Page::getOfferListPage();
			FrontEnd_Helper_viewHelper::setInCache($pageKey, $specialList);
			//echo  'FROM DATABASE';
		} else {
		
			$specialList = FrontEnd_Helper_viewHelper::getFromCacheByKey($pageKey);
			//echo 'The result is comming from cache!!';
		}
		
		//for facebook parameters
		$this->view->fbtitle = @$this->pageDetail->pageTitle;
		$this->view->fbshareUrl = HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('categorieen');
		if(LOCALE == '' )
		{
				$fbImage = 'logo_og.png';
		}else{
				$fbImage = 'flipit.png';
					
		}
		$this->view->fbImg = HTTP_PATH."public/images/" .$fbImage ;

		
		$this->view->specialCat = $specialList;
	}
	  public function clearcacheAction(){
	  	$cache = Zend_Registry::get('cache');
	  	$cache->clean();
	  	echo 'cache is cleared';
	  	exit;
	  }
}
