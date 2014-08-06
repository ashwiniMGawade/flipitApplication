<?php
class ShopExport
{
    protected $_localePath = '/';
    protected $_hostName = '';
    protected $_trans = null;

    public function __construct()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        require_once('ConstantForMigration.php');
        require_once('CommonMigrationFunctions.php');
        $application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        $connections = $application->getOption('doctrine');
        spl_autoload_register(array('Doctrine', 'autoload'));
        $manager = Doctrine_Manager::getInstance();
        $imbull = $connections['imbull'];
        $DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');
        echo "\n";
        print "Get all shops data from databases of all locales\n";

        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->exportingShops($connection ['dsn'], $key, $imbull);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
        $manager->closeConnection($DMC1);
    }

    protected function exportingShops($dsn, $key, $imbull)
    {
        if ($key == 'en') {
            $this->_localePath = '';
            $this->_hostName = "http://www.kortingscode.nl";
            $this->_logo = $this->_hostName . "/public/images/front_end/logo-popup.png";
            $suffix = "" ;
            $locale = "";
        } else {
            $this->_localePath = $key . "/";
            $this->_hostName = "http://www.flipit.com";
            $suffix = "_" . strtoupper($key) ;
            $locale = "-".strtoupper($key);
        }

        defined('PUBLIC_PATH')
        || define(
            'PUBLIC_PATH',
            dirname(dirname(dirname(__FILE__)))."/public/"
        );

        $DMC = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        $manager = Doctrine_Manager::getInstance();
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
                    'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).'language/fallback/frontend_php'
                    . $suffix . '.mo',
                    'locale' => $cutsomLocale,
            )
        );

        $this->_trans->addTranslation(
            array(
                    'content' => APPLICATION_PATH.'/../public/'.strtolower($this->_localePath).'language/po_links'
                    . $suffix . '.mo',
                    'locale' => $cutsomLocale ,
            )
        );


        $date = new Zend_Date();
        $month = $date->get(Zend_Date::MONTH_NAME);
        $year = $date->get(Zend_Date::YEAR);
        $day = $date->get(Zend_Date::DAY);

        defined('CURRENT_MONTH')|| define('CURRENT_MONTH', $month);
        defined('CURRENT_YEAR') || define('CURRENT_YEAR', $year);
        defined('CURRENT_DAY')  || define('CURRENT_DAY', $day);
        defined('PUBLIC_PATH') || define(
            'PUBLIC_PATH',
            dirname(dirname(dirname(__FILE__))) . "/public/"
        );
        set_time_limit(0);
        $data =  Shop::exportShopeList();
        echo "\n";
        print "Parse shops data and save it into excel file\n";

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', $this->_trans->translate('Shopname'));
        $objPHPExcel->getActiveSheet()->setCellValue('B1', $this->_trans->translate('Navigation URL'));
        $objPHPExcel->getActiveSheet()->setCellValue('C1', $this->_trans->translate('Money shop'));
        $objPHPExcel->getActiveSheet()->setCellValue('D1', $this->_trans->translate('Start'));
        $objPHPExcel->getActiveSheet()->setCellValue('E1', $this->_trans->translate('Network'));
        $objPHPExcel->getActiveSheet()->setCellValue('F1', $this->_trans->translate('Online'));
        $objPHPExcel->getActiveSheet()->setCellValue('G1', $this->_trans->translate('Offline since'));
        $objPHPExcel->getActiveSheet()->setCellValue('H1', $this->_trans->translate('Overwrite Title'));
        $objPHPExcel->getActiveSheet()->setCellValue('I1', $this->_trans->translate('Meta Description'));
        $objPHPExcel->getActiveSheet()->setCellValue('J1', $this->_trans->translate('Allow user generated content'));
        $objPHPExcel->getActiveSheet()->setCellValue('K1', $this->_trans->translate('Allow Discussions'));
        $objPHPExcel->getActiveSheet()->setCellValue('L1', $this->_trans->translate('Title'));
        $objPHPExcel->getActiveSheet()->setCellValue('M1', $this->_trans->translate('Sub-title'));
        $objPHPExcel->getActiveSheet()->setCellValue('N1', $this->_trans->translate('Notes'));
        $objPHPExcel->getActiveSheet()->setCellValue('O1', $this->_trans->translate('Editor'));
        $objPHPExcel->getActiveSheet()->setCellValue('P1', $this->_trans->translate('Category'));
        $objPHPExcel->getActiveSheet()->setCellValue('Q1', $this->_trans->translate('Similar Shops'));
        $objPHPExcel->getActiveSheet()->setCellValue('R1', $this->_trans->translate('Deeplinking code'));
        $objPHPExcel->getActiveSheet()->setCellValue('S1', $this->_trans->translate('Ref URL'));
        $objPHPExcel->getActiveSheet()->setCellValue('T1', $this->_trans->translate('Actual URL'));
        $objPHPExcel->getActiveSheet()->setCellValue('U1', $this->_trans->translate('Shop Text'));
        $objPHPExcel->getActiveSheet()->setCellValue('V1', $this->_trans->translate('Days Without Online Coupons'));
        $objPHPExcel->getActiveSheet()->setCellValue(
            'W1',
            $this->_trans->translate('No. of Times Shop became Favourite')
        );
        $objPHPExcel->getActiveSheet()->setCellValue('X1', $this->_trans->translate('Last week Clickouts'));
        $objPHPExcel->getActiveSheet()->setCellValue('Y1', $this->_trans->translate('Total Clickouts'));
        $objPHPExcel->getActiveSheet()->setCellValue('Z1', $this->_trans->translate('Amount of Coupons'));
        $objPHPExcel->getActiveSheet()->setCellValue('AA1', $this->_trans->translate('Amount of Offers'));
        $objPHPExcel->getActiveSheet()->setCellValue('AB1', $this->_trans->translate('How To Guide'));
        $objPHPExcel->getActiveSheet()->setCellValue('AC1', $this->_trans->translate('News Ticker'));
        $objPHPExcel->getActiveSheet()->setCellValue('AD1', $this->_trans->translate('Display singup option'));
        $objPHPExcel->getActiveSheet()->setCellValue('AE1', $this->_trans->translate('Display similar shops'));
        $objPHPExcel->getActiveSheet()->setCellValue('AF1', $this->_trans->translate('Display chains'));
        $objPHPExcel->getActiveSheet()->setCellValue('AG1', $this->_trans->translate('Custom Header Text'));
        $objPHPExcel->getActiveSheet()->setCellValue('AH1', $this->_trans->translate('Extra opties'));
        $objPHPExcel->getActiveSheet()->setCellValue('AI1', $this->_trans->translate('Last Updated'));

        $column = 2;
        $row = 2;

        foreach ($data as $shop) {
            print ".";
            $prog = '';
            if ($shop['affliateProgram']==true) {
                $prog = $this->_trans->translate('Yes');
            } else {
                $prog = $this->_trans->translate('No');
            }

            $startDate =  date("d-m-Y", strtotime($shop['created_at']));

            $affilateNetwork = '';
            if ($shop['affname'] == null
                    ||$shop['affname']==''
                    ||$shop['affname']=='undefined') {
                $affilateNetwork = '';
            } else {
                $affilateNetwork = $shop['affname'];
            }

            $offLine = '';
            if ($shop['status']==true) {
                $offLine=$this->_trans->translate('Yes');
            } else {
                $offLine=$this->_trans->translate('No');
            }

            $offLineSince = '';
            if ($shop['offlineSicne'] == 'undefined'
                    || $shop['offlineSicne']==null
                    || $shop['offlineSicne']=='') {
                $offLineSince='';
            } else {
                $offLineSince = date("d-m-Y", strtotime($shop['offlineSicne']));
            }

            $overriteTitle = '';
            if ($shop['overriteTitle'] == 'undefined'
                    || $shop['overriteTitle']==null
                    || $shop['overriteTitle']=='') {
                $overriteTitle='';
            } else {
                $overriteTitle = $shop['overriteTitle'];
            }

            $metaDesc = '';
            if ($shop['metaDescription']=='undefined'
                    || $shop['metaDescription']==null
                    || $shop['metaDescription']=='') {
                $metaDesc='';
            } else {
                $metaDesc = $shop['metaDescription'];
            }

            $userGenerated = '';
            if ($shop['usergenratedcontent']==true) {
                $userGenerated= $this->_trans->translate('Yes');
            } else {
                $userGenerated = $this->_trans->translate('No');
            }

            $howToGuide = '';
            if ($shop['howToUse']==true) {
                $howToGuide = $this->_trans->translate('Yes');
            } else {
                $howToGuide = $this->_trans->translate('No');
            }

            $newsTicker = '';
            if ($shop['newsTickerTime'] > 0) {
                $newsTicker = $this->_trans->translate('Yes');
            } else {
                $newsTicker = $this->_trans->translate('No');
            }

            $lastUpdated = '';
            $shopTime = strtotime(@$shop['updated_at']);
            $newTickerTime = isset($shop['newsTickerTime'])
            ? strtotime($shop['newsTickerTime']) : false;
            $offerTime =	 isset($shop['offerTime'])
            ? strtotime($shop['offerTime']) : false;
            $lastUpdated = max($shopTime, $newTickerTime, $offerTime);
            $lastUpdated = date("d-m-Y H:i:s", $lastUpdated);

            if ($shop['discussions'] ==true) {
                $discussion = $this->_trans->translate('Yes');
            } else {
                $discussion = $this->_trans->translate('No');
            }

            $title = '';
            if ($shop['title']=='undefined'
                    || $shop['title']==null
                    || $shop['title']=='') {
                $title='';
            } else {
                $title = FrontEnd_Helper_viewHelper::replaceStringVariable($shop['title']);
            }

            $subTitle = '';
            if ($shop['subTitle']=='undefined'
                    || $shop['subTitle']==null
                    || $shop['subTitle']=='') {
                $subTitle ='';
            } else {
                $subTitle = FrontEnd_Helper_viewHelper::replaceStringVariable($shop['subTitle']);
            }

            $notes = '';
            if ($shop['notes']=='undefined'
                    || $shop['notes']==null
                    || $shop['notes']=='') {
                $notes ='';
            } else {
                $notes = $shop['notes'];
            }

            $contentManagerName = '';
            if ($shop['contentManagerId']=='undefined'
                    || $shop['contentManagerId']==null
                    || $shop['contentManagerId']=='') {
                $contentManagerName ='';
            } else {
                $contentManagerName = User::getUserName($shop['contentManagerId']);
            }

            $categories = '';
            if (!empty($shop['category'])) {
                $prefix = '';
                foreach ($shop['category'] as $cat) {
                    $categories .= $prefix  . $cat['name'];
                    $prefix = ', ';
                }
            }

            $relatedshops = '';
            if (!empty($shop['relatedshops'])) {
                $prefix = '';
                foreach ($shop['relatedshops'] as $rShops) {
                    $relatedshops .= $prefix  . $rShops['name'];
                    $prefix = ', ';
                }
            }

            $deeplink = '';
            if ($shop['deepLink']=='undefined'
                    || $shop['deepLink']==null
                    || $shop['deepLink']=='') {
                $deeplink ='';
            } else {
                $deeplink = $shop['deepLink'];
            }

            $refUrl = '';
            if ($shop['refUrl']=='undefined'
                    || $shop['refUrl']==null
                    || $shop['refUrl']=='') {
                $refUrl ='';
            } else {
                $refUrl = $shop['refUrl'];
            }

            $actualUrl = '';
            if ($shop['actualUrl']=='undefined'
                    || $shop['actualUrl']==null
                    || $shop['actualUrl']=='') {
                $actualUrl ='';
            } else {
                $actualUrl = $shop['actualUrl'];
            }

            $shopText = '';
            if ($shop['shopText']=='undefined'
                    || $shop['shopText']==null
                    || $shop['shopText']=='') {
                $shopText ='';
            } else {
                $shopText = $shop['shopText'];
            }


            if ($shop['showSimliarShops'] == 1) {
                $showSimliarShops = $this->_trans->translate('Yes');
            } else {
                $showSimliarShops = $this->_trans->translate('No');
            }

            if ($shop['showSignupOption'] == 1) {
                $showSignupOption = $this->_trans->translate('Yes');
            } else {
                $showSignupOption = $this->_trans->translate('No');
            }


            if ($shop['showChains'] == 1) {
                $showChains = $this->_trans->translate('Yes');
            } else {
                $showChains = $this->_trans->translate('No');
            }

            if ($shop['customHeader']) {
                $customHeader = $this->_trans->translate('Yes');
            } else {
                $customHeader = $this->_trans->translate('No');
            }


            if ($shop['displayExtraProperties'] == 1) {
                $displayExtraProperties = $this->_trans->translate('Yes');
            } else {
                $displayExtraProperties = $this->_trans->translate('No');
            }

            $shopId = $shop['id'];
            $daysWithoutCoupon = Shop::getDaysSinceShopWithoutOnlneOffers($shopId);
            $timesShopFavourite = Shop::getTimesShopFavourite($shopId);
            $lastWeekClicks = ShopViewCount::getAmountClickoutOfShop($shopId);
            $totalClicks =  ShopViewCount::getTotalViewCountOfShopAndOffer($shopId);
            $totalAmountCoupons = Offer::getTotalAmountOfCouponsShop($shopId, 'CD');
            $totalAmountOffers = Offer::getTotalAmountOfCouponsShop($shopId);

            //set value in column of excel
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$column, $shop['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$column, $shop['permaLink']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$column, $prog);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$column, $startDate);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$column, $affilateNetwork);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$column, $offLine);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$column, $offLineSince);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$column, $overriteTitle);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$column, $metaDesc);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$column, $userGenerated);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$column, $discussion);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$column, $title);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$column, $subTitle);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$column, $notes);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$column, $contentManagerName);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$column, $categories);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$column, $relatedshops);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$column, $deeplink);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$column, $refUrl);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$column, $actualUrl);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$column, $shopText);
            $objPHPExcel->getActiveSheet()->setCellValue('V'.$column, $daysWithoutCoupon);
            $objPHPExcel->getActiveSheet()->setCellValue('W'.$column, $timesShopFavourite);
            $objPHPExcel->getActiveSheet()->setCellValue('X'.$column, $lastWeekClicks);
            $objPHPExcel->getActiveSheet()->setCellValue('Y'.$column, $totalClicks);
            $objPHPExcel->getActiveSheet()->setCellValue('Z'.$column, $totalAmountCoupons);
            $objPHPExcel->getActiveSheet()->setCellValue('AA'.$column, $totalAmountOffers);
            $objPHPExcel->getActiveSheet()->setCellValue('AB'.$column, $howToGuide);
            $objPHPExcel->getActiveSheet()->setCellValue('AC'.$column, $newsTicker);
            $objPHPExcel->getActiveSheet()->setCellValue('AD'.$column, $showSignupOption);
            $objPHPExcel->getActiveSheet()->setCellValue('AE'.$column, $showSimliarShops);
            $objPHPExcel->getActiveSheet()->setCellValue('AF'.$column, $showChains);
            $objPHPExcel->getActiveSheet()->setCellValue('AG'.$column, $customHeader);
            $objPHPExcel->getActiveSheet()->setCellValue('AH'.$column, $displayExtraProperties);
            $objPHPExcel->getActiveSheet()->setCellValue('AI'.$column, $lastUpdated);



            $column++;
            $row++;
        }

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

        $objPHPExcel->getActiveSheet()->getStyle('A1:'.'AI1')->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle('A1:AI1')->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        );
        $objPHPExcel->getActiveSheet()->getStyle('B2:AI'.$row)->getAlignment()->setVertical(
            PHPExcel_Style_Alignment::VERTICAL_TOP
        );
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.'AI1')->applyFromArray($borderStyle);
        $borderColumn =  (intval($column) -1 );
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.'AI'.$borderColumn)->applyFromArray($borderStyle);

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

        $pathToFile = UPLOAD_EXCEL_TMP_PATH . strtolower($this->_localePath) . 'excels/' ;

        if (!file_exists($pathToFile)) {
            mkdir($pathToFile, 0774, true);
        }

        $filepath = $pathToFile . "shopList".$locale.".xlsx" ;
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($filepath);
        $manager->closeConnection($DMC);
        echo "\n";
        print "$key - Shops have been exported successfully!!!";

        if ($key == 'en') {
            $key = 'excels';
        }

        CommonMigrationFunctions::copyDirectory(UPLOAD_EXCEL_TMP_PATH.$key, UPLOAD_DATA_FOLDER_EXCEL_PATH.$key);
        CommonMigrationFunctions::deleteDirectory(UPLOAD_EXCEL_TMP_PATH.$key);
    }
}
new ShopExport();
