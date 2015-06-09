<?php

class Admin_AuthController extends Zend_Controller_Action
{
    /**
     * handle database connection
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        \BackEnd_Helper_viewHelper::addConnection();//connection generate with second database

        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ?
        $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ?
        $message[0]['error'] : '';


    }
    /**
     * handle request
     * @see Zend_Controller_Action::preDispatch()
     * @author mkaur
     * @version 1.0
     */
    public function preDispatch()
    {

        //get action name from zend parametes
        $action = $this->getRequest()->getActionName();

        // check action
        switch($action) {
            case "logout" :


            break;
            case "index":

                //if user is authenticated
                if (\Auth_StaffAdapter::hasIdentity()) {
                     $this->_redirect('/admin/index');
                }

            default :
            break;
        }

    }

    /**
     * Action use for login user
     * user enter the valid username and password then
     * redirect to user list in admin panle
     * @author kraj
     * @version 1.1
     */
    public function indexAction()
    {
        //echo $this->view->translate('CMS LOGIN');
        //Get all params from post/get

        $params = $this->_request->getParams();
        //if param msg set then
        if (isset($params['msg'])) {
            //set msg session for message
            $msg = new Zend_Session_Namespace('msg');
            if (!isset($msg->msg)) {
                //if session set then message not show again and again
                $msg->msg = true;
                $this->view->message = $this->view->translate('Invalid username or password. Please try again.');
            }

        }
        //check post form or not
        if ($this->getRequest()->isPost()) {

            //unset then the msg session
            Zend_Session::namespaceUnset('msg');
            $username = $params['uname'];
            $password = $params['pwd'];

            /*Zend_Session::setOptions(array(
                    'cookie_lifetime' => 10,
                    'gc_maxlifetime'  => 10));*/

            //set authentication if user valid
            $data_adapter = new \Auth_StaffAdapter($username, $password);
            $auth = Zend_Auth::getInstance();


            $result = $auth->authenticate($data_adapter);
            if (\Auth_StaffAdapter::hasIdentity()) {
                
                //create object of user class
                $timeSeconds = 28800;
               
                $Obj = Zend_Registry::get('emUser')->find('KC\Entity\User\User', \Auth_StaffAdapter::getIdentity()->id);

                $Obj->updateLoginTime(\Auth_StaffAdapter::getIdentity()->id);
                $user = new Zend_Session_Namespace('user');
                $user->user_data = $Obj;
                $user->setExpirationSeconds($timeSeconds);

                $userPermission = $Obj->getPermissions();
                //set session for permission
                $sessionNamespace = new Zend_Session_Namespace();
                $sessionNamespace->settings =  $userPermission;

                //initialize mandrill with the template name and other necessary options
                $adminPasswordAge =    $this->getInvokeArg('adminPasswordAge')  ;

                # check is admin password age is defined or not if check then validate for the same
                if ($adminPasswordAge) {

                    # check passowrd is older than two months or not
                    $date = new DateTime(\Auth_StaffAdapter::getIdentity()->passwordChangeTime->format('Y-m-d H:i:s'));
                    $date->modify($adminPasswordAge);

                    $newPasswordChagetime = strtotime($date->format('Y-m-d H:i:s'));

                    if (time() >= $newPasswordChagetime) {
                        $sessionNamespace->showPasswordChange  = true;
                    } else {
                        $sessionNamespace->showPasswordChange  = false;
                    }

                } else {

                    $sessionNamespace->showPasswordChange = false;
                }

                //$Obj->setUserSession(Auth_StaffAdapter::getIdentity()->id,$_COOKIE['token']);
                //$sessionNamespace->setExpirationSeconds(10);
                $referer = new Zend_Session_Namespace('referer');

                # get first website which is accesible by logged in user

                $website = trim($userPermission['webaccess'][0]['websitename']);

                $locateData = explode('/', $website);

                # set website name
                setcookie('site_name', $website, time() + 86400, '/');

                # set locale based on website(en for kc.nl)
                $locale = isset($locateData[1]) ? $locateData[1] : 'en' ;
                setcookie('locale', $locale, time() + 86400, '/');


                //$sessionNamespace->setExpirationSeconds(10);
                $namespace = new Zend_Session_Namespace('Zend_Auth');
                $namespace->setExpirationSeconds($timeSeconds);

                if (isset($referer->refer) && $referer->refer != '') {
                    $url  =  $referer->refer;
                    //Zend_Session::namespaceUnset('referer');
                } else {
                    $url  =  HTTP_PATH . 'admin/offer';
                }
                //redirect to other page(user list)
                $this->_redirect(rtrim($url, '?'));

            } else {
                //redirect to same page but with msg parameter
                $this->_helper->redirector('index', 'auth', 'admin', array('msg'=>1));

            }
        }
    }
    /**
     * function generates a random password
     * @version 1.1  updated by kraj
     * @author mkaur
     */
    public function forgotpasswordAction()
    {
        //get parameter from post /get
        $params = $this->_request->getParams();

        //if msg param set
        if(isset($params['msg'])) {
            //set session for msg in zend session namespance
            $msg = new Zend_Session_Namespace('msg');
            if(!isset($msg->msg)) {
                //display message in error panel only one time
                $msg->msg = true;
                $this->view->message = $this->view->translate('Your E-mail ID is not in our database');
            }

        }
        //check form is post or not
        if ($this->getRequest()->isPost()) {

                //unset then the msg session
                Zend_Session::namespaceUnset('msg');
                $email = $params["email"];
                //check user by email from database
                $authStaffAdapter = new \Auth_StaffAdapter();
                $result = $authStaffAdapter->forgotPassword($email);

                if (!empty($result)) {

                    //generate new password
                    $newPwd = $authStaffAdapter->genRandomString(10);
                    $setPass = \Zend_Registry::get('emUser')->find('KC\Entity\User\User', $result['id']);
                    $setPass->password = md5($newPwd);
                    //set new password in database
                    \Zend_Registry::get('emUser')->persist($setPass);
                    \Zend_Registry::get('emUser')->flush();
                    //create view object
                    $html = new Zend_View();
                    $html->setScriptPath(APPLICATION_PATH. '/modules/admin/views/scripts/template/');
                    //assign valeues
                    $html->assign('name', $result['username']);
                    $html->assign('pwd', $newPwd);
                    $html->assign('host', HTTP_PATH ."/admin");
                    //render view
                    $bodyText = $html->render('template.phtml');
                    $recipents = array("to" => $this->getRequest()->getParam("email"));
                    $subject = \FrontEnd_Helper_viewHelper::__email("email_Forgot password");
                    $body = $bodyText;
                    //send a mail to user password update notification
                    $sendEmail = \BackEnd_Helper_viewHelper::SendMail($recipents,$subject, $body);
                    $url  =  HTTP_PATH . 'admin/auth/pwdresetsuccessfully';
                    $this->_redirect($url);


                } else {
                        //redirect to same action with param like msg
                        $this->_helper->redirector('forgotpassword', 'auth', 'admin',array('msg'=>1));

                }

        }

    }
    /**
     * logoutaction expire the session and redirect.
     * @author mkaur
     * @version 1.0
     */
    public function logoutAction()
    {
       
        //clear identity of the user
        \Auth_StaffAdapter::clearIdentity();
        //unset the session
        Zend_Session::namespaceUnset('settings');
        Zend_Session::namespaceUnset('user');
        #Unset the cookie for CMS
        unset($_COOKIE['locale']);
        unset($_COOKIE['site_name']);
        //redirect to auth index action of the auth
        Zend_Session::destroy();
        $this->_redirect("/admin/auth");
    }


    /**
     * display forgot password successfully
     * page
     * @author mkaur
     * @version 1.0
     */
    public function pwdresetsuccessfullyAction()
    {
        // action body
    }


    /**
     * updatePassword
     *
     * this action is used for user password updation
     */
    public function updatePasswordAction()
    {
        if($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $id = \Auth_StaffAdapter::getIdentity()->id;
            $params['id'] = $id;
            # call used password update function
            $entityManagerUser  = \Zend_Registry::get('emUser');
            $repo = $entityManagerUser->getRepository('KC\Entity\User\User');
            $user = new \KC\Repository\User();
            $result = $user->updatePassword($params);

            $flash = $this->_helper->getHelper('FlashMessenger');

            # display error or success message based on the result
            if($result) {
                $message = $this->view->translate($result);
                $flash->addMessage(array('error' => $message));

            }else {

                $message = $this->view->translate($result);
                $flash->addMessage(array('success' => "Password has been updated successfully"));

                $sessionNamespace = new Zend_Session_Namespace();
                $sessionNamespace->showPasswordChange  = false;
            }

            $this->_redirect("/admin/index");
        }

    }

}
