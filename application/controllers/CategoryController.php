<?php
class CategoryController extends Zend_Controller_Action {
    
    #####################################################
    ############# REFACORED CODE ########################
    #####################################################
    public function indexAction()
    {
        $permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink) ;
        $this->pageDetail = Page::getPageFromPageAttribute(9);
        $this->view->headTitle($this->pageDetail->metaTitle);
        $this->view->headMeta()->setName('description', trim($this->pageDetail->metaDescription));

        if ($this->pageDetail->customHeader) {
            $this->view->layout()->customHeader = "\n" . $this->pageDetail->customHeader;
        }

        $categories = BackEnd_Helper_viewHelper::getRequestedDataBySetGetCache('all_category_list', Category::getCategoryIcons());
        $specialPagesList = BackEnd_Helper_viewHelper::getRequestedDataBySetGetCache('all_categoryspeciallist_list', Page::getSpecialOfferListPages());
        $this->view->categories = array_merge($categories, $specialPagesList);

        if (LOCALE == '') {
            $facebookImage = 'logo_og.png';
            $facebookLocale = '';
        } else {
            $facebookImage = 'flipit.png';
            $facebookLocale = LOCALE;
        }

        $this->view->facebookTitle = $this->pageDetail->pageTitle;
        $this->view->facebookShareUrl = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('categorieen');
        $this->view->facebookImage = HTTP_PATH."public/images/" .$facebookImage;
        $this->view->facebookDescription = trim($this->pageDetail->metaDescription);
        $this->view->facebookLocale = $facebookLocale;
        $this->view->twitterDescription = trim($this->pageDetail->metaDescription);
        $signUpFormForStorePage = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formOneHomePage', 'SignUp');
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formSignupSidebarWidget', 'SignUp ');
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, $signUpFormForStorePage, $signUpFormSidebarWidget);
        $this->view->form = $signUpFormForStorePage;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
    }
    #####################################################
    ############# END REFACORED CODE ####################
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
	 * get offer related to category
	 * @author mkaur updated by blal
	 */
	 public function showAction() {
	  
	 	$permalink = $this->getRequest ()->getParam ('permalink');
	   //get category for voucher codes
	   $category = Category::getCategoryforFrontend($permalink);
	  
	   $this->view->category = $category;
	   if(count($category )> 0 ) {
	   $this->view->editRec = $category;
	   $this->view->headTitle(@trim($category[0]['metatitle']));
       $this->view->headMeta()->setName('description', @trim($category[0]['metaDescription']));
	   
	   //get voucher codes on category id basis from database
	   $vouchers = Category::getCategoryVoucherCodes($category[0]['id'],71);
	   
	 
	   $ArNew =  array();
	   foreach ($vouchers as $v){
	   	$ArNew[$v['id']]  = $v;
	   }
	   $authorId = Category::getAuthorId();
	   $this->view->authorId = $authorId['authorId'];
	   $paginator = FrontEnd_Helper_viewHelper::renderPagination($vouchers,$this->_getAllParams(),27,3);
	   $this->view->paginator = $paginator;
	   
	   //for facebook parameters
	   $this->view->fbtitle = @$category[0]['name'];
	   $this->view->fbshareUrl = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('categorieen') . '/' .@$category[0]['permaLink'];

		if(LOCALE == '' )
		{
				$fbImage = 'logo_og.png';
		}else{
				$fbImage = 'flipit.png';
					
		}
		$this->view->fbImg = HTTP_PATH."public/images/" .$fbImage ;
	   
	   }else {
	     throw new Zend_Controller_Action_Exception('', 404);
	   	
	   }
	 }
	  

	  public function clearcacheAction(){
	  	$cache = Zend_Registry::get('cache');
	  	$cache->clean();
	  	echo 'cache is cleared';
	  	exit;
	  }
}
