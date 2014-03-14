<?php
/**
 * all the regarding Email Lightbox functionality
 * @author sunny patial
 *
 */
class Admin_AccountsettingController extends Zend_Controller_Action
{
	public $dataShopName = array();
	public $dataOfferName = array();
	public $dataShopImage = array();
	public $expDate = array();
	public $shopPermalink = array();

	public $category = array();
	public $dataShopNameCat = array();
	public $dataOfferNameCat = array();
	public $dataShopImageCat = array();
	public $expDateCat = array();
	public $shopPermalinkCat = array();
	public $recipientMetaData  = array();

	public $headerMail = array();
	public $loginLinkAndData = array();
	public $to = array();
	public $staticContent = array();
	public $headerContent = array();
	public $footerContent = array();

	# holds settings regarding user rights
	protected $_settings = false ;
	/**
	 * check authentication before load the page
	 * @see Zend_Controller_Action::preDispatch()
	 * @author sunny patial
	 * @version 1.0
	 */
	public function preDispatch() {


		$conn2 = BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
		$params = $this->_getAllParams();
		if (!Auth_StaffAdapter::hasIdentity()) {
			$referer = new Zend_Session_Namespace('referer');
			$referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$this->_redirect('/admin/auth/index');
		}
		BackEnd_Helper_viewHelper::closeConnection($conn2);
		$this->view->controllerName = $this->getRequest()->getParam('controller');
		$this->view->action = $this->getRequest()->getParam('action');


		# redirect of a user don't have any permission for this controller
		$sessionNamespace = new Zend_Session_Namespace();
		$this->_settings  = $sessionNamespace->settings['rights'] ;


		if(! $this->getRequest()->isXmlHttpRequest())
		{

			# add action as new case which needs to be viewed by other users
			switch(strtolower($this->view->action))
			{
				case 'emailcontent':
				case 'mandrill':
				break;
				default:
					if( $this->_settings['system manager']['rights'] != '1' )
					{
						$this->_redirect('/admin/auth/index');
					}

			}

		} else {

			# add action as new case which needs to be viewed by other users
			switch(strtolower($this->view->action))
			{
				case 'madrill':
				case 'changemailconfirmation':
			    case 'saveemailcontent' :
		    	case 'email-header-footer' :
				break;
				default:
					if( $this->_settings['system manager']['rights'] != '1' )
					{
		    			$this->getResponse()->setHttpResponseCode(404);
   						$this->_helper->redirector('index' , 'index' , null ) ;
					}

			}

		}

	}
	public function init()
    {
    	$flash = $this->_helper->getHelper('FlashMessenger');
    	$message = $flash->getMessages();
    	$this->view->messageSuccess = isset($message[0]['success']) ?
    	$message[0]['success'] : '';
    	$this->view->messageError = isset($message[0]['error']) ?
    	$message[0]['error'] : '';
        /* Initialize action controller here */
    }

    /**
     * get stores to step 2 create account from database
     * get Codes for No more free logins from database
     * @author sunny patial
     * @version 1.0
     */
    public function indexAction()
    {
        // action body
    	$store_data = Signupfavoriteshop::getalladdstore();
    	$this->view->store_data = $store_data;
    	$data = Signupcodes::getfreeCodelogin();
    	$this->view->codelogindata = $data;
    	$maxacc_data = Signupmaxaccount::getAllMaxAccounts();
    	$this->view->maxacc_data = $maxacc_data;
    }



    /**
     * Change email confimation status on click on yes no button
     */
    public function changemailconfirmationAction(){
    	$status=$this->getRequest()->getParam("status");
    	Signupmaxaccount::changeEmailConfimationSetting($status);
    	die;
    }

    /**
     * mandrill
     *
     * This function initialize the mandrill and send the mail using mandrill template
     *
     * @author cbhopal
     * @version 1.0
     */
    public function mandrillAction() {


    	if ($this->_request->isPost())
    	{
    		//add the flash mesage that the newsletter has been sent
    		$flash = $this->_helper->getHelper('FlashMessenger');

    		$isScheduled = $this->getRequest()->getParam("isScheduled" , false);

			if($isScheduled)
			{
	    		if(Signupmaxaccount::saveScheduledNewsletter( $this->getRequest()))
	    		{
	    			$flash->addMessage(array('success' => $this->view->translate('Newsletter has been successfully scheduled')));
	    		} else {
	    			$flash->addMessage(array('error' => $this->view->translate('There is some problem in your data') ));
	    		}

	    		$this->_helper->redirector('emailcontent' , 'accountsetting' , null ) ;
			}

			# update current scheduled status to sent
			Signupmaxaccount::updateNewsletterSchedulingStatus();

 	    	if(LOCALE == '')
	    	{
	    		$imgLogoMail = "<a href=". rtrim(HTTP_PATH_FRONTEND , '/') ."><img src='".HTTP_PATH."public/images/HeaderMail.gif'/></a>";
	    		$siteName = "Kortingscode.nl";
	    	} else	{
		    	$imgLogoMail = "<a href=". rtrim(HTTP_PATH_FRONTEND , '/') ."><img src='".HTTP_PATH."public/images/flipit-welcome-mail.jpg'/></a>";
		    	$siteName = "Flipit.com";
	    	}

	    	set_time_limit ( 10000 );
	    	ini_set('max_execution_time',115200);
	    	ini_set("memory_limit","1024M");

	  	    //get offers from top ten popular shops and top one cateory as in homepage

	    	$voucherflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularvaouchercode_list');

    		//key not exist in cache

    		if($voucherflag){

    			# get 10 popular vouchercodes for news letter	
                $topVouchercodes = FrontEnd_Helper_viewHelper::gethomeSections("popular", 10) ;
    			$topVouchercodes =  FrontEnd_Helper_viewHelper::fillupTopCodeWithNewest($topVouchercodes,10);

    	   	} else {
    			$topVouchercodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularvaouchercode_list');
    		}

    		$categoryflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularcategory_list');

    		//key not exist in cache

    		if($categoryflag){

				$topCategories = array_slice(FrontEnd_Helper_viewHelper::gethomeSections("category", 10),0,1);

				FrontEnd_Helper_viewHelper::setInCache('all_popularcategory_list', $topCategories);

    		} else {

    			$topCategories = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularcategory_list');

    		}


	    	//Start get email locale basis
	    	$email_data = Signupmaxaccount::getAllMaxAccounts();
	    	$emailFrom  = $email_data[0]['emailperlocale'];
	    	$emailSubject  = $email_data[0]['emailsubject'];
	    	$senderName  = $email_data[0]['sendername'];
	    	//End get email locale basis



	    	//call functions to set the needed data in global arrays
	    	$voucherCodesData = BackEnd_Helper_viewHelper::getTopVouchercodesDataMandrill($topVouchercodes);

	    	$this->getVouchercodesOfCategories($topCategories);
	    	$this->getDirectLoginLinks();
	    	$this->getHeaderFooterContent();


	    	//set the header image for mail
	    	$this->headerMail = array(array('name' => 'headerMail',
	    								    'content' => $imgLogoMail
	    			                 ),
					    			array('name' => 'headerContent',
					    					'content' => $this->headerContent
					    			),
					    			array('name' => 'footerContent',
					    					'content' => $this->footerContent
					    			));

	    	//set the static content of mail so that we can change the text in PO Edit
	    	$this->staticContent = array(
					    			array('name' => 'websiteName',
					    					'content' => $siteName
					    			),
					    			array('name' => 'unsubscribe',
					    					'content' => $this->view->translate('Uitschrijven')
					    			),
					    			array('name' => 'editProfile',
					    					'content' => $this->view->translate('Wijzigen profiel')
					    			),
					    			array('name' => 'contact',
					    					'content' => $this->view->translate('Contact')
					    			),
					    			array('name' => 'contactLink',
					    					'content' => HTTP_PATH_FRONTEND . 'info/contact'
					    			),
					    			array('name' => 'moreOffersLink',
					    					'content' => HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('populair')
					    			),
					    			array('name' => 'moreOffers',
					    					'content' => $this->view->translate('Bekijk meer van onze top aanbiedingen') . ' >'
					    			)
					    	);

	    	//merge all the arrays into single array
	    	$data = array_merge($voucherCodesData['dataShopName'],
	    			$voucherCodesData['dataOfferName'],
	    			$voucherCodesData['dataShopImage'],
	    			$voucherCodesData['expDate'],
	    			$this->headerMail, $this->dataShopNameCat,
	    			$this->dataOfferNameCat, $this->dataShopImageCat,
	    			$this->expDateCat, $this->category
	    	);

	    	//merge the permalinks array and static content array into single array
	    	$dataPermalink = array_merge($voucherCodesData['shopPermalink'], $this->shopPermalinkCat,
	    								 $this->staticContent);

	    	//initialize mandrill with the template name and other necessary options
	    	$mandrill = new Mandrill_Init( $this->getInvokeArg('mandrillKey'));
	    	$template_name = $this->getInvokeArg('newsletterTemplate');
	    	$template_content = $data;

	    	$message = array(
	    			'subject'    => $emailSubject ,
	    			'from_email' => $emailFrom,
	    			'from_name'  => $senderName,
	    			'to'         => $this->to ,
	    			'inline_css' => true,
	    			"recipient_metadata" =>   $this->recipientMetaData ,
	    			'global_merge_vars' => $dataPermalink,
	    			'merge_vars' => $this->loginLinkAndData
	    	);


	    	try {

				$mandrill->messages->sendTemplate($template_name, $template_content, $message);
				$message = $this->view->translate('Newsletter has been sent successfully');

	    	} catch (Mandrill_Error $e) {

				//echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
	 			$message = $this->view->translate('There is some problem in your data');

	    	}

	    	//send newsletter

	    	$flash->addMessage(array('success' => $message));

	    	//redirect to account setting controller after mail sent
	    	$this->_helper->redirector('emailcontent' , 'accountsetting' , null ) ;
    	} else {

    		$this->_helper->redirector('index' , 'index' , null ) ;
    	}
    	die;

    }



    /**
     * getVouchercodesOfCategories
     *
     * This function loops the category data and set the needed data in gloabal arrays
     *
     * @param array $topCategories
     * @author cbhopal
     * @version 1.0
     */
    public function getVouchercodesOfCategories($topCategories)
    {
    	//set the logo for category, category name and more category link
    	//if it exists or not in $category array
        if(count($topCategories[0]['category']['categoryicon']) > 0):
    	    if(@file_exists(ROOT_PATH.$topCategories[0]['category']['categoryicon']['path'] .'thum_medium_'. $topCategories[0]['category']['categoryicon']['name']) && $topCategories[0]['category']['categoryicon']['name']!=''):
    	        $img = PUBLIC_PATH_LOCALE.$topCategories[0]['category']['categoryicon']['path'].'thum_medium_'. $topCategories[0]['category']['categoryicon']['name'];
    	    else:
    	        $img = PUBLIC_PATH_LOCALE."images/NoImage/NoImage_70x60.png";
    	    endif;
    	else:
    	    $img = PUBLIC_PATH_LOCALE."images/NoImage/NoImage_70x60.png";
    	endif;
    	$permalinkCatMainEmail = HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('categorieen') .'/'. $topCategories[0]['category']['permaLink'] . '?utm_source=transactional&utm_medium=email&utm_campaign='.date('d-m-Y');
    	$this->category = array(array('name' => 'categoryImage',
					    			  'content' => "<a style='color:#333333; text-decoration:none;' href='$permalinkCatMainEmail'><img src='".$img."'/></a>"
					    	    ),
				    			array('name' => 'categoryName',
				    					'content' => $this->view->translate('Populairste categorie:') ." <a style='color:#333333; text-decoration:none;' href='$permalinkCatMainEmail'>". $topCategories[0]['category']['name'] ."</a>"
				    			),
				    			array('name' => 'categoryNameMore',
			    					  'content' => '<a href="'.$permalinkCatMainEmail.'" style="font-size:12px; text-decoration:none; color:#0B7DC1;" >' . $this->view->translate('Bekijk meer van onze') ." ". $topCategories[0]['category']['name'] ." ". $this->view->translate('aanbiedingen') . ' > </a>'
			    				));

    	//get three voucher codes in top one category from homepage
    	$vouchers = array_slice(Category::getCategoryVoucherCodes($topCategories[0]['categoryId']),0,3);

    	foreach ($vouchers as $key => $value) {

    		$permalinkCatEmail = HTTP_PATH_FRONTEND . $value['shop']['permalink'].'?utm_source=transactional&utm_medium=email&utm_campaign='.date('d-m-Y');
    		//set $dataShopNameCat array with the title of shop in this category
    		$this->dataShopNameCat[$key]['name'] = "shopTitleCat_".($key+1);
    		$this->dataShopNameCat[$key]['content'] = "<a style='color:#333333; text-decoration:none;' href='$permalinkCatEmail'>".$value['shop']['name']."</a>";

    		//set $dataOfferNameCat array with the title of offer in this category
    		$this->dataOfferNameCat[$key]['name'] = "offerTitleCat_".($key+1);
    		$this->dataOfferNameCat[$key]['content'] = $value['title'];

    		//set the logo for shop in this category if it exists or not in $dataShopImageCat array
    		if(count($value['shop']['logo']) > 0):
    		    if(@file_exists(ROOT_PATH.$value['shop']['logo']['path'] .'thum_medium_store_'. $value['shop']['logo']['name']) && $value['shop']['logo']['name']!=''):
    		        $img = PUBLIC_PATH_LOCALE.$value['shop']['logo']['path'].'thum_medium_store_'. $value['shop']['logo']['name'];
    		    else:
    		        $img = PUBLIC_PATH_LOCALE."images/NoImage/NoImage_200x100.jpg";
    		    endif;
    		else:
    		    $img = PUBLIC_PATH_LOCALE."images/NoImage/NoImage_200x100.jpg";
    		endif;

    		$this->dataShopImageCat[$key]['name'] = 'shopLogoCat_'.($key+1);
    		$this->dataShopImageCat[$key]['content'] = "<a href='$permalinkCatEmail'><img src='$img'></a>";

    		//set the expiry date for offer in this category in $expDateCat array
    		$expiryDate = new Zend_Date($value['endDate']);
    		$this->expDateCat[$key]['name'] = 'expDateCat_'.($key+1);
    		$this->expDateCat[$key]['content'] = $this->view->translate('Verloopt op:') ." ". $expiryDate->get(Zend_Date::DATE_MEDIUM);

    		//set the permalink for shop in this category in $shopPermalinkCat array
    		$this->shopPermalinkCat[$key]['name'] = 'shopPermalinkCat_'.($key+1);
    		$this->shopPermalinkCat[$key]['content'] = $permalinkCatEmail;
    	}
    }

    /**
     * getDirectLoginLinks
     *
     * This function makes the URL for direct login links for each users
     *
     * @author cbhopal
     * @version 1.0
     */
    public function getDirectLoginLinks()
    {
    	$email_data = Signupmaxaccount::getAllMaxAccounts();
    	$testEmail = $this->getRequest()->getParam('testEmail');
    	$dummyPass = MD5('12345678');
    	$send = $this->getRequest()->getParam('send');
    	$visitorData = array();
    	$visitorMetaData = array();
    	$toVisitorArray = array();
 
    	if(isset($send) && $send == 'test'){

			$getTestEmaildata =  Visitor::getuserpwddetail($testEmail);

    		$key = 0;
    		$visitorData[$key]['rcpt'] = $testEmail;
    		$visitorData[$key]['vars'][0]['name'] = 'loginLink';
    		$visitorData[$key]['vars'][0]['content'] =  HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link("login") . "/" .FrontEnd_Helper_viewHelper::__link("directlogin") . "/" . base64_encode($getTestEmaildata[0]['email']) ."/". $getTestEmaildata[0]['password'];
    		$visitorData[$key]['vars'][1]['name'] = 'loginLinkWithUnsubscribe';
    		$visitorData[$key]['vars'][1]['content'] = HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link("login") . "/" .FrontEnd_Helper_viewHelper::__link("directloginunsubscribe") . "/" . base64_encode($testEmail) ."/". $dummyPass;

    		$toVisitorArray[$key]['email'] = $testEmail;
    		$toVisitorArray[$key]['name'] = 'Member';
    		$this->loginLinkAndData = $visitorData;//set the visitor data in $loginLinkAndData array
    		$this->to = $toVisitorArray;

    	} else {



    		# to make newsletters only be sent by admin or super admin user
    		if($this->_settings['administration']['rights']  == 1){


				//retrieve the visitors with status, active and weeklynewsletter true
				$visitors = new Visitor();

				$visitors = $visitors->getVisitorsToSendNewsletter();

				//initialize the mandrill to retrieve the data of the users to whom we have sent mails
		    	$mandrill = new Mandrill_Init( $this->getInvokeArg('mandrillKey'));
		    	$getUserDataFromMandrill = $mandrill->users->senders();

		    	//set the profile inactive if any user has hard bounce or soft bounce
		    	foreach ($getUserDataFromMandrill as $key => $value) {
		    		if($value['soft_bounces'] >= 6 || $value['hard_bounces'] >= 2 ){
						$updateActive = Doctrine_Query::create()->update('Visitor')->set('active',0)->where("email = '".$value['address']."'")->execute();
		    		}
		    	} 

		    	//loop the visitors and generate the links for unsubscribe and edit profile
		    	foreach ($visitors as $key => $value) {

		    		# ADD REFERRAL KEYWORDS for mandril (recipient MetaData)
		    		$keywords ='' ;

		    		foreach ($value['keywords'] as $k => $word) {

		    			$keywords .= $word['keyword'] . ' ';
		    		}

		    		$visitorData[$key]['rcpt'] = $value['email'];
		    		$visitorData[$key]['vars'][0]['name'] = 'loginLink';


		    		$visitorMetaData[$key]['rcpt'] = $value['email'];
		            $visitorMetaData[$key]['values']['referrer'] = trim($keywords) ;
		           // $visitorMetaData[$key]['values']['url'] = '';

		    		$visitorData[$key]['vars'][0]['content'] = HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link("login") . "/" .FrontEnd_Helper_viewHelper::__link("directlogin") . "/" . base64_encode($value['email']) ."/". $value['password'];

		    		$visitorData[$key]['vars'][1]['name'] = 'loginLinkWithUnsubscribe';
		    		$visitorData[$key]['vars'][1]['content'] = HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link("login") . "/" .FrontEnd_Helper_viewHelper::__link("directloginunsubscribe") . "/" . base64_encode($value['email']) ."/". $value['password'];

		    		$toVisitorArray[$key]['email'] = $value['email'];
		    		$toVisitorArray[$key]['name'] = !empty($value['firstName']) ? $value['firstName'] : 'Member';

		    	}



		    	$this->recipientMetaData = $visitorMetaData; // set referer for each user;
		    	$this->loginLinkAndData = $visitorData;//set the visitor data in $loginLinkAndData array
		    	$this->to = $toVisitorArray;//set the users email to which mails has been sent $to array
    		}
    	}
    }


    /**
     * emailHeaderFooter
     *
     * save email header/footer content
     *
     * @author Surinderpal Singh
     */
    public function emailHeaderFooterAction()
    {
    	# sanitize data
    	$data = mysql_escape_string(
    			BackEnd_Helper_viewHelper::stripSlashesFromString(
    					$this->getRequest()->getParam('data'))) ;


    	# check tepmlete type
		switch($this->getRequest()->getParam('template'))
    	{
    		case 'email-header':
    			# update headet template content
    			Signupmaxaccount::updateHeaderContent($data);
			break;

    		case 'email-footer':
    			# update footer template content
    			Signupmaxaccount::updateFooterContent($data);
			break;
    	}

    	die ;
    }
    public function getHeaderFooterContent()
    {
    	$data = Signupmaxaccount::getEmailHeaderFooter();
    	$this->headerContent = $data['email_header'];
    	$this->footerContent = $data['email_footer'];
    }

    public function emailcontentAction()
    {
    	$data = Signupmaxaccount::getAllMaxAccounts();
    	$this->view->data = $data;

    	$this->view->rights = $this->_settings['administration'];
    	$this->view->timezones_list = Signupmaxaccount::$timezones;
    }

    public function saveemailcontentAction()
    {
    	# sanitize data
    	$val = mysql_escape_string(
    						BackEnd_Helper_viewHelper::stripSlashesFromString(
    									$this->getRequest()->getParam('val'))) ;

    	switch ($this->getRequest()->getParam('name')){
    		case 'senderEmail':
    			$senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
								->set('emailperlocale','"'.	$val .'"')->execute();
    		break;
    		case 'senderName':
    			$senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
			    			->set('sendername','"'. $val .'"')->execute();
    		break;
    		case 'emailSubject':
    			$senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
    					 ->set('emailsubject','"'. $val .'"')->execute();
    		break;
    		case 'testEmail':
    			$senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
						  ->set('testemail','"'.$val.'"')->execute();
    		break;

    	}

    	die;
    }


    public function saveTestimonialsAction()
    {


    	if($this->_settings['content']['rights'] != '1')
    	{
    		$this->getResponse()->setHttpResponseCode(404);
    		echo $this->_helper->json('This page does not exist');

    	}

    	# sanitize data
    	$content =  mysql_escape_string(
    					 BackEnd_Helper_viewHelper::stripSlashesFromString(
    					 		$this->getRequest()->getParam('content') ));


    	switch ($this->getRequest()->getParam('type')){
    		case 'testimonial1':
    			$senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
    			->set('testimonial1', '"'. $content .'"')
    			->execute();
    			break;
    		case 'testimonial2':
    			$senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
    			->set('testimonial2','"'. $content.'"')
    			->execute();
    			break;
    		case 'testimonial3':
    			$senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
    			->set('testimonial3','"'. $content .'"')
    			->execute();
    			break;
    		case 'showTestimonial':
    			$senderEmail = Doctrine_Query::create()->update('Signupmaxaccount')
    			->set('showTestimonial', $content)
    			->execute();
    			break;

    	}
    	die;

    }

    public function totalRecepientsAction()
    {

     	if($this->_settings['content']['rights'] != '1')
    	{
    		$this->getResponse()->setHttpResponseCode(404);
    		echo $this->_helper->json('This page does not exist');

    	}


    	$visitors = Doctrine_Query::create()->select('count(id) as recepients')
			    	->from('Visitor v')
			    	->where('status = 1')
			    	->andWhere('active = 1')
			    	->andWhere('weeklyNewsLetter = 1')
			    	->fetchOne(null, Doctrine::HYDRATE_ARRAY);


    	echo $this->_helper->json(array('recepients' => $visitors['recepients']), true);
    }

}

