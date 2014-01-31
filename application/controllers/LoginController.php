<?php

class LoginController extends Zend_Controller_Action {

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
	
	public function preDispatch() {

		$action = $this->getRequest()->getActionName();
		if (Auth_VisitorAdapter::hasIdentity() && ($action == 'forgotpassword' 
								||	$action == 'resetpassword' ||	$action == 'index')){
			
			$this->_helper->redirector(null,FrontEnd_Helper_viewHelper::__link('mijn-favorieten')) ;
		}
	}

	public function indexAction() {
		
		# get cononical link
		$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
		//$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononical($permalink) ;
		
		$this->view->headTitle("Members Only");

	}
	
	/**
	 * check user authenticaion 
	 * 
	 * @version 1.0
	 */
	public function checkloginAction() {

		$headTitle = $this->view->translate("Members Only ");
    	$this->view->headTitle($headTitle);
		
		$params = $this->_request->getParams();
		//for displaying error message when incorrect credentials
		$errmsg = $this->_helper->flashMessenger->getMessages();
		if (!empty($errmsg)) {
			$this->view->message = 'showmsg';
		}
		//check post form or not
		if ($this->getRequest()->isPost()) {
			$username = $params["uname"];
			$password = MD5($params["pwd"]);
			
			$data_adapter = new Auth_VisitorAdapter($username, $password);

			$auth = Zend_Auth::getInstance();

			$auth->setStorage(new Zend_Auth_Storage_Session('front_login'));
			$result = $auth->authenticate($data_adapter);

			if (Auth_VisitorAdapter::hasIdentity()) {

				$userid = Auth_VisitorAdapter::getIdentity()->id;
				$obj = new Visitor();
				$obj->updateLoginTime($userid);
				//setcookie('kc_session_active', 1, time() + 2592000, '/');
				setcookie('kc_unique_user_id', $userid, time() + 2592000, '/');
				echo $url = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('mijn-favorieten'); 
				die();
			} else {
				echo "false";
				die();

			}
		}

	}
	/**
	 * forget password by email
	 *
	 * @version 1.0
	 */
	public function forgotpasswordAction() {
		
		$this->view->controllerName = $this->getRequest()->getControllerName();
		$this->view->actionName = $this->getRequest()->getActionName();
		
		# get cononical link
		$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
		$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononical($permalink) ;
		
		$headTitle = $this->view->translate("Members Only ");
    	$this->view->headTitle($headTitle);
	}
	/**
	 * send mail to user for reset password
	 *
	 * @version 1.0
	 */ 
	public function forgotpwdAction() {
		
		$headTitle = $this->view->translate("Members Only ");
    	$this->view->headTitle($headTitle);
		$params = $this->_request->getParams();
		
		if ($this->getRequest()->isPost()) {
			
			$email = $params["email"];
			$result = Auth_VisitorAdapter::forgotPassword($email);
			
			if ($result == true) {
				$resultuserid = base64_encode($result["id"]);
				
				$domain = $_SERVER['HTTP_HOST'];
				 
				$imgLogoMail = "<a href=".HTTP_PATH_LOCALE."><img src='".HTTP_PATH."public/images/flipit-welcome-mail.jpg'/></a>";
				 
				$siteName = "Flipit.com";
				 
				$siteUrl = HTTP_PATH_LOCALE;
				 
				if($domain == "kortingscode.nl" || $domain == "www.kortingscode.nl") {
					 
					$imgLogoMail = "<a href=".HTTP_PATH_LOCALE."><img src='".HTTP_PATH."public/images/HeaderMail.gif'/></a>";
					 
					$siteName = "Kortingscode.nl";
					 
				}
				$mailData = array(array('name'=>'headerWelcome',
						'content'=>$imgLogoMail
				),
						array('name'=>'bestRegards',
								'content'=>$this->view->translate('Beste').' '.$siteName.' '.$this->view->translate('Bezoeker,')
						),
						array('name'=>'centerContent',
								'content'=>$this->view->translate('Geen enkel probleem dat u uw wachtwoord heeft vergeten, via de volgende link kunt u deze opnieuw instellen:'). '<a href="'
											. HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login').'/'.FrontEnd_Helper_viewHelper::__link('resetpassword').'/'.$resultuserid
											. '">'.$this->view->translate('Klik hier').'</a>' 
						),
						array('name'=>'bottomContent',
								'content'=> $this->view->translate('Groetjes').',<br><br>'. $this->view->translate('De redactie van Kortingscode.nl')
				
						),
						array('name'=>'copyright',
								'content'=>$this->view->translate('Copyright &copy; 2013').' '.$siteName.'. '.$this->view->translate('All Rights Reserved.')
						),
						array('name'=>'address',
								'content'=>$this->view->translate("U ontvangt deze nieuwsbrief omdat u ons uw toestemming heeft gegeven om u op de hoogte te houden van onze laatste").
								'<br/>' . $this->view->translate("acties."). '<a href='.$siteUrl.' style="color:#ffffff; padding:0 2px;">'.$siteName.'</a>' . $this->view->translate('is een onderdeel van Imbull, Weteringschans 120, 1017XT Amsterdam - KvK 34339618')
						),
						array('name'=>'logincontact',
								'content'=>'<a style="color:#ffffff; padding:0 4px; text-decoration:none;" href="'.HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login').'">'.$this->view->translate('Inloggen').'</a>'
						));
				 
				$email_data = Signupmaxaccount::getemailmaxaccounts();
				$emailFrom  = $email_data[0]['emailperlocale'];
				 
				$mandrill = new Mandrill_Init( $this->getInvokeArg('mandrillKey'));
				$template_name = 'wlcm-email';
				$template_content = $mailData;
				$message = array(
						'subject'    => $this->view->translate('Wachtwoord Wijziging'),
						'from_email' => $emailFrom,
						'from_name'  => $this->view->translate('Forgot-Password'),
						'to'         => array(array('email'=>$this->getRequest()->getParam("email"),'name'=> 'Member')) ,
						'inline_css' => true
				);
				$mandrill->messages->sendTemplate($template_name, $template_content, $message);
				
				//Update table visitor field flag = true
				$v = Visitor::updatePasswordRequest($result["id"], 0);
				echo "emailsent";
				die();
			} else {
				echo "emailnotfound";
				die();
			}

		}
	}
	/**
	 * reset password by email link
	 *
	 * @version 1.0
	 */
	public function resetpasswordAction() {
	
		$headTitle = $this->view->translate("Members Only ");
    	$this->view->headTitle($headTitle);
		
		$params = $this->_request->getParams();
		$user_id = @(base64_decode($params["forgotid"]));
		
		
		$user_id = FrontEnd_Helper_viewHelper::sanitize( $user_id) ;
		// find visitor exist  based on $user_id
		$visitor = Doctrine_Core::getTable("Visitor")->find($user_id);
		
		if($visitor['changepasswordrequest']) {
			$flash = $this->_helper->getHelper('FlashMessenger');
		    $message = $this->view->translate('Password Reset Link has already been used!');
		    $flash->addMessage(array('success' => $message));
		    $flash = $this->_helper->getHelper('FlashMessenger');
		    $message = $flash->getMessages();
		    $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
		    $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';
		    $this->view->flag = false;
		}else{
			$this->view->flag = true;
		}
		$this->view->forgotid = $user_id;

	}

	public function resetpwdAction() {

		$headTitle = $this->view->translate("Members Only ");
    	$this->view->headTitle($headTitle);
		
		$params = $this->_request->getParams();
		$user_id = $params["forgotid"];
		if ($this->getRequest()->isPost()) {
			$newpwd = $params["pwd"];
			$res = Visitor::updatefrontendPassword($user_id, $newpwd);
			if ($res) {

				$flash = $this->_helper->getHelper('FlashMessenger');
				$message = $this->view
						->translate('Uw wachtwoord is gewijzigd.');
				$flash->addMessage(array('success' => $message));

				if (!Auth_VisitorAdapter::hasIdentity()) {

					echo HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login');
					//update visitor field flag true;
					$v = Visitor::updatePasswordRequest($user_id, 1);
					
				} else {
					echo HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login'). '/' .FrontEnd_Helper_viewHelper::__link('profiel');
					//update visitor field flag false;
					$v = Visitor::updatePasswordRequest($user_id, 1);
				}
				die;
			} else {

				echo "usernotfound";
				die();

			}
		}
	}
	/**
	 * unset all cookies and session of the logged user
	 *
	 * @version 1.0
	 */
	public function logoutAction() {
		
		$headTitle = $this->view->translate("Members Only ");
    	$this->view->headTitle($headTitle);
		
		//unset the session
		Auth_VisitorAdapter::clearIdentity();
		
		
		setcookie('kc_unique_user_id', "", time() - 3600, '/');
		unset($_COOKIE['kc_unique_user_id']);

		
		# set reponse header X-Nocache used for varnish
		$this->getResponse()->setHeader('X-Nocache', 'no-cache');
		
		/*setcookie('kc_session_active', "", time() - 3600, '/');
		unset($_COOKIE['kc_session_active']);
		session_destroy();*/
	
		$module = $this->getRequest()->getParam('lang');
		$this->_helper->redirector('index','index',$module);
		//$namespace = new Zend_Session_Namespace('Zend_Auth');
		//$namespace->setExpirationSeconds(14400);
	}
	/**
	 * redirecttosignup
	 * 
	 * Show light box for login or signup
	 * return phtml of current action 
	 *
	 * @author kraj
	 * @version 1.0
	 */
	public function redirecttosignupAction() {
		
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
	public function updateuserdataAction() {
		$headTitle = $this->view->translate("Members Only ");
    	$this->view->headTitle($headTitle);
		
		if (Auth_VisitorAdapter::hasIdentity()) {
			$params = $this->getRequest()->getParams();
			$params['userId'] = Auth_VisitorAdapter::getIdentity()->id;
			$userdetail = Visitor::updateVisitor($params);
			
			 
			if( $userdetail)
			{

				$subsStatus = $this->getRequest()->getParam('currentSubscriptionStatus');
				$newStatus =  $this->getRequest()->getParam('weekly') == 'on'  ? 1 : 0  ;

				# chekc if your has not update newsletter status	
				if($subsStatus == $newStatus)
				{
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
	public function directloginAction() {
		
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
	public function directloginunsubscribeAction() {
	
		
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
	public function usermenuAction(){
		$this->_helper->layout()->disableLayout();
	}
	// Returns the footer part for the user by fetching the partial which checks if a user is logged in.
	public function userfooterAction(){
		$this->_helper->layout()->disableLayout();
	}	
	// Returns the widget part for the user by fetching the partial which checks if a user is logged in.
	public function userwidgetAction(){
		$this->_helper->layout()->disableLayout();
	}		
	// returns the signup bar on home
	public function usersignupAction(){
		$this->_helper->layout()->disableLayout();
	}
	// returns the dummy page bar on home
	public function dummypageAction(){

		$this->view->layout()->robotKeywords = 'noindex, nofollow' ;
		
	}
	public function dummyboxAction(){
		
		$this->view->layout()->robotKeywords = 'noindex, nofollow' ;
		
		$headTitle = $this->view->translate("Members Only ");
		$this->view->headTitle($headTitle);
		$this->_helper->layout()->disableLayout();
	}
}
