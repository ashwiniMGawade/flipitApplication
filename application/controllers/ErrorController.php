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

                $pagePermalink = $this->_helper->Error->getPageParmalink(ltrim($this->_request->getPathInfo(), '/'));
                $pageNumber = $this->_helper->Error->getPageNumbering($pagePermalink);
                $pageDetails = $this->getPageDetails($pagePermalink, $pageNumber);
                if ($pageNumber >= 4) {
                    $this->_helper->layout()->disableLayout();
                    FrontEnd_Helper_viewHelper::setErrorPageParameters($this);
                }
                if ($pageDetails) {
                    if ($pageDetails['pageAttributeId'] == 2) {
                        $this->view->pageCssClass = 'faq-page home-page';
                    } else if (isset($pageDetails['pageAttributeId']) && $pageDetails['pageAttributeId'] == 1) {
                        $flashMessage = $this->_helper->getHelper('FlashMessenger');
                        $message = $flashMessage->getMessages();
                        $this->view->successMessage = isset($message[0]['success']) ? $message[0]['success'] :'';
                        $this->view->pageCssClass = 'contact-page home-page';
                    } else {
                        $this->view->pageCssClass = 'flipit-expired-page home-page';
                    }

                    if (is_array($this->pagePermalink)) {
                        $this->pagePermalink = end($this->pagePermalink);
                    }
                    if($pageDetails['pageType'] == 'default'):
                        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($this->pagePermalink);
                    endif;
                    if ($pageDetails['customHeader']) {
                        $this->view->layout()->customHeader = "\n" . $pageDetails['customHeader'];
                    }
                    $specialPageOffers = Offer::getSpecialPageOffers($pageDetails);
                    $paginationNumber['page'] = $pageNumber;
                    $specialOffersPaginator = FrontEnd_Helper_viewHelper::renderPagination(
                        $specialPageOffers,
                        $paginationNumber,
                        30,
                        3
                    );
                    $frontendViewHelper = new FrontEnd_Helper_SidebarWidgetFunctions();
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
                    $this->view->pageHeaderImage = Logo::getPageLogo($pageDetails['pageHeaderImageId']);
                    $this->view->offercount = count($specialPageOffers);
                    $this->view->offersPaginator = $specialOffersPaginator;
                    $this->view->widget = $sidebarWidget;
                    $this->view->pageMode = true;
                } else {
                    $this->_helper->layout()->disableLayout();
                    FrontEnd_Helper_viewHelper::setErrorPageParameters($this);
                }
                break;
            default:
                $this->_helper->layout()->disableLayout();
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';
                $this->view->popularShops = Shop::getPopularStores(12);
                $websitesWithLocales = FrontEnd_Helper_viewHelper::getWebsitesLocales(Website::getAllWebsites());
                $this->view->flipitLocales = $websitesWithLocales;
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
        $signUpFormSidebarWidget =
            FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formSignupSidebarWidget', 'SignUp ');
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

    public function getPageDetails($pagePermalink, $pageNumber)
    {
        if (intval($pageNumber) > 0) {
            $pagePermalink = explode('/'.$pageNumber, $pagePermalink);
            $pagePermalink = $this->_helper->Error->getDefaultPermalink($pagePermalink);
        } else {
            $pagePermalink = $this->_helper->Error->getPermalinkForFlipit($pagePermalink);
        }
        $this->pagePermalink = $pagePermalink;
        $pagedata = Page::getPageDetailsInError(rtrim($pagePermalink, '/'));
        return $pagedata;
    }
}
