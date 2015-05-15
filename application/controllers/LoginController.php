<?php
class LoginController extends Zend_Controller_Action
{
    public $_loginLinkAndData = array();
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
        $this->viewHelperObject = new \FrontEnd_Helper_viewHelper();
    }

    public function preDispatch()
    {
        $action = $this->getRequest()->getActionName();
        if (\Auth_VisitorAdapter::hasIdentity()
            && ($action == 'forgotpassword'
            || $action == 'resetpassword'
            || $action == 'index')
        ) {
            $this->_redirect(
                HTTP_PATH_LOCALE. \FrontEnd_Helper_viewHelper::__link('link_inschrijven'). '/' .
                \FrontEnd_Helper_viewHelper::__link('link_profiel')
            );
        }
    }

    public function indexAction()
    {
        $emailAddressFromMemory = '';
        $emailAddressSpace = new \Zend_Session_Namespace('emailAddressSpace');
        if (isset($emailAddressSpace->emailAddressSpace)) {
            $emailAddressFromMemory = $emailAddressSpace->emailAddressSpace;
            $emailAddressSpace->emailAddressSpace = '';
        }
        $loginForm = new \Application_Form_Login();
        $this->view->form = $loginForm;
        $loginForm->getElement('emailAddress')->setValue($emailAddressFromMemory);
        $this->viewHelperObject->getMetaTags($this);
        if ($this->getRequest()->isPost()) {
            if ($loginForm->isValid($this->getRequest()->getPost())) {
                $visitorDetails = $loginForm->getValues();
                $this->_helper->Login->setVisitorSession($visitorDetails);
                self::redirectByVisitorStatus($visitorDetails);
            } else {
                $loginForm->highlightErrorElements();
            }
        }
        $this->view->headTitle(\FrontEnd_Helper_viewHelper::__form('form_Members Only'));
        $this->view->pageCssClass = 'login-page';
        # set reponse header X-Nocache used for varnish
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
    }

    public function redirectByVisitorStatus($visitorDetails)
    {
        if (\Auth_VisitorAdapter::hasIdentity()) {
            $this->_helper->Login->setUserCookies();
            \FrontEnd_Helper_viewHelper::redirectAddToFavouriteShop();
            $redirectUrl = HTTP_PATH_LOCALE
                . \FrontEnd_Helper_viewHelper::__link('link_mijn-favorieten') . "/"
                . \FrontEnd_Helper_viewHelper::__link('link_memberonlycodes');
            $shopIdNameSpace = new \Zend_Session_Namespace('shopId');
            if ($shopIdNameSpace->shopId) {
                $shopName = \KC\Repository\Shop::getShopName(base64_decode($shopIdNameSpace->shopId));
                $membersNamespace = new \Zend_Session_Namespace('membersOnly');
                if (isset($membersNamespace->membersOnly) && $membersNamespace->membersOnly == '1') {
                    $shopInfo = \KC\Repository\Shop::getShopInformation(base64_decode($shopIdNameSpace->shopId));
                    $shopPermalink = !empty($shopInfo) ? $shopInfo[0]['permaLink'] : '';
                } else {
                    $shopPermalink = \FrontEnd_Helper_viewHelper::__link('link_mijn-favorieten');
                }
                $membersNamespace->membersOnly = '';
                $visitorFavouriteShopStatus = \KC\Repository\Visitor::getFavoriteShopsForUser(
                    \Auth_VisitorAdapter::getIdentity()->id,
                    base64_decode($shopIdNameSpace->shopId)
                );
                if ($visitorFavouriteShopStatus) {
                    $messageText = 'shop already added in your favourite shops';
                } else {
                    $messageText = 'have been added to your favorite shops';
                    \KC\Repository\Shop::shopAddInFavourite(
                        \Auth_VisitorAdapter::getIdentity()->id,
                        base64_decode($shopIdNameSpace->shopId)
                    );
                }
                $shopIdNameSpace->shopId = '';
                $message = $shopName. " ".  \FrontEnd_Helper_viewHelper::__translate($messageText);
                $redirectUrl = HTTP_PATH_LOCALE. $shopPermalink;
                self::addFlashMessage($message, $redirectUrl, 'success');
            } else {
                $this->_redirect($redirectUrl);
            }

        } else {
            $visitorEmail = new \Zend_Session_Namespace('emailAddressSpace');
            $visitorEmail->emailAddressSpace = $visitorDetails['emailAddress'];
            $this->addFlashMessage(
                \FrontEnd_Helper_viewHelper::__translate('User Does Not Exist'),
                HTTP_PATH_LOCALE . \FrontEnd_Helper_viewHelper::__link('link_login'),
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
        \Auth_VisitorAdapter::clearIdentity();
        setcookie('kc_unique_user_id', "", time() - (86400 * 3), '/');
        # set reponse header X-Nocache used for varnish
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
        \Zend_Session::namespaceUnset('favouriteShopId');
        $this->_redirect(HTTP_PATH_LOCALE);
    }

    public function forgotpasswordAction()
    {
        $permalink = ltrim(\Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $this->view->canonical = \FrontEnd_Helper_viewHelper::generateCononical($permalink);
        $this->view->headTitle(\FrontEnd_Helper_viewHelper::__form('form_Members Only'));
        $forgotPasswordForm = new \Application_Form_ForgotPassword();
        $this->view->form = $forgotPasswordForm;
        if ($this->getRequest()->isPost()) {
            if ($forgotPasswordForm->isValid($this->getRequest()->getPost())) {
                $visitorDetails = \KC\Repository\Visitor::getVisitorDetailsByEmail(
                    FrontEnd_Helper_viewHelper::sanitize($forgotPasswordForm->getValue('emailAddress'))
                );
                $fromEmail = \KC\Repository\Signupmaxaccount::getEmailAddress();
                if ($visitorDetails!= false) {
                    \KC\Repository\Visitor::updatePasswordRequest($visitorDetails['id'], 0);
                    $mailer  = new \FrontEnd_Helper_Mailer();
                    $content = array(
                                    'name'    => 'content',
                                    'content' => $this->view->partial(
                                        'emails/forgotpassword.phtml',
                                        array(
                                            'resetPasswordLink' => HTTP_PATH_LOCALE .
                                            \FrontEnd_Helper_viewHelper::__link('link_login').'/'
                                            .FrontEnd_Helper_viewHelper::__link('link_resetpassword').'/'
                                            .base64_encode($visitorDetails['id'])
                                            )
                                    )
                                );
                    $VisitorName = $visitorDetails['firstName'].' '.$visitorDetails['lastName'];
                    \BackEnd_Helper_MandrillHelper::getDirectLoginLinks($this, 'frontend', $visitorDetails['email']);
                    $mailer->send(
                        \FrontEnd_Helper_viewHelper::__email('email_sitename'),
                        $fromEmail[0]['emailperlocale'],
                        $VisitorName,
                        $visitorDetails['email'],
                        \FrontEnd_Helper_viewHelper::__email('email_Forgot Password'),
                        $content,
                        \FrontEnd_Helper_viewHelper::__email('email_Forgot password header'),
                        '',
                        $this->_loginLinkAndData
                    );
                    $this->addFlashMessage(
                        \FrontEnd_Helper_viewHelper::__translate('Please check you mail and click on reset password link'),
                        HTTP_PATH_LOCALE . \FrontEnd_Helper_viewHelper::__link('link_login') . '/'
                        .\FrontEnd_Helper_viewHelper::__link('link_forgotpassword'),
                        'error'
                    );
                } else {
                    $this->addFlashMessage(
                        \FrontEnd_Helper_viewHelper::__translate('Wrong Email address. Please enter valid email address'),
                        HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('link_login') . '/'
                        .\FrontEnd_Helper_viewHelper::__link('link_forgotpassword'),
                        'error'
                    );
                }
            } else {
                $forgotPasswordForm->highlightErrorElements();
            }
        }
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
        $this->view->pageCssClass = 'login-page';
        $this->viewHelperObject->getMetaTags($this);
    }

    public function resetpasswordAction()
    {
        $this->view->headTitle(\FrontEnd_Helper_viewHelper::__form('form_Members Only'));
        $visitorId = \FrontEnd_Helper_viewHelper::sanitize((base64_decode($this->_request->getParam("forgotid"))));
        $visitor = \KC\Repository\Visitor::getUserDetails($visitorId);
        $resetPasswordForm = new \Application_Form_ResetPassword();
        $this->view->form = $resetPasswordForm;
        if ($visitor['changepasswordrequest']) {
            $this->view->resetLinkMessage = \FrontEnd_Helper_viewHelper::__translate('This password reset link has already been used!');
            $this->view->linkAlreadyUsed = false;
        } else {
            $this->view->linkAlreadyUsed = true;
        }
        if ($this->getRequest()->isPost()) {
            if ($resetPasswordForm->isValid($this->getRequest()->getPost())) {
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
        $this->viewHelperObject->getMetaTags($this);
    }

    public function resetPassword($visitorId, $newPassword, $encodedVisitorId)
    {
        $updatedPassword = \KC\Repository\Visitor::updateVisitorPassword($visitorId, $newPassword);
        if ($updatedPassword) {
            if (!\Auth_VisitorAdapter::hasIdentity()) {
                \KC\Repository\Visitor::updatePasswordRequest($visitorId, 1);
                $redirectLink = HTTP_PATH_LOCALE . \FrontEnd_Helper_viewHelper::__link('link_login');
            } else {
                \KC\Repository\Visitor::updatePasswordRequest($visitorId, 1);
                $redirectLink =
                    HTTP_PATH_LOCALE . \FrontEnd_Helper_viewHelper::__link('link_login'). '/'
                    .\FrontEnd_Helper_viewHelper::__link('link_profiel');
            }
            $this->addFlashMessage(\FrontEnd_Helper_viewHelper::__translate('Your password has been changed.'), $redirectLink, 'success');
        } else {
            $this->addFlashMessage(
                \FrontEnd_Helper_viewHelper::__translate('Invalid reset password url please confirm again.'),
                HTTP_PATH_LOCALE . \FrontEnd_Helper_viewHelper::__link('link_login'). '/'
                .\FrontEnd_Helper_viewHelper::__link('link_resetpassword') .'/' . $encodedVisitorId,
                'success'
            );
        }
        return true;
    }

    public function confirmemailAction()
    {
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
        $visitorEmail = \FrontEnd_Helper_viewHelper::sanitize((base64_decode($this->_request->getParam("email"))));
        $visitor = \KC\Repository\Visitor::getVisitorDetailsByEmail($visitorEmail);
        if (!empty($visitor)) {
            if (\Visitor::updateVisitorStatus($visitor[0]['id'])) {
                $this->addFlashMessage(
                    \FrontEnd_Helper_viewHelper::__translate('Your email address has been confirmed please login'),
                    HTTP_PATH_LOCALE . \FrontEnd_Helper_viewHelper::__link('link_login'),
                    'success'
                );
            } else {
                $this->addFlashMessage(
                    \FrontEnd_Helper_viewHelper::__translate('Your email address is already confirmed'),
                    HTTP_PATH_LOCALE . \FrontEnd_Helper_viewHelper::__link('link_login'),
                    'error'
                );
            }
        } else {
            $this->addFlashMessage(
                \FrontEnd_Helper_viewHelper::__translate('Invalid confirmation link'),
                HTTP_PATH_LOCALE . \FrontEnd_Helper_viewHelper::__link('link_login'),
                'error'
            );
        }
    }

    public function directloginAction()
    {
        $username = base64_decode($this->getRequest()->getParam("email"));
        $password = $this->getRequest()->getParam("pwd");
        $data_adapter = new \Auth_VisitorAdapter($username, MD5($password));
        $auth = \Zend_Auth::getInstance();
        $auth->setStorage(new \Zend_Auth_Storage_Session('front_login'));
        $result = $auth->authenticate($data_adapter);
        if (\Auth_VisitorAdapter::hasIdentity()) {
            $userid = \Auth_VisitorAdapter::getIdentity()->id;
            $obj = new \KC\Repository\Visitor();
            $obj->updateLoginTime($userid);
            $this->_helper->Login->setUserCookies();
            $url =
                HTTP_PATH_LOCALE
                . \FrontEnd_Helper_viewHelper::__link('link_inschrijven')
                .'/'
                .\FrontEnd_Helper_viewHelper::__link('link_profiel');
            $this->getResponse()->setHeader('X-Nocache', 'no-cache');
            $this->_redirect($url);
        }
    }
    #this function used in mandrill
    public function directloginunsubscribeAction()
    {
        $this->directUnsubscribe();
    }

    public function directcodealertunsubscribeAction()
    {
        $this->directUnsubscribe('codealert');
    }

    public function directUnsubscribe($type = '')
    {
        $username = base64_decode($this->getRequest()->getParam("email"));
        $password = $this->getRequest()->getParam("pwd");
        
        if ($type == 'codealert') {
            $shopName = \KC\Repository\Shop::getShopName(base64_decode($this->getRequest()->getParam("shopid")));
            $message = $shopName.' '.\FrontEnd_Helper_viewHelper::__translate('have been removed from your favorite shops');
            $visitorsId = \KC\Repository\Visitor::getVisitorDetailsByEmail($username);
            $visitorsId = !empty($visitorsId) ? $visitorsId['id'] : '';
            if (isset($visitorsId) && $visitorsId != '') {
                $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $queryBuilder->delete("KC\Entity\FavoriteShop", "fv")
                    ->where('fv.visitor='.$visitorsId)
                    ->andWhere('fv.shop='.base64_decode($this->getRequest()->getParam("shopid")))
                    ->getQuery()
                    ->execute();
            }
        } else {
            $message = \FrontEnd_Helper_viewHelper::__translate('You are successfully unsubscribed to our newsletter');
            $newsletterQueryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $newsletterQueryBuilder->update('KC\Entity\Visitor', 'v')
                ->set('v.weeklyNewsLetter', 0)
                ->where($newsletterQueryBuilder->expr()->eq("v.email", $newsletterQueryBuilder->expr()->literal($username)))
                ->getQuery()->execute();
        }

        $moduleKey = $this->getRequest()->getParam('lang', null);
        $data_adapter = new \Auth_VisitorAdapter($username, MD5($password));
        $auth = \Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('front_login'));
        $result = $auth->authenticate($data_adapter);

        if (\Auth_VisitorAdapter::hasIdentity()) {
            $userid = \Auth_VisitorAdapter::getIdentity()->id;
            $obj = new \KC\Repository\Visitor();
            $obj->updateLoginTime($userid);
            $this->_helper->Login->setUserCookies();
            $flash = $this->_helper->getHelper('FlashMessenger');
            $flash->addMessage(array('success' => $message));
            $this->getResponse()->setHeader('X-Nocache', 'no-cache');
            $this->_helper->redirector(
                \FrontEnd_Helper_viewHelper::__link('link_profiel'),
                \FrontEnd_Helper_viewHelper::__link('link_inschrijven'),
                $moduleKey
            );
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
}
