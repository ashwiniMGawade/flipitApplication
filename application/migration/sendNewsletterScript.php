<?php
/**
 * Send scheduled newsletter of all countries
 *
 * @author Sp Singh
 *
 */
class SendNewsletter {

	protected $_localePath = '/';
	protected $_hostName = '';
	protected $_trans = null;
	protected $_locale = '';
	protected $_siteName = null;
	protected $_logo = null;
	protected $_linkPath = null;
	protected $_publicPath = null;


	# visitors data
	protected $_recipientMetaData  = array();
	protected $_loginLinkAndData = array();
	protected $_to = array();

	# mandrill data
	protected $_mandrillKey = "";
	protected $_template = "" ;
	protected $_rootPath = "" ;

	
	function __construct() {


		ini_set('memory_limit', '-1');

		set_time_limit(0);

		// Define path to application directory
		defined('APPLICATION_PATH')
		|| define('APPLICATION_PATH',
				dirname(dirname(__FILE__)));

		defined('LIBRARY_PATH')
		|| define('LIBRARY_PATH', realpath(dirname(dirname(dirname(__FILE__))). '/library'));

		defined('DOCTRINE_PATH') || define('DOCTRINE_PATH', LIBRARY_PATH . '/Doctrine');

		// Define application environment
		defined('APPLICATION_ENV')
		|| define('APPLICATION_ENV',
				(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
						: 'production'));


		//Ensure library/ is on include_path
		set_include_path(
				implode(PATH_SEPARATOR,
						array(realpath(APPLICATION_PATH . '/../library'),
								get_include_path(),)));
		set_include_path(
				implode(PATH_SEPARATOR,
						array(realpath(DOCTRINE_PATH), get_include_path(),)));

		/** Zend_Application */
		require_once(LIBRARY_PATH.'/PHPExcel/PHPExcel.php');
		require_once(LIBRARY_PATH.'/BackEnd/Helper/viewHelper.php');
		require_once (LIBRARY_PATH . '/Zend/Application.php');
		require_once(DOCTRINE_PATH . '/Doctrine.php');

		// Create application, bootstrap, and run
		$application = new Zend_Application(APPLICATION_ENV,
				APPLICATION_PATH . '/configs/application.ini');

		$frontControlerObject = $application->getOption('resources');


		$this->_mandrillKey = $frontControlerObject['frontController']['params']['mandrillKey'];
		$this->_template = $frontControlerObject['frontController']['params']['newsletterTemplate'];

		$connections = $application->getOption('doctrine');
		spl_autoload_register(array('Doctrine', 'autoload'));

		$manager = Doctrine_Manager::getInstance();

		$imbull = $connections['imbull'];

		// cycle htoruh all site database
		$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');


		// cycle thoruh all site database
		foreach ( $connections as $key => $connection ) {
			// check database is being must be site
			if ($key != 'imbull') {
				try {
					$this->send( $connection ['dsn'], $key ,$imbull );
				} catch ( Exception $e ) {
					echo $e->getMessage ();
					echo "\n\n";
				}
				echo "\n\n";
			}
		}

		$manager->closeConnection($DMC1);
	}

	protected function send($dsn, $key,$imbull) {

		# setup appropriate vaues according to locale
		if ($key == 'en') {
			$this->_localePath = '';
			$this->_hostName = "http://www.kortingscode.nl";
			$this->_logo = $this->_hostName ."/public/images/HeaderMail.gif" ;
			$suffix = "" ;
			$this->_locale = '';
			$this->_siteName = "Kortingscode.nl";
		} else {
			$this->_localePath = $key . "/";
			$this->_hostName = "http://www.flipit.com";
			$suffix = "_" . strtoupper($key) ;
			$this->_locale = $key;
			$this->_siteName = "Flipit.com";
			$this->_logo =  $this->_hostName ."/public/images/flipit-welcome-mail.jpg";
		}

		defined('PUBLIC_PATH')
			|| define('PUBLIC_PATH',
				dirname(dirname(dirname(__FILE__)))."/public/");

		$DMC = Doctrine_Manager::connection($dsn, 'doctrine_site');
		spl_autoload_register(array('Doctrine', 'modelsAutoload'));

		$manager = Doctrine_Manager::getInstance();
		//Doctrine_Core::loadModels(APPLICATION_PATH . '/models/generated');

		$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
		$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
		$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
		Doctrine_Core::loadModels(APPLICATION_PATH . '/models');

		try {

			$settings = Signupmaxaccount::getAllMaxAccounts();

			# check if newsletter is scheduled and still not sent then proceed with  newsletter sending
			if($settings[0]['newletter_is_scheduled'] && $settings[0]['newletter_status'] ==  0) {
				$cutsomLocale = !empty( $settings[0]['locale']) ? $settings[0]['locale'] : 'nl_NL';

				$this->_trans = new Zend_Translate(array(
						'adapter' => 'gettext',
						'disableNotices' => true));

				$this->_trans->addTranslation(
						array(
								'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).'language/frontend_php' . $suffix . '.mo',
								'locale' => $cutsomLocale,
						)
				);

				$this->_trans->addTranslation(
						array(
								'content' => APPLICATION_PATH.'/../public/'.strtolower($this->_localePath).'language/po_links' . $suffix . '.mo',
								'locale' => $cutsomLocale ,
						)
				);



				Zend_Registry::set('Zend_Translate', $this->_trans);

				$timezone = $settings[0]['timezone'];

				echo "\n" ;

				$sentTime = new Zend_Date($settings[0]['newletter_scheduled_time']);
				$sentTime->get('YYYY-MM-dd HH:mm:ss') ;

				$currentTime = new Zend_Date();
				$currentTime->setTimezone($timezone);

				echo "\n" ;
				$currentTime->get('YYYY-MM-dd HH:mm:ss') ; ;

				# if sent time is passed then send newsletter
				if ($currentTime->isLater($sentTime)) {

					echo "\nSending newletter...\n" ;

					$this->mandrilHandler($key, $settings);
				}

			} else {

				echo "\n";
				print "$key - Already sent";

			}

		} catch (Exception $e) {

			echo "\n";
			echo $e->getMessage();
		}

		$manager->closeConnection($DMC);

	}


	protected function mandrilHandler($key, $settings )
	{

		$this->_linkPath = $this->_hostName . '/' .$this->_localePath ;
		$this->_publicPath = $this->_hostName . '/public/' . $this->_localePath  ;

		$this->_rootPath = PUBLIC_PATH . $this->_localePath ;

		$imgLogoMail = "<a href=". rtrim($this->_linkPath , '/') ."><img src='".$this->_logo . "'/></a>";

		$topCategories = array_slice(FrontEnd_Helper_viewHelper::gethomeSections("category", 10),0,1);

		# fetch top 10 voucher codes
		$voucherCodesData = $this->getVouchercodes();

		# fetch top 10 categories
		$categoriesData = $this->getCategories();

	 	# return visitors data
		$this->getDirectLoginLinks();


	 	//set the header image for mail
		$headerMail = array(array('name' => 'headerMail',
				'content' => $imgLogoMail
		),
				array('name' => 'headerContent',
						'content' => $settings[0]['email_header']
				),
				array('name' => 'footerContent',
						'content' => $settings[0]['email_footer']
				));

		//set the static content of mail so that we can change the text in PO Edit
		$staticContent = array(
				array('name' => 'websiteName',
						'content' => $this->_siteName
				),
				array('name' => 'unsubscribe',
						'content' => $this->_trans->translate('Uitschrijven')
				),
				array('name' => 'editProfile',
						'content' => $this->_trans->translate('Wijzigen profiel')
				),
				array('name' => 'contact',
						'content' => $this->_trans->translate('Contact')
				),
				array('name' => 'contactLink',
						'content' => $this->_linkPath . 'info/contact'
				),
				array('name' => 'moreOffersLink',
						'content' => $this->_linkPath . FrontEnd_Helper_viewHelper::__link('populair')
				),
				array('name' => 'moreOffers',
						'content' => $this->_trans->translate('Bekijk meer van onze top aanbiedingen') . ' >'
				)
		);

		//merge all the arrays into single array
	 	$data = array_merge($voucherCodesData['dataShopName'],
				$voucherCodesData['dataOfferName'],
				$voucherCodesData['dataShopImage'],
				$voucherCodesData['expDate'],
				$headerMail,
	 			$categoriesData['category'],
				$categoriesData['dataShopNameCat'],
				$categoriesData['dataOfferNameCat'],
	 			$categoriesData['dataShopImageCat'],
				$categoriesData['expDateCat']
		);

		//merge the permalinks array and static content array into single array
		$dataPermalink = array_merge($voucherCodesData['shopPermalink'],
									$categoriesData['shopPermalinkCat'],
									$staticContent);

		//initialize mandrill with the template name and other necessary options
		$mandrill = new Mandrill_Init($this->_mandrillKey );
		$template_name = $this->_template ;
		$template_content = $data ;

		//Start get email locale basis
		$emailFrom  = $settings[0]['emailperlocale'];
		$emailSubject  = $settings[0]['emailsubject'];
		$senderName  = $settings[0]['sendername'];

		$message = array(
				'subject'    => $emailSubject ,
				'from_email' => $emailFrom,
				'from_name'  => $senderName,
				'to'         => $this->_to ,
				'inline_css' => true,
				"recipient_metadata" =>  $this->_recipientMetaData ,
				'global_merge_vars' => $dataPermalink,
				'merge_vars' => $this->_loginLinkAndData
		);

		try {

			$mandrill->messages->sendTemplate($template_name, $template_content, $message);

			# set newsletter scheduling to be false and newsletter status true. Also set sending time to be past

			Signupmaxaccount::updateNewsletterSchedulingStatus();

			$message = 'Newsletter has been sent successfully' ;
			
		} catch (Mandrill_Error $e) {
			$message ='There is some problem in your data';
		}

		echo "\n";
		print "$key - $message ";

	}


	/**
	 * getVouchercodes
	 *
	 * This function loops the offer data and set the needed data in gloabal arrays for mandrill newsletter
	 *
	 * @param array $topVouchercodes
	 * @author sp singh
	 * @version 1.0
	 */
	protected function getVouchercodes()
	{


		//get offers from top ten popular shops and top one cateory as in homepage
        $topVouchercodes = FrontEnd_Helper_viewHelper::gethomeSections("popular", 10) ;
		$topVouchercodes =  FrontEnd_Helper_viewHelper::fillupTopCodeWithNewest($topVouchercodes,10);



		$dataShopName = $dataShopImage =  $shopPermalink = $expDate = $dataOfferName = array();

		foreach ($topVouchercodes as $key => $value) {

			$permalinkEmail = $this->_linkPath . $value['offer']['shop']['permaLink'].'?utm_source=transactional&utm_medium=email&utm_campaign='.date('d-m-Y');
			//sets the $dataShopName array with shop names
			$dataShopName[$key]['name'] = "shopTitle_".($key+1);
			$dataShopName[$key]['content'] = "<a style='color:#333333; text-decoration:none;'href='$permalinkEmail'>".$value['offer']['shop']['name']."</a>";

			//sets the $dataOfferName array with offer names
			$dataOfferName[$key]['name'] = "offerTitle_".($key+1);
			$dataOfferName[$key]['content'] = $value['offer']['title'];

			//set the logo for shop if it exists or not in $dataShopImage array
			if(count($value['offer']['shop']['logo']) > 0):
				if(@file_exists($this->_rootPath.$value['offer']['shop']['logo']['path'] .'thum_medium_store_'. $value['offer']['shop']['logo']['name']) && $value['offer']['shop']['logo']['name']!=''):
					$img = $this->_publicPath .$value['offer']['shop']['logo']['path'].'thum_medium_store_'. $value['offer']['shop']['logo']['name'];
				else:
					$img = $this->_publicPath ."/images/NoImage/NoImage_200x100.jpg";
				endif;
			else:
				$img = $this->_publicPath ."/images/NoImage/NoImage_200x100.jpg";
			endif;

			$dataShopImage[$key]['name'] = 'shopLogo_'.($key+1);
			$dataShopImage[$key]['content'] = "<a href='$permalinkEmail'><img src='$img'></a>";

			//set $expDate array with the expiry date of offer
			$expiryDate = new Zend_Date($value['offer']['endDate']);
			$expDate[$key]['name'] = 'expDate_'.($key+1);
			$expDate[$key]['content'] = FrontEnd_Helper_viewHelper::__link('Verloopt op:') ." " . $expiryDate->get(Zend_Date::DATE_MEDIUM);

			//set $shopPermalink array with the permalink of shop
			$shopPermalink[$key]['name'] = 'shopPermalink_'.($key+1);
			$shopPermalink[$key]['content'] = $permalinkEmail;
		}

		return array('dataShopName' => $dataShopName,
					'dataShopImage' => $dataShopImage,
					'shopPermalink' => $shopPermalink,
					'expDate' => $expDate,
					'dataOfferName' =>  $dataOfferName );
	}



	/**
	 * getCategories
	 *
	 * This function loops the category data and set the needed data in gloabal arrays
	 *
	 * @param array $topCategories
	 * @author sp singh
	 * @version 1.0
	 */
	protected function getCategories()
	{
		# get top categories
		$topCategories = array_slice(FrontEnd_Helper_viewHelper::gethomeSections("category", 10),0,1);

		$dataShopNameCat = $dataOfferNameCat =  $dataShopImageCat = $expDateCat = $shopPermalinkCat = array();

		//set the logo for category, category name and more category link

		//if it exists or not in $category array
		if(count($topCategories[0]['category']['categoryicon']) > 0):
			if(@file_exists($this->_rootPath.$topCategories[0]['category']['categoryicon']['path'] .'thum_medium_'. $topCategories[0]['category']['categoryicon']['name']) && $topCategories[0]['category']['categoryicon']['name']!=''):
				$img = $this->_publicPath.$topCategories[0]['category']['categoryicon']['path'].'thum_medium_'. $topCategories[0]['category']['categoryicon']['name'];
			else:
				$img = $this->_publicPath."/images/NoImage/NoImage_70x60.png";
			endif;
		else:
			$img = $this->_publicPath."/images/NoImage/NoImage_70x60.png";
		endif;

		$permalinkCatMainEmail = $this->_linkPath . FrontEnd_Helper_viewHelper::__link('categorieen') .'/'. $topCategories[0]['category']['permaLink'] . '?utm_source=transactional&utm_medium=email&utm_campaign='.date('d-m-Y');
		$category = array(array('name' => 'categoryImage',
				'content' => "<a style='color:#333333; text-decoration:none;' href='$permalinkCatMainEmail'><img src='".$img."'/></a>"
		),
				array('name' => 'categoryName',
						'content' => $this->_trans->translate('Populairste categorie:') ." <a style='color:#333333; text-decoration:none;' href='$permalinkCatMainEmail'>". $topCategories[0]['category']['name'] ."</a>"
				),
				array('name' => 'categoryNameMore',
						'content' => '<a href="'.$permalinkCatMainEmail.'" style="font-size:12px; text-decoration:none; color:#0B7DC1;" >' . $this->_trans->translate('Bekijk meer van onze') ." ". $topCategories[0]['category']['name'] ." ". $this->_trans->translate('aanbiedingen') . ' > </a>'
				));

		//get three voucher codes in top one category from homepage
		$vouchers = array_slice(Category::getCategoryVoucherCodes($topCategories[0]['categoryId']),0,3);

		foreach ($vouchers as $key => $value) {

			$permalinkCatEmail = $this->_linkPath . $value['shop']['permalink'].'?utm_source=transactional&utm_medium=email&utm_campaign='.date('d-m-Y');
			//set $dataShopNameCat array with the title of shop in this category
			$dataShopNameCat[$key]['name'] = "shopTitleCat_".($key+1);
			$dataShopNameCat[$key]['content'] = "<a style='color:#333333; text-decoration:none;' href='$permalinkCatEmail'>".$value['shop']['name']."</a>";

			//set $dataOfferNameCat array with the title of offer in this category
			$dataOfferNameCat[$key]['name'] = "offerTitleCat_".($key+1);
			$dataOfferNameCat[$key]['content'] = $value['title'];

			//set the logo for shop in this category if it exists or not in $dataShopImageCat array
			if(count($value['shop']['logo']) > 0):
				if(@file_exists($this->_rootPath.$value['shop']['logo']['path'] .'thum_medium_store_'. $value['shop']['logo']['name']) && $value['shop']['logo']['name']!=''):
					$img = $this->_publicPath.$value['shop']['logo']['path'].'thum_medium_store_'. $value['shop']['logo']['name'];
				else:
					$img = $this->_publicPath."/images/NoImage/NoImage_200x100.jpg";
				endif;
			else:
				$img = $this->_publicPath."/images/NoImage/NoImage_200x100.jpg";
			endif;

			$dataShopImageCat[$key]['name'] = 'shopLogoCat_'.($key+1);
			$dataShopImageCat[$key]['content'] = "<a href='$permalinkCatEmail'><img src='$img'></a>";

			//set the expiry date for offer in this category in $expDateCat array
			$expiryDate = new Zend_Date($value['endDate']);
			$expDateCat[$key]['name'] = 'expDateCat_'.($key+1);
			$expDateCat[$key]['content'] = $this->_trans->translate('Verloopt op:') ." ". $expiryDate->get(Zend_Date::DATE_MEDIUM);

			//set the permalink for shop in this category in $shopPermalinkCat array
			$shopPermalinkCat[$key]['name'] = 'shopPermalinkCat_'.($key+1);
			$shopPermalinkCat[$key]['content'] = $permalinkCatEmail;
		}

		return array('category' => $category,
					'dataShopNameCat' => $dataShopNameCat,
					'dataOfferNameCat' => $dataOfferNameCat,
					'dataShopImageCat' => $dataShopImageCat,
					'expDateCat' => $expDateCat,
					'shopPermalinkCat' =>  $shopPermalinkCat );
	}

	/**
	 * getDirectLoginLinks
	 *
	 * This function makes the URL for direct login links for each users
	 *
	 * @author cbhopal
	 * @version 1.0
	 */
	protected function getDirectLoginLinks()
	{
		
		$visitorData = array();
		$visitorMetaData = array();
		$toVisitorArray = array();

		//retrieve the visitors with status, active and weeklynewsletter true
		$visitors = new Visitor();

		$visitors = $visitors->getVisitorsToSendNewsletter();
		
 
		//initialize the mandrill to retrieve the data of the users to whom we have sent mails
		$mandrill = new Mandrill_Init( $this->_mandrillKey);
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

				$visitorData[$key]['vars'][0]['content'] = $this->_linkPath . FrontEnd_Helper_viewHelper::__link("login") . "/" .FrontEnd_Helper_viewHelper::__link("directlogin") . "/" . base64_encode($value['email']) ."/". $value['password'];

				$visitorData[$key]['vars'][1]['name'] = 'loginLinkWithUnsubscribe';
				$visitorData[$key]['vars'][1]['content'] = $this->_linkPath . FrontEnd_Helper_viewHelper::__link("login") . "/" .FrontEnd_Helper_viewHelper::__link("directloginunsubscribe") . "/" . base64_encode($value['email']) ."/". $value['password'];

				$toVisitorArray[$key]['email'] = $value['email'];
				$toVisitorArray[$key]['name'] = !empty($value['firstName']) ? $value['firstName'] : 'Member';

			}

			$this->_recipientMetaData = $visitorMetaData; // set referer for each user;
			$this->_loginLinkAndData = $visitorData;//set the visitor data in $loginLinkAndData array
			$this->_to = $toVisitorArray;//set the users email to which mails has been sent $to array
	}
}

new SendNewsletter();
?>