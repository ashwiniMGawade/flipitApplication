<?php

/**
 * Script for exporting the shops
 *
 * @author Surinderpal Singh
 *
 */
class VisitorExport {

	protected $_localePath = '/';
	protected $_trans = null;


	function __construct() {

	    require_once('ConstantForMigration.php');
	    require_once('CommonMigrationFunctions.php');
		// Create application, bootstrap, and run
		$application = new Zend_Application(APPLICATION_ENV,
				APPLICATION_PATH . '/configs/application.ini');

		$connections = $application->getOption('doctrine');
		spl_autoload_register(array('Doctrine', 'autoload'));

		$manager = Doctrine_Manager::getInstance();

		$imbull = $connections['imbull'];

		// cycle htoruh all site database
		$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');


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


		$manager->closeConnection($DMC1);
	}

	protected function exportingShops($dsn, $keyIn,$imbull) {

		try {




			if ($keyIn == 'en') {
				$this->_localePath = '';
				$suffix = "" ;
			} else {
				$this->_localePath = $keyIn . "/";
				$suffix = "_" . strtoupper($keyIn) ;
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


			defined ( 'PUBLIC_PATH' ) || define ( 'PUBLIC_PATH', dirname ( dirname ( dirname ( __FILE__ ) ) ) . "/public/" );


			ini_set('memory_limit', '-1');

			//setting to no time limit,
			set_time_limit(0);

			//get data from database (user table)
			$data = Doctrine_Query::create()
					->select('v.*,k.*,fv.shopId,sp.name')
					->from("Visitor v")
					->leftJoin('v.favoritevisitorshops fv')
					->leftJoin('fv.shops sp')
					->leftJoin('v.keywords k')
					->where('v.deleted=0')
					->orderBy("v.id DESC")
					->fetchArray();

			echo "\n";
		    print "Parse visitors data and save it into excel file\n";

			//CREATE A OBJECT OF PHPECEL CLASS
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setCellValue('A1', $this->_trans->translate('Name'));
			$objPHPExcel->getActiveSheet()->setCellValue('B1', $this->_trans->translate('Email'));
			$objPHPExcel->getActiveSheet()->setCellValue('C1', $this->_trans->translate('Gender'));
			$objPHPExcel->getActiveSheet()->setCellValue('D1', $this->_trans->translate('DOB'));
			$objPHPExcel->getActiveSheet()->setCellValue('E1', $this->_trans->translate('Postal Code'));
			$objPHPExcel->getActiveSheet()->setCellValue('F1', $this->_trans->translate('Weekly Newsletter'));
			$objPHPExcel->getActiveSheet()->setCellValue('G1', $this->_trans->translate('Fashion Newsletter'));
			$objPHPExcel->getActiveSheet()->setCellValue('H1', $this->_trans->translate('Travel Newsletter'));
			$objPHPExcel->getActiveSheet()->setCellValue('I1', $this->_trans->translate('Code Alert'));
			$objPHPExcel->getActiveSheet()->setCellValue('J1', $this->_trans->translate('Active'));
			$objPHPExcel->getActiveSheet()->setCellValue('K1', $this->_trans->translate('Keyword'));
			$objPHPExcel->getActiveSheet()->setCellValue('L1', $this->_trans->translate('Favorite Shops'));
			$objPHPExcel->getActiveSheet()->setCellValue('M1', $this->_trans->translate('Registration Date'));

			$column = 2;
			$row = 2;
			foreach ($data as $visitor) {

		        print ".";

				$name  =  $visitor['firstName'] . " " . $visitor['lastName'];

				$gender = '';
				if($visitor['gender'] == 0){

					$gender = 'Male';

				}else{

					$gender = 'Female';
				}

				$dob = '';
				if($visitor['dateOfBirth'] != 'undefined'
						|| $visitor['dateOfBirth'] != null
						|| $visitor['dateOfBirth'] != '' ){
					$dob = $visitor['dateOfBirth'];
				}

				$postal = '';
				if($visitor['postalCode'] != 'undefined'
						|| $visitor['postalCode'] != null
						|| $visitor['postalCode'] != '' ){
					$postal = $visitor['postalCode'];
				}

				$weekNews = '';
				if($visitor['weeklyNewsLetter'] == 1 ){
					$weekNews = $this->_trans->translate('Yes');
				}else{
					$weekNews = $this->_trans->translate('No');
				}

				$fashionNews = '';
				if($visitor['fashionNewsLetter'] == 1 ){
					$fashionNews = $this->_trans->translate('Yes');
				}else{
					$fashionNews = $this->_trans->translate('No');
				}

				$travelNews = '';
				if($visitor['travelNewsLetter'] == 1 ){
					$travelNews = $this->_trans->translate('Yes');
				}else{
					$travelNews = $this->_trans->translate('No');
				}

				$codeAlert = '';
				if($visitor['codeAlert'] == 1 ){
					$codeAlert = $this->_trans->translate('Yes');
				}else{
					$codeAlert = $this->_trans->translate('No');
				}

				$active = '';
				if($visitor['active'] == 1 ){
					$active = $this->_trans->translate('Yes');
				}else{
					$active = $this->_trans->translate('No');
				}

				$keywords = '';
				if(!empty($visitor['keywords'])){
					$prefix = '';
					foreach ($visitor['keywords'] as $key)
					{
						$keywords .= $prefix  . $key['keyword'];
						$prefix = ', ';
					}
				}

				$favoritevisitorshops = '';
				if(!empty($visitor['favoritevisitorshops'])){
					$prefix = '';
					foreach ($visitor['favoritevisitorshops'] as $fav)
					{
						# check if favorite shop and it name exists
						if(isset($fav['shops']) && isset($fav[0]['name']))
						{
							$favoritevisitorshops .= $prefix  . $fav['shops'][0]['name'];
							$prefix = ', ';
						}
					}
				}

				$created_at = $visitor['created_at'];

				//SET VALUE IN CELL
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$column, $name);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$column, $visitor['email']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$column, $gender);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$column, $dob);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$column, $postal);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$column, $weekNews);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$column, $fashionNews);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$column, $travelNews);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$column, $codeAlert);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$column, $active);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$column, $keywords);
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$column, $favoritevisitorshops);
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$column, $created_at);
				//$objPHPExcel->getActiveSheet()->setCellValue('E'.$column, '35');


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

			$objPHPExcel->getActiveSheet()->getStyle('A1:'.'M1')->applyFromArray($headerStyle);

			//SET ALIGN OF TEXT
			$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B2:M'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

			//BORDER TO CELL
			//$objPHPExcel->getActiveSheet()->getStyle('A1:'.'E1')->applyFromArray($borderStyle);
			$borderColumn =  (intval($column) -1 );
			$objPHPExcel->getActiveSheet()->getStyle('A1:'.'M'.$borderColumn)->applyFromArray($borderStyle);

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

	    	$pathToFile = UPLOAD_EXCEL_TMP_PATH . strtolower($this->_localePath) . 'excels/' ;

	    	# create dir if not exists
	    	if(!file_exists($pathToFile)) {
	    		mkdir($pathToFile, 0774, TRUE);
	    	}

	    	$visitorFile  = $pathToFile . "visitorList.xlsx" ;

	    	//write to an xlsx file and upload to excel folder locale basis
	    	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	    	$objWriter->save($visitorFile);

	    	$manager->closeConnection($DMC);

			echo "\n";
			print "$keyIn - Visitors have been exported successfully!!!";
			
			if($keyIn == 'en')
			{
				$keyIn = 'excels';
			}
			 
			CommonMigrationFunctions::copyDirectory(UPLOAD_EXCEL_TMP_PATH.$keyIn, UPLOAD_DATA_FOLDER_EXCEL_PATH.$keyIn);
			CommonMigrationFunctions::deleteDirectory(UPLOAD_EXCEL_TMP_PATH.$keyIn);
			

		} catch (Exception $e) {
			echo $e;
		}
	}

}


new VisitorExport();

?>