<?php
class ErrorController extends Zend_Controller_Action
{
	public function errorAction()
    {

    	$domain = $_SERVER['HTTP_HOST'];
    	$this->view->controller = $this->_request->getControllerName();

        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {

            $this->view->message = 'You have reached the error page';
            return;

        }


        switch ($errors->type) {

            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:

            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:

            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

            	$page  = ltrim($this->_request->getPathInfo(), '/');
            	$page = rtrim($page, '/');
            	$permalink = explode('/page/',$page);


                if(count($permalink) > 0){

                        $page = $permalink[0];

                }
               preg_match("/[^\/]+$/", $page, $matches);

                if(intval(@$matches[0]) > 0){

                	$page = explode('/'.$matches[0],$page);

                	if($domain != "kortingscode.nl" || $domain != "www.kortingscode.nl"){

                		$page = explode('/',$page[0]);
                		if(!empty($page[1])):
                			$pageSpecial = $page[1];
                		else:
                			$pageSpecial = $page[0];
                		endif;

                	}else {

                		$pageSpecial = $page[0];

                	}

                		/**
                		 *	alwasy use last index of array
                		 * @var $page rl params
                		 * @author sp singh
                		 */

		          	$pageSpecial = end($page);

                	$pagedata = Page::getPageDetailInError($pageSpecial);

                }else{

                	//get the value of page parmalink from URL
                	if(LOCALE!='en') {

                		$front = Zend_Controller_Front::getInstance();
						$cd = $front->getControllerDirectory();

						$moduleNames = array_keys($cd);

						$permalink = ltrim($_SERVER['REQUEST_URI'], '/');
						$routeProp = explode( '/' , $page) ;
						//echo "<pre>"; print_r($routeProp); die;
						$tempLang  = rtrim( $routeProp[0] , '/') ;
						if(in_array($routeProp[0] , $moduleNames)) {
							$page = "";
							foreach($routeProp as $key => $route){
								if($key > 0){
									$page .= $route .'/';
								}
							}

						}

                	}

//                	$page = $this->getRequest()->getParam('controller').'/'.$this->getRequest()->getParam('action');
                	$pagedata = false ;
                	if(strlen(rtrim($page,'/')) > 0 )
                	{
	                	$pagedata = Page::getPageDetailInError(rtrim($page,'/'));
                	}
                }


                //echo "<pre>"; print_r($page); die;
               	$this->view->message = 'Page not found';
                if ($pagedata) {

                   if(is_array($page))
                   {
                        $page = end($page);
                   }
	                $wdgt = FrontEnd_Helper_viewHelper::getSidebarWidget($arr=array(),rtrim($page,'/'));

                	if($pagedata['pageType'] == 'default'):
                		$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononical($page) ;
                	endif;


                	if($pagedata['customHeader'])
                	{
                		$this->view->layout()->customHeader = "\n" . $pagedata['customHeader'];
                	}


                	$this->view->pageMode = true;
            		$values = $pagedata;

            		$reqiurevaule = array();
            		$page = $values;

            		$logo  = Logo::getPageLogo(@$page['logoId']);

            		$this->view->pageTitle = @$page['pageTitle'];
            		$this->view->headTitle(@$page['metaTitle']);
            		$this->view->headMeta()->setName('description', @trim($page['metaDescription']));
            		$reqiurevaule['pageid'] = $page['id'];
            		$reqiurevaule['pagetype'] = $page['pageType'];
            		$reqiurevaule['couponregular'] = $page['couponRegular'];
            		$reqiurevaule['couponeditorpick'] = $page['couponEditorPick'];
            		$reqiurevaule['couponexclusive'] = $page['couponExclusive'];
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
            		$splofferlists = Offer::getspecialofferonly($reqiurevaule);

            		$paginator = FrontEnd_Helper_viewHelper::renderPagination($splofferlists,@$matches[0], 54, 7);
					
            		$this->view->matches = $matches[0];
            		$this->view->widget = $wdgt;
            		$this->view->page = $page;
            		$this->view->paginator = $paginator;
            		$this->view->offercount = @count($splofferlists);
            		$this->view->pageLogo = @$logo[0];


            	} else {

            		$this->getResponse ()->setHttpResponseCode ( 404 );
            	}

                break;

            default:
            	//application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';
                break;
        }

        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $errors->request->getParams());
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
			echo $this->view->exception = $errors->exception;
			die();
		}

        $this->view->request   = $errors->request;

        $this->view->helper = $this->_helper ;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }
}

