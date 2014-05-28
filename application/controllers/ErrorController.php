<?php
class ErrorController extends Zend_Controller_Action
{
    protected $pagePermalink = '';
    public function errorAction()
    {
        $this->view->controller = $this->_request->getControllerName();
        $errors = $this->_getParam('error_handler');
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached on error page';
            return;
        }
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                $pagePermalink = $this->getPageParmalink();
                $pageNumber = $this->getPageNumbering($pagePermalink);
                $pageDetails = $this->getPageDetails($pagePermalink, $pageNumber);
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
                    $specialPageOffers = Offer::getSpecialPageOffers($pageDetails);
                    $specialOffersPaginator = FrontEnd_Helper_viewHelper::renderPagination(
                        $specialPageOffers,
                        $pageNumber,
                        30,
                        3
                    );
                    $frontendViewHelper = new FrontEnd_Helper_viewHelper();
                    $sidebarWidget = $frontendViewHelper->getSidebarWidget(
                        $sidebarParameters = array(),
                        rtrim($this->pagePermalink, '/')
                    );

                    $this->view->message = 'Page not found';
                    $this->view->pageTitle = $pageDetails['pageTitle'];
                    $this->view->headTitle($pageDetails['metaTitle']);
                    $this->view->headMeta()->setName('description', trim($pageDetails['metaDescription']));
                    $this->view->matches = $pageNumber;
                    $this->view->page = $pageDetails;
                    $this->view->pageLogo = $pageLogo[0];
                    $this->view->offercount = count($specialPageOffers);
                    $this->view->offersPaginator = $specialOffersPaginator;
                    $this->view->widget = $sidebarWidget;
                    $this->view->pageMode = true;
                } else {
                    $this->getResponse()->setHttpResponseCode(404);
                    $this->_helper->layout()->disableLayout();
                    $this->view->popularShops = Shop::getPopularStores(10);
                    $websitesWithLocales = FrontEnd_Helper_viewHelper::getWebsitesLocales(Website::getAllWebsites());
                    $this->view->flipitLocales = $websitesWithLocales;
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
        }

        $largeSignUpForm = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('largeSignupForm', 'SignUp');
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formSignupSidebarWidget', 'SignUp ');
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, $largeSignUpForm, $signUpFormSidebarWidget);

        $this->view->request   = $errors->request;
        $this->view->helper = $this->_helper ;
        $this->view->form = $largeSignUpForm;
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

    public function getPageParmalink()
    {
        $pagePermalinkWithoutLeftSlash  = ltrim($this->_request->getPathInfo(), '/');
        $pagePermalink = rtrim($pagePermalinkWithoutLeftSlash, '/');
        $permalink = explode('/page/', $pagePermalink);
        if (count($permalink) > 0) {
            $pagePermalink = $permalink[0];
        }
        return $pagePermalink;
    }

    public function getPageDetails($pagePermalink, $pageNumber)
    {
        if (intval($pageNumber) > 0) {
            $pagePermalink = explode('/'.$pageNumber, $pagePermalink);
            $pagePermalink = $this->getDefaultPermalink($pagePermalink);
        } else {
            $pagePermalink = $this->getPermalinkForFlipit($pagePermalink);
        }
        $this->pagePermalink = $pagePermalink;
        $pagedata = Page::getPageDetailsInError(rtrim($pagePermalink, '/'));
        return $pagedata;
    }

    public function getPageNumbering($pagePermalink)
    {
        preg_match("/[^\/]+$/", $pagePermalink, $matches);
        return $matches[0];
    }

    public function getDefaultPermalink($pagePermalink)
    {
        if (HTTP_PATH != "www.kortingscode.nl") {
            $splitParmalink = explode('/', $pagePermalink[0]);
            if(!empty($splitParmalink[1])):
                 $pagePermalink = $splitParmalink[1];
            else:
                 $pagePermalink = $splitParmalink[0];
            endif;
        } else {
             $pagePermalink = $splitParmalink[0];
        }
        return  $pagePermalink;
    }

    public function getPermalinkForFlipit($pagePermalink)
    {
        if (LOCALE!='en') {
            $frontEndControllersDirectory = Zend_Controller_Front::getInstance();
            $moduleDirectories = $frontEndControllersDirectory->getControllerDirectory();
            $moduleNames = array_keys($moduleDirectories);
            $routeProperties = explode('/', $pagePermalink);
            if (in_array($routeProperties[0], $moduleNames)) {
                $pagePermalink = "";
                foreach ($routeProperties as $routeIndex => $route) {
                    if ($routeIndex > 0) {
                        $pagePermalink .= $route .'/';
                    }
                }
            }
        }
        return $pagePermalink;
    }
}
