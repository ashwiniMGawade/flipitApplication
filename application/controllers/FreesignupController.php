<?php

/**
 *
 * @author
 * @version
 */

require_once 'Zend/Controller/Action.php';

class FreesignupController extends Zend_Controller_Action
{

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
	 * The default action - show the home page
	 */

    public function indexAction()
    {
    	$headTitle = $this->view->translate("Members Only ");
    	$this->view->headTitle($headTitle);
    }
    /**
     * Show step 1 of signup
     *
     * @author cbhopal modify by kraj
     * @version 1.0
     */
    public function step1Action() {



    	# display error message in case invalid email address
    	$flash  = $this->_helper->getHelper('FlashMessenger');
    	$message = $flash->getMessages();
    	$this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : null ;






    	$this->view->controllerName = $this->getRequest()->getParam('controller');
    	$this->view->action 		= $this->getRequest()->getParam('action');

    	# get cononical link
    	$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
    	$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononicalForSignUp($permalink) ;

    	$headTitle = $this->view->translate("Members Only ");
    	$this->view->headTitle($headTitle);

    	//cal function and get status of email cofimation step
    	$this->view->emailStepStatus = Signupmaxaccount::getemailConfirmationStatus();


        //cal function and get status of testimonials
    	$this->view->testimonials = Signupmaxaccount::getTestimonials();



    }
    /**
     * Show step 2 of signup
     *
     * @author cbhopal modify by kraj
     * @version 1.0
     */
    public function step2Action() {

    	$this->view->controllerName = $this->getRequest()->getParam('controller');
    	$this->view->action 		= $this->getRequest()->getParam('action');

    	# get cononical link
    	$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
    	$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononicalForSignUp($permalink) ;

    	$headTitle = $this->view->translate("Members Only ");
    	$this->view->headTitle($headTitle);

    	if($this->getRequest()->isPost()){
    		$params=$this->_request->getParams();

    		//cal function and get status of email cofimation step
    		$emailConfirmationStatus = Signupmaxaccount::getemailConfirmationStatus();

    		if($emailConfirmationStatus==false) {
    			//redirect to step3 directly mean skip step 2
    		    $url= HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('inschrijven') . '/' . FrontEnd_Helper_viewHelper::__link('stap2') . '/' . base64_encode($params['emailAddress']);
    	        $this->_redirect($url);

    		} else {
	    		$this->view->email = $params['emailAddress'];

		    	$html = new Zend_View();
		    	$html->setScriptPath(APPLICATION_PATH . '/views/scripts/freesignup');
		    	$html->assign('email',$params['emailAddress']);
		    	$bodyText = $html->render('confirmemail.phtml');

		    	$recipents = array("to" => $params['emailAddress']);
		    	$subject = $this->view->translate("Welkom bij Kortingscode.nl");
		    	$body = $bodyText;
		    	$sendEmail = BackEnd_Helper_viewHelper::SendMail($recipents,$subject, $body);
		    }
    	}else{
    		$url=HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('inschrijven') .'/'. FrontEnd_Helper_viewHelper::__link('stap1');
    		$this->_redirect($url);
    	}

    }
    /**
     * Show step 3 of signup
     *
     * @author cbhopal modify by kraj
     * @version 1.0
     */
    public function step3Action() {

    	$this->view->controllerName = $this->getRequest()->getParam('controller');
    	$this->view->action 		= $this->getRequest()->getParam('action');


    	# get cononical link
    	$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
    	$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononicalForSignUp($permalink) ;

    	$headTitle = $this->view->translate("Members Only ");
    	$this->view->headTitle($headTitle);

    	//cal function and get status of email cofimation step
    	$this->view->emailStepStatus = Signupmaxaccount::getemailConfirmationStatus();

    	$params=$this->_request->getParams();
    	if(isset($params['mail']) && $params['mail'] != ''){
    		$this->view->email = $params['mail'];

    	}else{
    		$url=HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('inschrijven') .'/'. FrontEnd_Helper_viewHelper::__link('stap1');
    		$this->_redirect($url);
    	}

    }
    /**
     * Show step 4 of signup
     *
     * @author cbhopal modify by kraj
     * @version 1.0
     */
    public function step4Action() {



   		# get cononical link
    	$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
    	$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononicalForSignUp($permalink) ;

    	$headTitle = $this->view->translate("Members Only ");
    	$this->view->headTitle($headTitle);

    	//cal function and get status of email cofimation step
    	$this->view->emailStepStatus = Signupmaxaccount::getemailConfirmationStatus();
    	$data = $this->getRequest()->getParams();

    	if($this->getRequest()->isPost()){
    		try {
    			$params = '';
    			$domain = $_SERVER['HTTP_HOST'];

    			$imgLogoMail = "<a href=".HTTP_PATH_LOCALE."><img src='".HTTP_PATH."public/images/flipit-welcome-mail.jpg'/></a>";

    			$siteName = "Flipit.com";

    			$siteUrl = HTTP_PATH_LOCALE;

    			if($domain == "kortingscode.nl" || $domain == "www.kortingscode.nl")
    			{

    				$imgLogoMail = "<a href=".HTTP_PATH_LOCALE."><img src='".HTTP_PATH."public/images/HeaderMail.gif'/></a>";

    				$siteName = "Kortingscode.nl";

    			}

    			$lastid = Visitor::addfrontVisitor($data) ;
    			if(! $lastid  )
    			{

    				$flash = $this->_helper->getHelper('FlashMessenger');
    				$message = $this->view->translate('Please enter a valid email address');
    				$flash->addMessage(array('error' => $message ));

    				$url=HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('inschrijven') .'/'. FrontEnd_Helper_viewHelper::__link('stap1');
    				$this->_redirect($url);

    				die ;

    			}

    			$userdetail = Visitor::getuserdetail($lastid);

    			# gte top 5 vouchercodes
    			$topVouchercodes = FrontEnd_Helper_viewHelper::gethomeSections("popular", 5);

    			//call functions to set the needed data in global arrays
    			$voucherCodesData = BackEnd_Helper_viewHelper::getTopVouchercodesDataMandrill($topVouchercodes);


    			$mailData = array(array('name'=>'headerWelcome',
    									'content'=>$imgLogoMail
    							  ),
    						array('name'=>'bestRegards',
	    							'content'=>$this->view->translate('Beste nieuwsbrieflezer,')
	    					),
	    					array('name'=>'centerContent',
	    							'content'=>$this->view->translate('Vanaf nu ontvang je onze wekelijkse nieuwsbrief met de beste kortingscodes.')
	    					),
	    					array('name'=>'bottomContent',
	    							'content'=>$this->view->translate('Bedankt').', <br/>'.$siteName

	    					),
	    					array('name'=>'copyright',
	    							'content'=>$this->view->translate('Copyright &copy; 2013').' '.$siteName.'. '.$this->view->translate('All Rights Reserved.')
	    					),
	    					array('name'=>'address',
	    							'content'=>$this->view->translate("U ontvangt deze nieuwsbrief omdat u ons uw toestemming heeft gegeven om u op de hoogte te houden van onze laatste").
	    										'<br/>' . $this->view->translate("acties."). '<a href='.$siteUrl.' style="color:#ffffff; padding:0 2px;">'.$siteName.'</a>' . $this->view->translate('is een onderdeel van Imbull, Weteringschans 120, 1017XT Amsterdam - KvK 34339618')
	    					),
	    					array('name'=>'logincontact',
	    						  'content'=>'<a style="color:#ffffff; padding:0 4px; text-decoration:none;" href="'.HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login').'/'.FrontEnd_Helper_viewHelper::__link('directlogin'). "/" . base64_encode($userdetail[0]['email']) ."/". $userdetail[0]['password'].'">'.$this->view->translate('My Profile').'</a>'
  	    					));




    			$poupularTitle = array( array('name' => 'poupularTitle',
    							'content' => $this->view->translate('Top 5 kortingscodes :')
    							));



    			//merge all the arrays into single array
    			$data = array_merge($voucherCodesData['dataShopName'],
	    			$voucherCodesData['dataOfferName'],
	    			$voucherCodesData['dataShopImage'],
	    			$voucherCodesData['expDate'],$mailData,$poupularTitle);

    			//merge the permalinks array and static content array into single array
    			$dataPermalink = array_merge($voucherCodesData['shopPermalink']);

    			$email_data = Signupmaxaccount::getemailmaxaccounts();
    			$emailFrom  = $email_data[0]['emailperlocale'];

    			$mandrill = new Mandrill_Init( $this->getInvokeArg('mandrillKey') );
    			$template_name = $this->getInvokeArg('welcomeTemplate');
    			$template_content = $data;
    			$message = array(
    					'subject'    => $this->view->translate('Welcome e-mail subject'),
    					'from_email' => $emailFrom,
    					'from_name'  => $this->view->translate('welcome'),
    					'to'         => array(array('email'=>$userdetail[0]['email'],'name'=>!empty($userdetail[0]['firstName']) ? $userdetail[0]['firstName'] : 'Member')) ,
    					'inline_css' => true,
    					'global_merge_vars' => $dataPermalink
    			);
    			$mandrill->messages->sendTemplate($template_name, $template_content, $message);

  				$username = $userdetail[0]["email"];
    			$password = $userdetail[0]["password"];
    			$data_adapter = new Auth_VisitorAdapter($username, $password);

    			$auth=Zend_Auth::getInstance();
    			$auth->setStorage(new Zend_Auth_Storage_Session('front_login'));
    			$result=$auth->authenticate($data_adapter);

    			if(Auth_VisitorAdapter::hasIdentity()){
    				$userid=Auth_VisitorAdapter::getIdentity()->id;
     				$obj=new Visitor();
     				$obj->updateLoginTime($userid);

    				//setcookie('kc_session_active',1, time() + 3600, '/');
    				setcookie('kc_unique_user_id', $userid, time() + 1800, '/');

    			}
    		} catch (Exception $e) {
    			$this->view->exception = $this->view->translate("Je hebt een account bij Kortingscode.nl reeds");
    		}

    	} else {
    		$url = HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('login');
    		$this->_redirect($url);
    	}
    }

    public function checkuserAction(){
    	$u =  new Visitor();
    	$cnt  = intval($u->checkDuplicateUser($this->_getParam('emailAddress'),$this->_getParam('id')));
    	if($cnt > 0)
    	{
    		echo Zend_Json::encode(false);

    	} else {

    		echo Zend_Json::encode(true);
    	}

    	die();
    }

    public function confirmemailAction()
    {

    }

}
