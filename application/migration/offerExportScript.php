<?php

/**
 * Script for exporting the shops
 *
 * @author Surinderpal Singh
 *
 */
class OfferExport
{
    protected $_localePath = '/';
    protected $_trans = null;


    public function __construct()
    {
    /*
    $domain1 = $_SERVER['HOSTNAME'];
    $domain = 'http://www.'.$domain1;
    */
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

    protected function exportingShops($dsn, $key,$imbull)
    {
        if ($key == 'en') {
            $this->_localePath = '';
            $suffix = "" ;
        } else {
            $this->_localePath = $key . "/";
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

        $cutsomLocale = LocaleSettings::getLocaleSettings();
        $cutsomLocale = !empty($cutsomLocale[0]['locale']) ? $cutsomLocale[0]['locale'] : 'nl_NL';

        $this->_trans = new Zend_Translate(array(
                'adapter' => 'gettext',
                'disableNotices' => true));

        $this->_trans->addTranslation(
                array(
                        'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).'language/fallback/frontend_php' . $suffix . '.mo',
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

        //get all offer from database
        $data = Offer::exportofferList();

        //echo "<pre>";
        //print_r($data); die;
        //create object of phpExcel

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', $this->_trans->translate('Title'));
        $objPHPExcel->getActiveSheet()->setCellValue('B1', $this->_trans->translate('Shop'));
        $objPHPExcel->getActiveSheet()->setCellValue('C1', $this->_trans->translate('Type'));
        $objPHPExcel->getActiveSheet()->setCellValue('D1', $this->_trans->translate('Visibility'));
        $objPHPExcel->getActiveSheet()->setCellValue('E1', $this->_trans->translate('Extended'));
        $objPHPExcel->getActiveSheet()->setCellValue('F1', $this->_trans->translate('Start'));
        $objPHPExcel->getActiveSheet()->setCellValue('G1', $this->_trans->translate('End'));
        $objPHPExcel->getActiveSheet()->setCellValue('H1', $this->_trans->translate('Clickouts'));
        $objPHPExcel->getActiveSheet()->setCellValue('I1', $this->_trans->translate('Author'));
        $objPHPExcel->getActiveSheet()->setCellValue('J1', $this->_trans->translate('Coupon Code'));
        $objPHPExcel->getActiveSheet()->setCellValue('K1', $this->_trans->translate('Ref URL'));
        $objPHPExcel->getActiveSheet()->setCellValue('L1', $this->_trans->translate('Exclusive'));
        $objPHPExcel->getActiveSheet()->setCellValue('M1', $this->_trans->translate('Editor Picks'));
        $objPHPExcel->getActiveSheet()->setCellValue('N1', $this->_trans->translate('User Generated'));
        $objPHPExcel->getActiveSheet()->setCellValue('O1', $this->_trans->translate('Approved'));
        $objPHPExcel->getActiveSheet()->setCellValue('P1', $this->_trans->translate('Offline'));
        $objPHPExcel->getActiveSheet()->setCellValue('Q1', $this->_trans->translate('Created At'));
        $objPHPExcel->getActiveSheet()->setCellValue('R1', $this->_trans->translate('Deeplink'));
        $objPHPExcel->getActiveSheet()->setCellValue('S1', $this->_trans->translate('Terms & Conditions'));

        $column = 2;
        $row = 2;

            // loop for each offer
        foreach ($data as $offer) {

            // condition apply on offer
            $title = '';
            if ($offer['title'] == '' || $offer['title'] == 'undefined'
                    || $offer['title'] == null || $offer['title'] == '0') {

                $title = '';

            } else {

                $title = $offer['title'];
            }
            $shopname = '';
            if (isset($offer['shop'])) {

                if ($offer['shop']['name'] == ''
                        || $offer['shop']['name'] == 'undefined'
                        || $offer['shop']['name'] == null
                        || $offer['shop']['name'] == '0') {

                    $shopname = '';

                } else {

                    $shopname = $offer['shop']['name'];
                }
            }
            $type = '';
            if ($offer['discountType'] == 'CD') {

                $type = $this->_trans->translate('Coupon');

            } elseif ($offer['discountType'] == 'SL') {

                $type = $this->_trans->translate('Sale');

            } else {

                $type = $this->_trans->translate('Printable');
            }
            // get visability name from array
            $Visability = '';
            if ($offer['Visability'] == 'DE') {

                $Visability = $this->_trans->translate('Default');

            } else {

                $Visability = $this->_trans->translate('Members');
            }

            // get extended from array
            $Extended = '';
            if ($offer['extendedOffer'] == true) {

                $Extended = $this->_trans->translate('Yes');

            } else {

                $Extended = $this->_trans->translate('No');
            }

            // create start date format
            $startDate = date("d-m-Y", strtotime($offer['startDate']));
            // end date format
            $endDate = date("d-m-Y", strtotime($offer['endDate']));
            // get Clickouts from array
            $Clickouts = $offer['Count'];
            // get Author from array
            $Author = '';
            if (isset($offer['authorName'])) {

                $Author = $offer['authorName'];

            } else {

                $Author = '';
            }

            $code = '';
            if ($offer['couponCode'] == '' || $offer['couponCode'] == 'undefined'
                    || $offer['couponCode'] == null) {

                $code = '';

            } else {

                $code = $offer['couponCode'];
            }

            $refUrl = '';
            if ($offer['refURL'] == '' || $offer['refURL'] == 'undefined'
                    || $offer['refURL'] == null) {

                $refUrl = '';

            } else {

                $refUrl = $offer['refURL'];
            }

            $exclusive = '';
            if ($offer['exclusiveCode'] == true) {

                $exclusive = $this->_trans->translate('Yes');

            } else {

                $exclusive = $this->_trans->translate('No');
            }

            $editor = '';
            if ($offer['editorPicks'] == true) {

                $editor = $this->_trans->translate('Yes');

            } else {

                $editor = $this->_trans->translate('No');
            }

            $usergenerated = '';
            if ($offer['userGenerated'] == true) {

                $usergenerated = $this->_trans->translate('Yes');

            } else {

                $usergenerated = $this->_trans->translate('No');
            }

            $approved = '';
            if ($offer['approved'] == true) {

                $approved = $this->_trans->translate('Yes');

            } else {

                $approved = $this->_trans->translate('No');
            }

            $offline = '';
            if ($offer['offline'] == true) {

                $offline = $this->_trans->translate('Yes');

            } else {

                $offline = $this->_trans->translate('No');
            }

            $created_at = '';
            if ($offer['created_at'] == '' || $offer['created_at'] == 'undefined'
                    || $offer['created_at'] == null) {

                $created_at = '';

            } else {

                $created_at = date("d-m-Y", strtotime($offer['created_at']));
            }

            $deeplink = '';
            if ($offer['shop']['deepLink'] == '' || $offer['shop']['deepLink'] == 'undefined'
                    || $offer['shop']['deepLink'] == null) {

                $deeplink = '';

            } else {

                $deeplink = $offer['shop']['deepLink'];
            }

            $terms = '';
            if (@$offer['termandcondition'][0]['content'] == '' || @$offer['termandcondition'][0]['content'] == 'undefined'
                    || @$offer['termandcondition'][0]['content'] == null) {

                $terms = '';

            } else {

                $terms = @$offer['termandcondition'][0]['content'];
            }

            // set value in column of excel
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $column, $title);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $column, $shopname);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $column, $type);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $column, $Visability);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $column, $Extended);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $column, $startDate);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $column, $endDate);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $column, $Clickouts);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $column, $Author);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $column, $code);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $column, $refUrl);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $column, $exclusive);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $column, $editor);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $column, $usergenerated);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $column, $approved);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $column, $offline);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $column, $created_at);
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $column, $deeplink);
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $column, $terms);

            // counter incriment by 1
            $column++;
            $row++;

        }

        // FORMATING OF THE EXCELL
        $headerStyle = array(
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '00B4F2')),
                'font' => array('bold' => true));
        $borderStyle = array(
                'borders' => array(
                        'outline' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THICK,
                                'color' => array('argb' => '000000'))));
        // HEADER COLOR

        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'S1')->applyFromArray($headerStyle);

        // SET ALIGN OF TEXT
        $objPHPExcel->getActiveSheet()->getStyle('A1:S1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B2:S' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        // BORDER TO CELL
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'S1')->applyFromArray($borderStyle);
        $borderColumn = (intval($column) - 1);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'S' . $borderColumn)->applyFromArray($borderStyle);

        // SET SIZE OF THE CELL
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


        $pathToFile = UPLOAD_EXCEL_TMP_PATH . strtolower($this->_localePath) . 'excels/' ;

        # create dir if not exists
        if(!file_exists($pathToFile)) {
            mkdir($pathToFile, 0774, TRUE);
        }


        //write to an xlsx file and upload to excel folder locale basis

        $offerFile = $pathToFile."offerList.xlsx";

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($offerFile);

        $manager->closeConnection($DMC);

        echo "\n";
        print "$key - Offers have been exported successfully!!!";

        if($key == 'en') {
            $key = 'excels';
        }

        CommonMigrationFunctions::copyDirectory(UPLOAD_EXCEL_TMP_PATH.$key, UPLOAD_DATA_FOLDER_EXCEL_PATH.$key);
        CommonMigrationFunctions::deleteDirectory(UPLOAD_EXCEL_TMP_PATH.$key);

    }

}


new OfferExport();
