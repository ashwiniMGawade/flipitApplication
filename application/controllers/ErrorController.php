<?php
class ErrorController extends Zend_Controller_Action
{
    protected $pagePermalink = '';
    public function errorAction()
    {
        $websiteName = HTTP_PATH;
        $this->view->controller = $this->_request->getControllerName();
        $errors = $this->_getParam('error_handler');
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }
       // echo $errors->type; die;
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->view->message = 'Page not found';

                $pagePermalink  = ltrim($this->_request->getPathInfo(), '/');
                $pagePermalink = rtrim($pagePermalink, '/');
                $permalink = explode('/page/', $pagePermalink);
                if (count($permalink) > 0) {
                        $pagePermalink = $permalink[0];
                }
                preg_match("/[^\/]+$/", $pagePermalink, $matches);
                $pageDetails = $this->getPageDetail($pagePermalink);

                if ($pageDetails) {
                    if (is_array($this->pagePermalink)) {
                        $this->pagePermalink = end($this->pagePermalink);
                    }
                    
                    
                    if($pageDetails['pageType'] == 'default'):
                        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($this->pagePermalink);
                    endif;
                    if ($pageDetails['customHeader']) {
                        $this->view->layout()->customHeader = "\n" . $pageDetails['customHeader'];
                    }
                    
                    $pageLogo  = Logo::getPageLogo($pageDetails['logoId']);

                    $specialPageAttachedOffers = Offer::getSpecialPageOffers($pageDetails);
                    $specialOffersPaginator = FrontEnd_Helper_viewHelper::renderPagination($specialPageAttachedOffers, @$matches[0], 27, 7);

                    $frontendViewHelper = new FrontEnd_Helper_viewHelper();
                    $sidebarWidget = $frontendViewHelper->getSidebarWidget($arr = array(), rtrim($this->pagePermalink, '/'));
                    
                    $this->view->pageTitle = $pageDetails['pageTitle'];
                    $this->view->headTitle($pageDetails['metaTitle']);
                    $this->view->headMeta()->setName('description', @trim($pageDetails['metaDescription']));
                    
                    $this->view->pageMode = true;
                    $this->view->matches = $matches[0];
                    $this->view->widget = $sidebarWidget;
                    $this->view->page = $pageDetails;
                    $this->view->paginator = $specialOffersPaginator;
                    $this->view->offercount = count($specialPageAttachedOffers);
                    $this->view->pageLogo = $pageLogo[0];
                } else {
                    $this->getResponse()->setHttpResponseCode(404);
                }
                break;
            default:
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';
                break;
        }
        if ($log = $this->getLog()) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $errors->request->getParams());
        }
        if ($this->getInvokeArg('displayExceptions') == true) {
            echo $this->view->exception = $errors->exception;
            die();
        }

        $signUpFormForStorePage = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formOneHomePage', 'SignUp');
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formSignupSidebarWidget', 'SignUp ');
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, $signUpFormForStorePage, $signUpFormSidebarWidget);
        
        $this->view->request   = $errors->request;
        $this->view->helper = $this->_helper ;
        $this->view->form = $signUpFormForStorePage;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
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
    
    public function getPageDetail($pagePermalink)
    {
        preg_match("/[^\/]+$/", $pagePermalink, $matches);
        if (intval($matches[0]) > 0) {
            $pagePermalink = explode('/'.$matches[0], $pagePermalink);
            $pagePermalink  = $this->getDefaulPermalink($pagePermalink);
        } else {
            $pagePermalink = $this->getPermalinkForFlipit($pagePermalink);
        }
        $this->pagePermalink = $pagePermalink;
        $pagedata = Page::getPageDetailInError(rtrim($pagePermalink, '/'));
        return $pagedata;
    }
    
    public function getPermalinkForFlipit($pagePermalink)
    {
        if (LOCALE!='en') {
            $frontendControllersDirectory = Zend_Controller_Front::getInstance();
            $moduleDirectories = $frontendControllersDirectory->getControllerDirectory();
            $moduleNames = array_keys($moduleDirectories);
            $routeProperties = explode('/', $pagePermalink);
            if (in_array($routeProperties[0], $moduleNames)) {
                $pagePermalink = "";
                foreach ($routeProperties as $key => $route) {
                    if ($key > 0) {
                        $pagePermalink .= $route .'/';
                    }
                }
            }
        }
        return $pagePermalink;
    }
    
    public function getDefaulPermalink($pagePermalink)
    {
        if (HTTP_PATH != "www.kortingscode.nl") {
            $splitParmalink = explode('/', $pagePermalink[0]);
            if(!empty($splitParmalink[1])):
                $pageSpecialPermailink = $splitParmalink[1];
            else:
                $pageSpecialPermailink = $splitParmalink[0];
            endif;
        } else {
            $pageSpecialPermailink = $splitParmalink[0];
        }
        $pageSpecialPermailink = end($pagePermalink);
        return $pageSpecialPermailink;
    }
}
