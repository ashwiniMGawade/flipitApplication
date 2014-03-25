<?php

/**
 * Script for exporting the shops
 *
 * @author Raman
 *
 */
class ShopExport {

	protected $_localePath = '/';
	protected $_hostName = '';
	protected $_trans = null;


	function __construct() {

	ini_set('memory_limit', '-1');

	set_time_limit(0);
	/*
	$domain1 = $_SERVER['HOSTNAME'];
	$domain = 'http://www.'.$domain1;
	*/

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
		require_once(LIBRARY_PATH.'/FrontEnd/Helper/viewHelper.php');
		require_once (LIBRARY_PATH . '/Zend/Application.php');
		require_once(DOCTRINE_PATH . '/Doctrine.php');

		// Create application, bootstrap, and run
		$application = new Zend_Application(APPLICATION_ENV,
				APPLICATION_PATH . '/configs/application.ini');

		$connections = $application->getOption('doctrine');
		spl_autoload_register(array('Doctrine', 'autoload'));

		$manager = Doctrine_Manager::getInstance();

		$imbull = $connections['imbull'];

		// cycle htoruh all site database
		$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');
		
		echo "\n";
		print "Get all shops data from databases of all locales\n";
		
		// cycle htoruh all site database
		foreach ( $connections as $key => $connection ) {
			// check database is being must be site
			if ($key != 'imbull') {
				try {

					$this->exportingShops( $connection ['dsn'], $key ,$imbull );

					} catch ( Exception $e ) {

					echo $e->getMessage ();
					echo "\n\n";
				}
				echo "\n\n";
			}
		}

		//$this->exportingShops($connections['en']['dsn'], 'en' ,$imbull ); //uncommnet this line when you check only kortingscode.nl excel export list
		
		$manager->closeConnection($DMC1);
	}

	protected function exportingShops($dsn, $key,$imbull) {


		if ($key == 'en') {
			$this->_localePath = '';
			$this->_hostName = "http://www.kortingscode.nl";
			$this->_logo = $this->_hostName . "/public/images/front_end/logo-popup.png";
			$suffix = "" ;
		} else {
			$this->_localePath = $key . "/";
			$this->_hostName = "http://www.flipit.com";
			$suffix = "_" . strtoupper($key) ;
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

		$cutsomLocale = Signupmaxaccount::getallmaxaccounts();
		$cutsomLocale = !empty($cutsomLocale[0]['locale']) ? $cutsomLocale[0]['locale'] : 'nl_NL';

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


		$date = new Zend_Date();
		$month = $date->get(Zend_Date::MONTH_NAME);
		$year = $date->get(Zend_Date::YEAR);
		$day = $date->get(Zend_Date::DAY);


		#define currecnt month for text with [month]
		defined('CURRENT_MONTH')
				|| define('CURRENT_MONTH', $month );

						#define currecnt year for text with [year]
		defined('CURRENT_YEAR')
		|| define('CURRENT_YEAR', $year );

		#define currecnt day for text with [day]
		defined('CURRENT_DAY')
		|| define('CURRENT_DAY', $day );

		defined ( 'PUBLIC_PATH' ) || define ( 'PUBLIC_PATH', dirname ( dirname ( dirname ( __FILE__ ) ) ) . "/public/" );


		//setting to no time limit,
		set_time_limit(0);

		//declaring class instance
		$data =  Shop::exportShopeList();

		//echo "<pre>";
		//print_r($data); die;
    	//create object of phpExcel
		echo "\n";
		print "Parse shops data and save it into excel file\n";
		
    	$objPHPExcel = new PHPExcel();
    	$objPHPExcel->setActiveSheetIndex(0);
    	$objPHPExcel->getActiveSheet()->setCellValue('A1', $this->_trans->translate('Shopname'));
    	$objPHPExcel->getActiveSheet()->setCellValue('B1', $this->_trans->translate('Navigation URL'));
    	$objPHPExcel->getActiveSheet()->setCellValue('C1', $this->_trans->translate('Money shop'));
    	$objPHPExcel->getActiveSheet()->setCellValue('D1', $this->_trans->translate('Account manager'));
    	$objPHPExcel->getActiveSheet()->setCellValue('E1', $this->_trans->translate('Start'));
    	$objPHPExcel->getActiveSheet()->setCellValue('F1', $this->_trans->translate('Network'));
    	$objPHPExcel->getActiveSheet()->setCellValue('G1', $this->_trans->translate('Online'));
    	$objPHPExcel->getActiveSheet()->setCellValue('H1', $this->_trans->translate('Offline since'));
    	$objPHPExcel->getActiveSheet()->setCellValue('I1', $this->_trans->translate('Overwrite Title'));
    	$objPHPExcel->getActiveSheet()->setCellValue('J1', $this->_trans->translate('Meta Description'));
    	$objPHPExcel->getActiveSheet()->setCellValue('K1', $this->_trans->translate('Allow user generated content'));
    	$objPHPExcel->getActiveSheet()->setCellValue('L1', $this->_trans->translate('Allow Discussions'));
    	$objPHPExcel->getActiveSheet()->setCellValue('M1', $this->_trans->translate('Title'));
    	$objPHPExcel->getActiveSheet()->setCellValue('N1', $this->_trans->translate('Sub-title'));
    	$objPHPExcel->getActiveSheet()->setCellValue('O1', $this->_trans->translate('Notes'));
    	$objPHPExcel->getActiveSheet()->setCellValue('P1', $this->_trans->translate('Editor'));
    	$objPHPExcel->getActiveSheet()->setCellValue('Q1', $this->_trans->translate('Category'));
    	$objPHPExcel->getActiveSheet()->setCellValue('R1', $this->_trans->translate('Similar Shops'));
    	$objPHPExcel->getActiveSheet()->setCellValue('S1', $this->_trans->translate('Deeplinking code'));
    	$objPHPExcel->getActiveSheet()->setCellValue('T1', $this->_trans->translate('Ref URL'));
    	$objPHPExcel->getActiveSheet()->setCellValue('U1', $this->_trans->translate('Actual URL'));
    	$objPHPExcel->getActiveSheet()->setCellValue('V1', $this->_trans->translate('Shop Text'));
    	$objPHPExcel->getActiveSheet()->setCellValue('W1', $this->_trans->translate('Days Without Online Coupons'));
    	$objPHPExcel->getActiveSheet()->setCellValue('X1', $this->_trans->translate('No. of Times Shop became Favourite'));
    	$objPHPExcel->getActiveSheet()->setCellValue('Y1', $this->_trans->translate('Last week Clickouts'));
    	$objPHPExcel->getActiveSheet()->setCellValue('Z1', $this->_trans->translate('Total Clickouts'));
    	$objPHPExcel->getActiveSheet()->setCellValue('AA1', $this->_trans->translate('Amount of Coupons'));
    	$objPHPExcel->getActiveSheet()->setCellValue('AB1', $this->_trans->translate('Amount of Offers'));
    	$objPHPExcel->getActiveSheet()->setCellValue('AC1', $this->_trans->translate('How To Guide'));
    	$objPHPExcel->getActiveSheet()->setCellValue('AD1', $this->_trans->translate('News Ticker'));
    	$objPHPExcel->getActiveSheet()->setCellValue('AE1', $this->_trans->translate('Display singup option'));
        $objPHPExcel->getActiveSheet()->setCellValue('AF1', $this->_trans->translate('Display similar shops'));
        $objPHPExcel->getActiveSheet()->setCellValue('AG1', $this->_trans->translate('Display chains'));
        $objPHPExcel->getActiveSheet()->setCellValue('AH1', $this->_trans->translate('Custom Header Text'));
        $objPHPExcel->getActiveSheet()->setCellValue('AI1', $this->_trans->translate('Extra opties'));
        $objPHPExcel->getActiveSheet()->setCellValue('AJ1', $this->_trans->translate('Last Updated'));


    	$column = 2;
    	$row = 2;

    	//loop for each shop
    	foreach ($data as $shop)
    	{
    		echo "\n";
    		print "$key - Shops are being saved into excel file !!!";
    		
    		//condition apply on affliatedprograme
    		$prog = '';
    		if($shop['affliateProgram']==true){

    			$prog = $this->_trans->translate('Yes');
    		}
    		else{
    			$prog = $this->_trans->translate('No');
    		}

    		//get account manage name from array
    		$accountManagername = '';
    		if($shop['accountManagerName']==''
    				||$shop['accountManagerName']=='undefined'
    				||$shop['accountManagerName']==null
    				||$shop['accountManagerName']=='0'){

    			$accountManagername ='';
    		} else {

    			$accountManagername = User::getUserName($shop['accoutManagerId']);
    		}

    		//create start date format
  			$startDate =  date("d-m-Y", strtotime($shop['created_at']));

  			//get affilate network from array
  			$affilateNetwork = '';

	    		if($shop['affname']==null
	    				||$shop['affname']==''
	    				||$shop['affname']=='undefined'){

	    			$affilateNetwork = '';

	    		}  else {

	    			$affilateNetwork = $shop['affname'];
	    		}


  			//get offline (status of shop from array
    		$offLine='';
    		if($shop['status']==true){

    			$offLine=$this->_trans->translate('Yes');

    		} else {

    			$offLine=$this->_trans->translate('No');
    		}

    		//get offline since or not from array
    		$offLineSince = '';
    		if($shop['offlineSicne']=='undefined'
    				|| $shop['offlineSicne']==null
    				|| $shop['offlineSicne']==''){

    			$offLineSince='';

    		} else {

    			$offLineSince = date("d-m-Y", strtotime($shop['offlineSicne']));
    		}

    		$overriteTitle = '';
    		if($shop['overriteTitle']=='undefined'
    				|| $shop['overriteTitle']==null
    				|| $shop['overriteTitle']==''){

    			$overriteTitle='';

    		} else {

    			$overriteTitle = $shop['overriteTitle'];
    		}

    		$metaDesc = '';
    		if($shop['metaDescription']=='undefined'
    				|| $shop['metaDescription']==null
    				|| $shop['metaDescription']==''){

    			$metaDesc='';

    		} else {

    			$metaDesc = $shop['metaDescription'];
    		}

    		$userGenerated = '';
    		if($shop['usergenratedcontent']==true){

    			$userGenerated= $this->_trans->translate('Yes');
    		}
    		else{
    			$userGenerated = $this->_trans->translate('No');
    		}


    		$howToGuide = '';
    		if($shop['howToUse']==true){

    			$howToGuide = $this->_trans->translate('Yes');
    		}
    		else{
    			$howToGuide = $this->_trans->translate('No');
    		}

			# if it is set then current shop has atleast one new ticker
    		$newsTicker = '';
    		if($shop['newsTickerTime'] > 0 ){

    			$newsTicker = $this->_trans->translate('Yes');
    		}
    		else{
    			$newsTicker = $this->_trans->translate('No');
    		}

    		//get offline since or not from array
    		$lastUpdated = '';


    		# gte shop updated time based on shop updated time and its' newstickers update time and offers update time
    		$shopTime = strtotime(@$shop['updated_at']);

    		$newTickerTime = isset($shop['newsTickerTime'])
    		? strtotime($shop['newsTickerTime']) : false 	;

    		$offerTime =	 isset($shop['offerTime'])
    		? strtotime($shop['offerTime']) : false ;

    		# get latetest time among three
			$lastUpdated = max($shopTime, $newTickerTime, $offerTime);


			$lastUpdated = date("d-m-Y H:i:s", $lastUpdated);



    		if($shop['discussions'] ==true){

                $discussion = $this->_trans->translate('Yes');
            }
            else{
                $discussion = $this->_trans->translate('No');
            }



    		$title = '';
    		if($shop['title']=='undefined'
    				|| $shop['title']==null
    				|| $shop['title']==''){

    			$title='';

    		} else {

    			$title = FrontEnd_Helper_viewHelper::replaceStringVariable($shop['title']);
    		}

    		$subTitle = '';
    		if($shop['subTitle']=='undefined'
    				|| $shop['subTitle']==null
    				|| $shop['subTitle']==''){

    			$subTitle ='';

    		} else {

    			$subTitle = FrontEnd_Helper_viewHelper::replaceStringVariable($shop['subTitle']);
    		}

    		$notes = '';
    		if($shop['notes']=='undefined'
    				|| $shop['notes']==null
    				|| $shop['notes']==''){

    			$notes ='';

    		} else {

    			$notes = $shop['notes'];
    		}

    		$contentManagerName = '';
    		if($shop['contentManagerId']=='undefined'
    				|| $shop['contentManagerId']==null
    				|| $shop['contentManagerId']==''){

    			$contentManagerName ='';

    		} else {

    			$contentManagerName = User::getUserName($shop['contentManagerId']);
    		}

    		$categories = '';
    		if(!empty($shop['category'])){
    			$prefix = '';
    			foreach ($shop['category'] as $cat)
    			{
    				$categories .= $prefix  . $cat['name'];
    				$prefix = ', ';
    			}
    		}

    		$relatedshops = '';
    		if(!empty($shop['relatedshops'])){
    			$prefix = '';
    			foreach ($shop['relatedshops'] as $rShops)
    			{
    				$relatedshops .= $prefix  . $rShops['name'];
    				$prefix = ', ';
    			}
    		}

    		$deeplink = '';
    		if($shop['deepLink']=='undefined'
    				|| $shop['deepLink']==null
    				|| $shop['deepLink']==''){

    			$deeplink ='';

    		} else {

    			$deeplink = $shop['deepLink'];
    		}

    		$refUrl = '';
    		if($shop['refUrl']=='undefined'
    				|| $shop['refUrl']==null
    				|| $shop['refUrl']==''){

    			$refUrl ='';

    		} else {

    			$refUrl = $shop['refUrl'];
    		}

    		$actualUrl = '';
    		if($shop['actualUrl']=='undefined'
    				|| $shop['actualUrl']==null
    				|| $shop['actualUrl']==''){

    			$actualUrl ='';

    		} else {

    			$actualUrl = $shop['actualUrl'];
    		}

    		$shopText = '';
    		if($shop['shopText']=='undefined'
    				|| $shop['shopText']==null
    				|| $shop['shopText']==''){

    			$shopText ='';

    		} else {

    			$shopText = $shop['shopText'];
    		}


            if($shop['showSimliarShops'] == 1){
                $showSimliarShops = $this->_trans->translate('Yes');
            } else {
                $showSimliarShops = $this->_trans->translate('No');
            }


            if($shop['showSignupOption'] == 1){
                $showSignupOption = $this->_trans->translate('Yes');
            } else {
                $showSignupOption = $this->_trans->translate('No');
            }


            if($shop['showChains'] == 1){
                $showChains = $this->_trans->translate('Yes');
            } else {
                $showChains = $this->_trans->translate('No');
            }

            if($shop['customHeader']){
                $customHeader = $this->_trans->translate('Yes');
            } else {
                $customHeader = $this->_trans->translate('No');
            }


            if($shop['displayExtraProperties'] == 1){
                $displayExtraProperties = $this->_trans->translate('Yes');
            } else {
                $displayExtraProperties = $this->_trans->translate('No');
            }

    		$shopId = $shop['id'];
    		//Extra columns added to excel export
    		$daysWithoutCoupon = Shop::getDaysSinceShopWithoutOnlneOffers($shopId);
    		$timesShopFavourite = Shop::getTimesShopFavourite($shopId);
    		$lastWeekClicks = ShopViewCount::getAmountClickoutOfShop($shopId);
    		//$totalClicks = ShopViewCount::getTotalAmountClicksOfShop($shopId);
    		$totalClicks =  ShopViewCount::getTotalViewCountOfShopAndOffer($shopId);
    		$totalAmountCoupons = Offer::getTotalAmountOfCouponsShop($shopId, 'CD');
    		$totalAmountOffers = Offer::getTotalAmountOfCouponsShop($shopId);

    		//set value in column of excel
    		$objPHPExcel->getActiveSheet()->setCellValue('A'.$column, $shop['name']);
    		$objPHPExcel->getActiveSheet()->setCellValue('B'.$column, $shop['permaLink']);
    		$objPHPExcel->getActiveSheet()->setCellValue('C'.$column,$prog);
    		$objPHPExcel->getActiveSheet()->setCellValue('D'.$column, $accountManagername);
    		$objPHPExcel->getActiveSheet()->setCellValue('E'.$column, $startDate);
    		$objPHPExcel->getActiveSheet()->setCellValue('F'.$column, $affilateNetwork);
    		$objPHPExcel->getActiveSheet()->setCellValue('G'.$column, $offLine);
    		$objPHPExcel->getActiveSheet()->setCellValue('H'.$column, $offLineSince);
    		$objPHPExcel->getActiveSheet()->setCellValue('I'.$column, $overriteTitle);
    		$objPHPExcel->getActiveSheet()->setCellValue('J'.$column, $metaDesc);
    		$objPHPExcel->getActiveSheet()->setCellValue('K'.$column, $userGenerated);
    		$objPHPExcel->getActiveSheet()->setCellValue('L'.$column, $discussion);
    		$objPHPExcel->getActiveSheet()->setCellValue('M'.$column, $title);
    		$objPHPExcel->getActiveSheet()->setCellValue('N'.$column, $subTitle);
    		$objPHPExcel->getActiveSheet()->setCellValue('O'.$column, $notes);
    		$objPHPExcel->getActiveSheet()->setCellValue('P'.$column, $contentManagerName);
    		$objPHPExcel->getActiveSheet()->setCellValue('Q'.$column, $categories);
    		$objPHPExcel->getActiveSheet()->setCellValue('R'.$column, $relatedshops);
    		$objPHPExcel->getActiveSheet()->setCellValue('S'.$column, $deeplink);
    		$objPHPExcel->getActiveSheet()->setCellValue('T'.$column, $refUrl);
    		$objPHPExcel->getActiveSheet()->setCellValue('U'.$column, $actualUrl);
    		$objPHPExcel->getActiveSheet()->setCellValue('V'.$column, $shopText);
    		$objPHPExcel->getActiveSheet()->setCellValue('W'.$column, $daysWithoutCoupon);
    		$objPHPExcel->getActiveSheet()->setCellValue('X'.$column, $timesShopFavourite);
    		$objPHPExcel->getActiveSheet()->setCellValue('Y'.$column, $lastWeekClicks);
    		$objPHPExcel->getActiveSheet()->setCellValue('Z'.$column, $totalClicks);
    		$objPHPExcel->getActiveSheet()->setCellValue('AA'.$column, $totalAmountCoupons);
    		$objPHPExcel->getActiveSheet()->setCellValue('AB'.$column, $totalAmountOffers);
    		$objPHPExcel->getActiveSheet()->setCellValue('AC'.$column, $howToGuide);
    		$objPHPExcel->getActiveSheet()->setCellValue('AD'.$column, $newsTicker);


            $objPHPExcel->getActiveSheet()->setCellValue('AE'.$column, $showSignupOption);
            $objPHPExcel->getActiveSheet()->setCellValue('AF'.$column, $showSimliarShops);
            $objPHPExcel->getActiveSheet()->setCellValue('AG'.$column, $showChains);
            $objPHPExcel->getActiveSheet()->setCellValue('AH'.$column, $customHeader);
            $objPHPExcel->getActiveSheet()->setCellValue('AI'.$column, $displayExtraProperties);
    		$objPHPExcel->getActiveSheet()->setCellValue('AJ'.$column, $lastUpdated);

    		//counter incriment by 1
    		$column++;
    		$row++;
    	}

    	//FORMATING OF THE EXCELL
    	$headerStyle = array(
    			'fill' => array(
    					'type' => PHPExcel_Style_Fill::FILL_SOLID,
    					'color' => array('rgb'=>'00B4F2'),
    			),
    			'font' => array(
    					'bold' => true,
    			)
    	);
    	$borderStyle = array('borders' =>
    			array('outline' =>
    					array('style' => PHPExcel_Style_Border::BORDER_THICK,
    							'color' => array('argb' => '000000'),	),),);
    	//HEADER COLOR

    	$objPHPExcel->getActiveSheet()->getStyle('A1:'.'AJ1')->applyFromArray($headerStyle);

    	//SET ALIGN OF TEXT
    	$objPHPExcel->getActiveSheet()->getStyle('A1:AJ1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	$objPHPExcel->getActiveSheet()->getStyle('B2:AJ'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

    	//BORDER TO CELL
    	$objPHPExcel->getActiveSheet()->getStyle('A1:'.'AJ1')->applyFromArray($borderStyle);
    	$borderColumn =  (intval($column) -1 );

    	$objPHPExcel->getActiveSheet()->getStyle('A1:'.'AJ'.$borderColumn)->applyFromArray($borderStyle);


    	//SET SIZE OF THE CELL
    	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setAutoSize(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setAutoSize(true);

		
    	
    	# define Real upload path for excel
    	defined('UPLOAD_REAL_EXCEL_PATH')
    	|| define('UPLOAD_REAL_EXCEL_PATH', APPLICATION_PATH. '/../data/' );

    	# define upload path for excel
    	defined('UPLOAD_EXCEL_PATH')
	    	|| define('UPLOAD_EXCEL_PATH', APPLICATION_PATH. '/../public/tmp/' );

    	
    	$pathToFile = UPLOAD_EXCEL_PATH . strtolower($this->_localePath) . 'excels/' ;

    	# create dir if not exists
    	if(!file_exists($pathToFile)) {
    		mkdir($pathToFile, 774, TRUE);
    	}

    	$filepath = $pathToFile . "shopList.xlsx" ;

	   	//write to an xlsx file and upload to excel folder locale basis
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    	$objWriter->save($filepath);

    	$manager->closeConnection($DMC);

		echo "\n";
		print "$key - Shops have been exported successfully!!!";
		
	    copydir(UPLOAD_EXCEL_PATH.$key,UPLOAD_REAL_EXCEL_PATH.$key);
		

	}
	
	function copydir($source,$destination)
	{
		if(!is_dir($destination)){
			$oldumask = umask(0);
			mkdir($destination, 01777); // so you get the sticky bit set
			umask($oldumask);
		}
		$dir_handle = @opendir($source) or die("Unable to open");
		while ($file = readdir($dir_handle))
		{
			if($file!="." && $file!=".." && !is_dir("$source/$file")) //if it is file
				copy("$source/$file","$destination/$file");
			if($file!="." && $file!=".." && is_dir("$source/$file")) //if it is folder
				copydir("$source/$file","$destination/$file");
		}
		closedir($dir_handle);
	}
	
	
	

}

new ShopExport();

?>