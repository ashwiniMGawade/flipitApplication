<?php

/**
 * Script for exporting the shops 
 *
 * @author Raman
 *
 */
class GlobalShopExport {
	
	protected $_localePath = '/';
	protected $_hostName = '';
	protected $_trans = null;
	
	protected $_shopsData = array();
	
	
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
						$this->getAllShops( $connection ['dsn'], $key ,$imbull );
				} catch ( Exception $e ) {
						
					echo $e->getMessage ();
					echo "\n\n";
				}
				echo "\n\n";
			}
			
		}
		
		//$this->getAllShops($connections['en']['dsn'], 'en' ,$imbull ); //uncommnet this line when you check only kortingscode.nl excel export list
	 
		$this->exportShopsInExcel();
		
		$manager->closeConnection($DMC1);
		
		
	}
	
	
	# get all shops data and keep it into an array
	
	protected function getAllShops($dsn, $key,$imbull) {
		
		
		$DMC = Doctrine_Manager::connection($dsn, 'doctrine_site');
		spl_autoload_register(array('Doctrine', 'modelsAutoload'));
		
		$manager = Doctrine_Manager::getInstance();

		
		$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
		$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
		$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
		Doctrine_Core::loadModels(APPLICATION_PATH . '/models');
		
		$cutsomLocale = Signupmaxaccount::getAllMaxAccounts();
		$cutsomLocale = !empty($cutsomLocale[0]['locale']) ? $cutsomLocale[0]['locale'] : 'nl_NL';
		
		//declaring class instance
		$data =  Shop::exportShopeList();
		
		
		# save shop data, cyurrent lcoale and it custom locale
		$this->_shopsData[$key]['data'] = $data;
		$this->_shopsData[$key]['customLocale'] = $cutsomLocale ;
		$this->_shopsData[$key]['dsn'] = $dsn;
		
				
		
	
		$manager->closeConnection($DMC);
		sleep(1.5);
		echo "\n";
		print "$key - Shops have been fetched successfully!!!";
		
		
		
	}
	
	
	# write all shops into excel file
	protected  function exportShopsInExcel()
	{
		# check if shops available
		if(! empty($this->_shopsData))
		{
			echo "\n";
			print "Parse shops data and save it into excel file\n";
			
			
			# set up excel sheet format
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Shopname');
			$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Navigation URL');
			$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Money shop');
			$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Account manager');
			$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Start');
			$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Network');
			$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Online');
			$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Offline since');
			$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Overwrite Title');
			$objPHPExcel->getActiveSheet()->setCellValue('J1', 'Meta Description');
			$objPHPExcel->getActiveSheet()->setCellValue('K1', 'Allow user generated content');
			$objPHPExcel->getActiveSheet()->setCellValue('L1', 'Allow Discussions');
			$objPHPExcel->getActiveSheet()->setCellValue('M1', 'Title');
			$objPHPExcel->getActiveSheet()->setCellValue('N1', 'Sub-title');
			$objPHPExcel->getActiveSheet()->setCellValue('O1', 'Notes');
			$objPHPExcel->getActiveSheet()->setCellValue('P1', 'Editor');
			$objPHPExcel->getActiveSheet()->setCellValue('Q1', 'Category');
			$objPHPExcel->getActiveSheet()->setCellValue('R1', 'Similar Shops');
			$objPHPExcel->getActiveSheet()->setCellValue('S1', 'Deeplinking code');
			$objPHPExcel->getActiveSheet()->setCellValue('T1', 'Ref URL');
			$objPHPExcel->getActiveSheet()->setCellValue('U1', 'Actual URL');
			$objPHPExcel->getActiveSheet()->setCellValue('V1', 'Shop Text');
			$objPHPExcel->getActiveSheet()->setCellValue('W1', 'Days Without Online Coupons');
			$objPHPExcel->getActiveSheet()->setCellValue('X1', 'No. of Times Shop became Favourite');
			$objPHPExcel->getActiveSheet()->setCellValue('Y1', 'Last week Clickouts');
			$objPHPExcel->getActiveSheet()->setCellValue('Z1', 'Total Clickouts');
			$objPHPExcel->getActiveSheet()->setCellValue('AA1','Amount of Coupons');
			$objPHPExcel->getActiveSheet()->setCellValue('AB1','Amount of Offers');
			$objPHPExcel->getActiveSheet()->setCellValue('AC1','How To Guide');
			$objPHPExcel->getActiveSheet()->setCellValue('AD1','News Ticker');
			

			$objPHPExcel->getActiveSheet()->setCellValue('AE1', 'Display singup option');
			$objPHPExcel->getActiveSheet()->setCellValue('AF1', 'Display similar shops');
			$objPHPExcel->getActiveSheet()->setCellValue('AG1', 'Display chains');
			$objPHPExcel->getActiveSheet()->setCellValue('AH1', 'Custom Header Text');
			$objPHPExcel->getActiveSheet()->setCellValue('AI1', 'Extra opties');
			$objPHPExcel->getActiveSheet()->setCellValue('AJ1', 'Last Updated');


			$objPHPExcel->getActiveSheet()->setCellValue('AK1','Locale');
			
			
			$column = 2;
			$row = 2;
			
			
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
			
			
			#traverse through data 
			foreach ($this->_shopsData as $key => $data)
			{
				
				
				echo "\n";
				print "$key - Shops are being saved into excel file !!!";
				
				# cretae appropriate suffix for language files
				if ($key == 'en') {
					$this->_localePath = '';
					$suffix = "" ;
				} else {
					$this->_localePath = $key . "/";
					$suffix = "_" . strtoupper($key) ;
				}
					
					
				# cretae zend translate object
				$this->_trans = new Zend_Translate(array(
						'adapter' => 'gettext',
						'disableNotices' => true));
					
				$this->_trans->addTranslation(
						array(
								'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).'language/frontend_php' . $suffix . '.mo',
								'locale' => $data['customLocale'],
						)
				);
					
				$this->_trans->addTranslation(
						array(
								'content' => APPLICATION_PATH.'/../public/'.strtolower($this->_localePath).'language/po_links' . $suffix . '.mo',
								'locale' => $data['customLocale'] ,
						)
				);
				
				
				$DMC = Doctrine_Manager::connection($data['dsn'], 'doctrine_site');
				spl_autoload_register(array('Doctrine', 'modelsAutoload'));
				$manager = Doctrine_Manager::getInstance();
				
				
				$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
				$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
				$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
				Doctrine_Core::loadModels(APPLICATION_PATH . '/models');
				
				
				foreach ($data['data'] as  $shop) {
					
			 
						//condition apply on affliatedprograme
						$prog = '';
						if($shop['affliateProgram']==true){
							 
							$prog = 'Yes';
						}
						else{
							$prog = 'No';
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
							 
							$offLine = 'Yes';
							 
						} else {
							 
							$offLine = 'No';
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
							 
							$userGenerated= 'Yes';
						}
						else{
							$userGenerated = 'No';
						}
						
						
						$howToGuide = '';
						if($shop['howToUse']==true){
						
							$howToGuide = 'Yes';
						}
						else{
							$howToGuide = 'No';
						}
						
						# if it is set then current shop has atleast one new ticker
						$newsTicker = '';
						if($shop['newsTickerTime'] > 0 ){
						
							$newsTicker = 'Yes';
						}
						else{
							$newsTicker = 'No';
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
    		                $discussion = 'Yes';
			            } else{ $discussion = 'No';
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
        		        $showSimliarShops = 'Yes';
		            } else {
		                $showSimliarShops = 'No';
		            }


		            if($shop['showSignupOption'] == 1){
		                $showSignupOption = 'Yes';
		            } else {
		                $showSignupOption = 'No';
		            }


		            if($shop['showChains'] == 1){
		                $showChains = 'Yes';
		            } else {
		                $showChains = 'No';
		            }

		            if($shop['customHeader']){
		                $customHeader = 'Yes';
		            } else {
		                $customHeader = 'No';
		            }


		            if($shop['displayExtraProperties'] == 1){
		                $displayExtraProperties = 'Yes';
		            } else {
		                $displayExtraProperties = 'No';
		            }
						$shopId = $shop['id'];
						
						
						//Extra columns added to excel export
						$daysWithoutCoupon = Shop::getDaysSinceShopWithoutOnlneOffers($shopId);
						$timesShopFavourite = Shop::getTimesShopFavourite($shopId);
						$lastWeekClicks = ShopViewCount::getAmountClickoutOfShop($shopId);
						//$totalClicks =  ShopViewCount::getTotalAmountClicksOfShop($shopId);
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


						
						$localeVal = $key ;
						if($key == 'en')
						{
							$localeVal = 'default' ; 
						}
						 
						$objPHPExcel->getActiveSheet()->setCellValue('AK'.$column, $localeVal);
						
						//counter incriment by 1
						$column++;
						$row++;

				}
					

				$manager->closeConnection($DMC);
				
				# delay to make sure that connection closed properly
				sleep(1.5);
				
				
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
			 
			$objPHPExcel->getActiveSheet()->getStyle('A1:'.'AK1')->applyFromArray($headerStyle);
			 
			//SET ALIGN OF TEXT
			$objPHPExcel->getActiveSheet()->getStyle('A1:AK1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B2:AK'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			 
			//BORDER TO CELL
			$objPHPExcel->getActiveSheet()->getStyle('A1:'.'AK1')->applyFromArray($borderStyle);
			$borderColumn =  (intval($column) -1 );
			$objPHPExcel->getActiveSheet()->getStyle('A1:'.'AK'.$borderColumn)->applyFromArray($borderStyle);
			 
			 
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
			$objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setAutoSize(true);
			 
			 
			# define upload path for excell
	    	defined('UPLOAD_EXCEL_PATH')
		    	|| define('UPLOAD_EXCEL_PATH', APPLICATION_PATH. '/../data/' );


	    	$pathToFile = UPLOAD_EXCEL_PATH . 'excels/' ;

	    	# create dir if not exists
	    	if(!file_exists($pathToFile)) {
	    		mkdir($pathToFile, 774, TRUE);
	    	}

	    	$filepath = $pathToFile . "shopList.xlsx" ;

			$shopFile = $pathToFile."globalShopList.xlsx";
			 
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save($shopFile);
		}
		
	}
	 
}


new GlobalShopExport();
		
?>