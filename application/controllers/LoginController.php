<?php

class LoginController extends Zend_Controller_Action
{
    ###########################################################
    ############## REFACTORED CODE ########################
    ###########################################################
    public function init()
    {
        $module = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action = strtolower($this->getRequest()->getActionName());
        if (
            file_exists(
                APPLICATION_PATH . '/modules/' . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml"
            )
        ) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/' . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        $message = $flashMessage->getMessages();
        $this->view->successMessage = isset($message[0]['success']) ?
        $message[0]['success'] : '';
        $this->view->errorMessage = isset($message[0]['error']) ?
        $message[0]['error'] : '';
    }

    public function preDispatch()
    {
        $action = $this->getRequest()->getActionName();
        if (Auth_VisitorAdapter::hasIdentity()
            && ($action == 'forgotpassword'
            || $action == 'resetpassword'
            || $action == 'index')
        ) {
            $this->_redirect(
                HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('inschrijven'). '/' .
                FrontEnd_Helper_viewHelper::__link('profiel')
            );
        }
    }

    public function indexAction()
    {
        $this->view->headTitle("Members Only");
        $loginForm = new Application_Form_Login();
        $this->view->form = $loginForm;
        if ($this->getRequest()->isPost()) {
            if ($loginForm->isValid($_POST)) {
                $visitorDetails = $loginForm->getValues();
                $this->_helper->Login->setVisitorSession($visitorDetails);
                self::redirectByVisitorStatus($visitorDetails);
            } else {
                $loginForm->highlightErrorElements();
            }
        }
        $this->view->pageCssClass = 'login-page';
        # set reponse header X-Nocache used for varnish
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
    }

    public function redirectByVisitorStatus($visitorDetails)
    {
        if (Auth_VisitorAdapter::hasIdentity()) {
            $this->_helper->Login->setUserCookies();
            $this->_redirect(
                HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('inschrijven'). '/' .
                FrontEnd_Helper_viewHelper::__link('profiel')
            );
        } else {
            $this->addFlashMessage(
                $this->view->translate('User Does Not Exist'),
                HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login'),
                'error'
            );
        }
        return;
    }

    public function addFlashMessage($message, $redirectLink, $errorType)
    {
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        $flashMessage->addMessage(array($errorType => $message));
        $this->_redirect($redirectLink);
    }

    public function logoutAction()
    {
        Auth_VisitorAdapter::clearIdentity();
        setcookie('kc_unique_user_id', "", time() - 3600, '/');
        unset($_COOKIE['kc_unique_user_id']);
        # set reponse header X-Nocache used for varnish
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
        $module = $this->getRequest()->getParam('lang');
        $this->_helper->redirector('index');
    }

    public function forgotpasswordAction()
    {
        $permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink);
        $this->view->headTitle($this->view->translate("Members Only"));
        $forgotPasswordForm = new Application_Form_ForgotPassword();
        $this->view->form = $forgotPasswordForm;
        if ($this->getRequest()->isPost()) {
            if ($forgotPasswordForm->isValid($_POST)) {
                $visitorDetails = Auth_VisitorAdapter::forgotPassword($forgotPasswordForm->getValue('emailAddress'));
                if ($visitorDetails!= false) {
                    $mandrilFrontEnd = new FrontEnd_Helper_MandrillMailFunctions();
                    $mandrilFrontEnd->sendForgotPasswordMail(
                        $visitorDetails['id'],
                        $forgotPasswordForm->getValue('emailAddress'),
                        $this
                    );
                    $this->addFlashMessage(
                        $this->view->translate('Please check you mail and click on reset password link'),
                        HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login') . '/'
                        .FrontEnd_Helper_viewHelper::__link('forgotpassword'),
                        'error'
                    );
                } else {
                    $this->addFlashMessage(
                        $this->view->translate('Wrong Email address Please enter valid email address'),
                        HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login') . '/'
                        .FrontEnd_Helper_viewHelper::__link('forgotpassword'),
                        'error'
                    );
                }
            } else {
                $forgotPasswordForm->highlightErrorElements();
            }
        }
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
        $this->view->pageCssClass = 'login-page';
    }

    public function resetpasswordAction()
    {
        $this->view->headTitle($this->view->translate("Members Only"));
        $visitorId = FrontEnd_Helper_viewHelper::sanitize((base64_decode($this->_request->getParam("forgotid"))));
        $visitor = Visitor::getVisitorDetails($visitorId);
        $resetPasswordForm = new Application_Form_ResetPassword();
        $this->view->form = $resetPasswordForm;
        if ($visitor['changepasswordrequest']) {
            $this->view->resetLinkMessage = $this->view->translate('Password Reset Link has already been used!');
            $this->view->linkAlreadyUsed = false;
        } else {
            $this->view->linkAlreadyUsed = true;
        }
        if ($this->getRequest()->isPost()) {
            if ($resetPasswordForm->isValid($_POST)) {
                self::resetPassword(
                    $visitorId,
                    $resetPasswordForm->getValue('password'),
                    $this->_request->getParam("forgotid")
                );
            } else {
                $resetPasswordForm->highlightErrorElements();
            }
        }
        $this->view->pageCssClass = 'login-page';
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
    }

    public function resetPassword($visitorId, $newPassword, $encodedVisitorId)
    {
        $updatedPassword = Visitor::updateVisitorPassword($visitorId, $newPassword);
        if ($updatedPassword) {
            if (!Auth_VisitorAdapter::hasIdentity()) {
                Visitor::updatePasswordRequest($visitorId, 1);
                $redirectLink = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login');
            } else {
                Visitor::updatePasswordRequest($visitorId, 1);
                $redirectLink =
                    HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login'). '/'
                    .FrontEnd_Helper_viewHelper::__link('profiel');
            }
            $this->addFlashMessage($this->view->translate('Your password has been changed.'), $redirectLink, 'success');
        } else {
            $this->addFlashMessage(
                $this->view->translate('Invalid reset password url please confirm again.'),
                HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login'). '/'
                .FrontEnd_Helper_viewHelper::__link('resetpassword') .'/' . $encodedVisitorId,
                'success'
            );
        }
        return true;
    }
    ###########################################################
    ############## END REFACTORED CODE ########################
    ###########################################################

    /**
     * redirecttosignup
     *
     * Show light box for login or signup
     * return phtml of current action
     *
     * @author kraj
     * @version 1.0
     */
    public function redirecttosignupAction()
    {
        $headTitle = $this->view->translate("Members Only ");
        $this->view->headTitle($headTitle);

        $this->_helper->layout()->disableLayout();

    }
    /**
     * updateuserdata
     *
     * save user profile in databse
     * return boolen true
     *
     * @author kraj
     * @version 1.0
     */
    public function updateuserdataAction()
    {
        $headTitle = $this->view->translate("Members Only ");
        $this->view->headTitle($headTitle);

        if (Auth_VisitorAdapter::hasIdentity()) {
            $params = $this->getRequest()->getParams();
            $params['userId'] = Auth_VisitorAdapter::getIdentity()->id;
            $userdetail = Visitor::updateVisitor($params);


            if( $userdetail) {

                $subsStatus = $this->getRequest()->getParam('currentSubscriptionStatus');
                $newStatus =  $this->getRequest()->getParam('weekly') == 'on'  ? 1 : 0  ;

                # chekc if your has not update newsletter status
                if($subsStatus == $newStatus) {
                    $status = 0;
                    $userdetail['message'] = $this->view->translate( "Uw gegevens zijn succesvol aangepast") ;
                } else {

                    # display subscribe/unsubscribe message
                    if($newStatus) {
                        $status = 'subsribed' ;
                        $userdetail['message'] = $this->view->translate( "you have successfully subscribed weekly newsletter.") ;
                    } else {

                        $status = 'unsubscibed' ;
                        $userdetail['message'] = $this->view->translate( "Je bent succesvol uitgeschreven en je zal geen nieuwsbrieven meer van ons ontvangen.") ;
                    }
                }
                $userdetail['newStatus'] = $newStatus ;
                $userdetail['updateType'] = $status;



            } else {

                $userdetail['message'] = $this->view->translate( "Please enter a valid email address") ;

            }


            echo Zend_Json::encode($userdetail);
        }
        die;
    }

    /**
     * directlogin
     *
     * Direct login the user to update his/her profile from newsletter
     *
     * @author cbhopal
     * @version 1.0
     */
    public function directloginAction()
    {
        $username = base64_decode($this->getRequest()->getParam("email"));
        $password = $this->getRequest()->getParam("pwd");
        $data_adapter = new Auth_VisitorAdapter($username, $password);
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('front_login'));
        $result = $auth->authenticate($data_adapter);
        if (Auth_VisitorAdapter::hasIdentity()) {
            $userid = Auth_VisitorAdapter::getIdentity()->id;
            $obj = new Visitor();
            $obj->updateLoginTime($userid);
            //setcookie('kc_session_active', 1, time() + 1800, '/');
            setcookie('kc_unique_user_id', $userid, time() + 2592000, '/');
            $url = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login').'/'.FrontEnd_Helper_viewHelper::__link('profiel');
            $this->getResponse()->setHeader('X-Nocache', 'no-cache');
            $this->_redirect($url);
        }
    }

    /**
     * directloginunsubscribe
     *
     * Direct login the user to update his/her profile from newsletter
     * and unsubscribe from newsletter
     *
     * @author cbhopal
     * @version 1.0
     */
    public function directloginunsubscribeAction()
    {
        $username = base64_decode($this->getRequest()->getParam("email"));
        $password = $this->getRequest()->getParam("pwd");
        $updateWeekNewLttr = Doctrine_Query::create()->update('Visitor')->set('weeklyNewsLetter',0)->where("email = '".$username."'")->execute();
        $moduleKey = $this->getRequest()->getParam('lang' , null);
        $data_adapter = new Auth_VisitorAdapter($username, $password);
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('front_login'));
        $result = $auth->authenticate($data_adapter);
        if (Auth_VisitorAdapter::hasIdentity()) {
            $userid = Auth_VisitorAdapter::getIdentity()->id;
            $obj = new Visitor();
            $obj->updateLoginTime($userid);
            //setcookie('kc_session_active', 1, time() + 1800, '/');
            setcookie('kc_unique_user_id', $userid, time() + 2592000, '/');
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('You are successfully unsubscribed to our newsletter');
            $flash->addMessage(array('success' => $message));
            $this->getResponse()->setHeader('X-Nocache', 'no-cache');

            //echo FrontEnd_Helper_viewHelper::__link('profiel') ;
            //echo  FrontEnd_Helper_viewHelper::__link('login') ;
            $this->_helper->redirector(FrontEnd_Helper_viewHelper::__link('profiel') , FrontEnd_Helper_viewHelper::__link('login') , $moduleKey ) ;

        }

    }

    // Returns the right top menu for the user by fetching the partial which checks if a user is logged in.
    public function usermenuAction()
    {
        $this->_helper->layout()->disableLayout();
    }
    // Returns the footer part for the user by fetching the partial which checks if a user is logged in.
    public function userfooterAction()
    {
        $this->_helper->layout()->disableLayout();
    }
    // Returns the widget part for the user by fetching the partial which checks if a user is logged in.
    public function userwidgetAction()
    {
        $this->_helper->layout()->disableLayout();
    }
    // returns the signup bar on home
    public function usersignupAction()
    {
        $this->_helper->layout()->disableLayout();
    }
    // returns the dummy page bar on home
    public function dummypageAction()
    {
        $this->view->layout()->robotKeywords = 'noindex, nofollow' ;

    }
    public function dummyboxAction()
    {
        $this->view->layout()->robotKeywords = 'noindex, nofollow' ;

        $headTitle = $this->view->translate("Members Only ");
        $this->view->headTitle($headTitle);
        $this->_helper->layout()->disableLayout();
    }
}
