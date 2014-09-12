<?php
class GlobalShopExport
{
    protected $_localePath = '/';
    protected $_trans = null;
    protected $_shopsData = array();
    protected $row = 4;
    protected $column = 4;
    public function __construct()
    {
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
        $doctrineImbullDbConnection = CommonMigrationFunctions::getGlobalDbConnection($connections);
        $imbull = $connections['imbull'];
        echo CommonMigrationFunctions::showProgressMessage(
            'get all shops data from databases of all locales'
        );
        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->getAllShops($connection ['dsn'], $key, $imbull);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
        $this->exportShopsInExcel();
        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function getAllShops($dsn, $key, $imbull)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        $customLocale = LocaleSettings::getLocaleSettings();
        $customLocale = !empty($customLocale[0]['locale']) ? $customLocale[0]['locale'] : 'nl_NL';
        $data = Shop::exportShopList();
        $this->_shopsData[$key]['data'] = $data;
        $this->_shopsData[$key]['customLocale'] = $customLocale;
        $this->_shopsData[$key]['dsn'] = $dsn;
        $manager->closeConnection($doctrineSiteDbConnection);
        echo "\n";
        echo $key." Shops have been fetched successfully!!!";
    }

    public function shopExcelHeaders($key)
    {
        if ($key == 'en') {
            $this->_localePath = '';
            $suffix = "" ;
        } else {
            $this->_localePath = $key . "/";
            $suffix = "_" . strtoupper($key);
        }
        $customLocale = $this->_shopsData[$key]['customLocale'];
        $this->_trans = new Zend_Translate(array(
                'adapter' => 'gettext',
                'disableNotices' => true));
        $this->_trans->addTranslation(
            array(
                    'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath)
                    .'language/fallback/frontend_php'
                    . $suffix . '.mo',
                    'locale' => $customLocale,
            )
        );

        $this->_trans->addTranslation(
            array(
                    'content' => APPLICATION_PATH.'/../public/'.strtolower($this->_localePath).'language/po_links'
                    . $suffix . '.mo',
                    'locale' => $customLocale ,
            )
        );
        $objPHPExcel = $this->getExcelHeaders();
        return  $objPHPExcel;
    }

    public function getExcelHeaders()
    {
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', $this->_trans->translate('Generation Date and Time'));
        $objPHPExcel->getActiveSheet()->setCellValue('A3', $this->_trans->translate('Shopname'));
        $objPHPExcel->getActiveSheet()->setCellValue('B3', $this->_trans->translate('Navigation URL'));
        $objPHPExcel->getActiveSheet()->setCellValue('C3', $this->_trans->translate('Money shop'));
        $objPHPExcel->getActiveSheet()->setCellValue('D3', $this->_trans->translate('Account manager'));
        $objPHPExcel->getActiveSheet()->setCellValue('E3', $this->_trans->translate('Start'));
        $objPHPExcel->getActiveSheet()->setCellValue('F3', $this->_trans->translate('Network'));
        $objPHPExcel->getActiveSheet()->setCellValue('G3', $this->_trans->translate('Online'));
        $objPHPExcel->getActiveSheet()->setCellValue('H3', $this->_trans->translate('Offline since'));
        $objPHPExcel->getActiveSheet()->setCellValue('I3', $this->_trans->translate('Overwrite Title'));
        $objPHPExcel->getActiveSheet()->setCellValue('J3', $this->_trans->translate('Meta Description'));
        $objPHPExcel->getActiveSheet()->setCellValue('K3', $this->_trans->translate('Allow user generated content'));
        $objPHPExcel->getActiveSheet()->setCellValue('L3', $this->_trans->translate('Allow Discussions'));
        $objPHPExcel->getActiveSheet()->setCellValue('M3', $this->_trans->translate('Title'));
        $objPHPExcel->getActiveSheet()->setCellValue('N3', $this->_trans->translate('Sub-title'));
        $objPHPExcel->getActiveSheet()->setCellValue('O3', $this->_trans->translate('Notes'));
        $objPHPExcel->getActiveSheet()->setCellValue('P3', $this->_trans->translate('Editor'));
        $objPHPExcel->getActiveSheet()->setCellValue('Q3', $this->_trans->translate('Category'));
        $objPHPExcel->getActiveSheet()->setCellValue('R3', $this->_trans->translate('Similar Shops'));
        $objPHPExcel->getActiveSheet()->setCellValue('S3', $this->_trans->translate('Deeplinking code'));
        $objPHPExcel->getActiveSheet()->setCellValue('T3', $this->_trans->translate('Ref URL'));
        $objPHPExcel->getActiveSheet()->setCellValue('U3', $this->_trans->translate('Actual URL'));
        $objPHPExcel->getActiveSheet()->setCellValue('V3', $this->_trans->translate('Shop Text'));
        $objPHPExcel->getActiveSheet()->setCellValue('W3', $this->_trans->translate('Days Without Online Coupons'));
        $objPHPExcel->getActiveSheet()->setCellValue(
            'X3',
            $this->_trans->translate('No. of Times Shop became Favourite')
        );
        $objPHPExcel->getActiveSheet()->setCellValue('Y3', $this->_trans->translate('Last week Clickouts'));
        $objPHPExcel->getActiveSheet()->setCellValue('Z3', $this->_trans->translate('Total Clickouts'));
        $objPHPExcel->getActiveSheet()->setCellValue('AA3', $this->_trans->translate('Amount of Coupons'));
        $objPHPExcel->getActiveSheet()->setCellValue('AB3', $this->_trans->translate('Amount of Offers'));
        $objPHPExcel->getActiveSheet()->setCellValue('AC3', $this->_trans->translate('How To Guide'));
        $objPHPExcel->getActiveSheet()->setCellValue('AD3', $this->_trans->translate('News Ticker'));
        $objPHPExcel->getActiveSheet()->setCellValue('AE3', $this->_trans->translate('Display singup option'));
        $objPHPExcel->getActiveSheet()->setCellValue('AF3', $this->_trans->translate('Display similar shops'));
        $objPHPExcel->getActiveSheet()->setCellValue('AG3', $this->_trans->translate('Display chains'));
        $objPHPExcel->getActiveSheet()->setCellValue('AH3', $this->_trans->translate('Custom Header Text'));
        $objPHPExcel->getActiveSheet()->setCellValue('AI3', $this->_trans->translate('Extra opties'));
        $objPHPExcel->getActiveSheet()->setCellValue('AJ3', $this->_trans->translate('Last Updated'));
        $objPHPExcel->getActiveSheet()->setCellValue('AK3', $this->_trans->translate('Locale'));
        return $objPHPExcel;
    }

    public function excelColumnsData()
    {
        CommonMigrationFunctions::dateFormatAndPublicConstant();
        $objPHPExcel = $this->shopExcelHeaders('en');
        foreach ($this->_shopsData as $key => $data) {
            echo "\n";
            echo $key." - Shops are being saved into excel file !!!";
            $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($data['dsn']);
            $manager = CommonMigrationFunctions::loadDoctrineModels();
            $objPHPExcel =  $this->perShopData($data['data'], $key, $objPHPExcel);
            $this->exportLocaleShopsInExcel($data['data'], $key, $doctrineSiteDbConnection, $manager);
        }
        return $objPHPExcel;
    }

    public function perShopData($shopData, $key, $objPHPExcel, $localeExport = '')
    {
        if (!empty($localeExport )) {
            $column = 4;
            $row = 4;
        } else {
            $column = $this->column;
            $row = $this->row;
        }
        foreach ($shopData as $shop) {
            $prog = '';
            if ($shop['affliateProgram'] == true) {
                $prog = 'Yes';
            } else {
                $prog = 'No';
            }

            $accountManagername = '';
            if ($shop['accountManagerName'] == ''
                    ||$shop['accountManagerName']=='undefined'
                    ||$shop['accountManagerName']==null
                    ||$shop['accountManagerName']=='0') {
                $accountManagername ='';
            } else {
                $accountManagername = User::getUserName($shop['accoutManagerId']);
            }

            $startDate =  date("d-m-Y", strtotime($shop['created_at']));
            
            $affilateNetwork = '';
            if ($shop['affname']==null
                    ||$shop['affname']==''
                    ||$shop['affname']=='undefined') {
                $affilateNetwork = '';
            } else {
                $affilateNetwork = $shop['affname'];
            }

            $offLine='';
            if ($shop['status']==true) {
                $offLine = 'Yes';
            } else {
                $offLine = 'No';
            }

            $offLineSince = '';
            if ($shop['offlineSicne']=='undefined'
                    || $shop['offlineSicne']==null
                    || $shop['offlineSicne']=='') {
                $offLineSince='';
            } else {
                $offLineSince = date("d-m-Y", strtotime($shop['offlineSicne']));
            }

            $overriteTitle = '';
            if ($shop['overriteTitle']=='undefined'
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
                $userGenerated= 'Yes';
            } else {
                $userGenerated = 'No';
            }

            $howToGuide = '';
            if ($shop['howToUse']==true) {
                $howToGuide = 'Yes';
            } else {
                $howToGuide = 'No';
            }

            $newsTicker = '';
            if ($shop['newsTickerTime'] > 0) {
                $newsTicker = 'Yes';
            } else {
                $newsTicker = 'No';
            }

            $lastUpdated = '';
            $shopTime = strtotime($shop['updated_at']);
            $newTickerTime = isset($shop['newsTickerTime'])
                    ? strtotime($shop['newsTickerTime']) : false;
            $offerTime =     isset($shop['offerTime'])
                    ? strtotime($shop['offerTime']) : false;
            $lastUpdated = max($shopTime, $newTickerTime, $offerTime);
            $lastUpdated = date("d-m-Y H:i:s", $lastUpdated);

            if ($shop['discussions'] ==true) {
                $discussion = 'Yes';
            } else {
                $discussion = 'No';
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
                $showSimliarShops = 'Yes';
            } else {
                $showSimliarShops = 'No';
            }


            if ($shop['showSignupOption'] == 1) {
                $showSignupOption = 'Yes';
            } else {
                $showSignupOption = 'No';
            }

            if ($shop['showChains'] == 1) {
                $showChains = 'Yes';
            } else {
                $showChains = 'No';
            }

            if ($shop['customHeader']) {
                $customHeader = 'Yes';
            } else {
                $customHeader = 'No';
            }


            if ($shop['displayExtraProperties'] == 1) {
                $displayExtraProperties = 'Yes';
            } else {
                $displayExtraProperties = 'No';
            }
            $shopId = $shop['id'];

            $objPHPExcel->getActiveSheet()->setCellValue('A2', date('Y-m-d H:i:s'));
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$column, $shop['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$column, $shop['permaLink']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$column, $prog);
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
            $objPHPExcel->getActiveSheet()->setCellValue(
                'W'.$column,
                Shop::getDaysSinceShopWithoutOnlneOffers($shopId)
            );
            $objPHPExcel->getActiveSheet()->setCellValue('X'.$column, Shop::getTimesShopFavourite($shopId));
            $objPHPExcel->getActiveSheet()->setCellValue(
                'Y'.$column,
                ShopViewCount::getAmountClickoutOfShop($shopId)
            );
            $objPHPExcel->getActiveSheet()->setCellValue(
                'Z'.$column,
                ShopViewCount::getTotalViewCountOfShopAndOffer($shopId)
            );
            $objPHPExcel->getActiveSheet()->setCellValue(
                'AA'.$column,
                Offer::getTotalAmountOfCouponsShop($shopId, 'CD')
            );
            $objPHPExcel->getActiveSheet()->setCellValue('AB'.$column, Offer::getTotalAmountOfCouponsShop($shopId));
            $objPHPExcel->getActiveSheet()->setCellValue('AC'.$column, $howToGuide);
            $objPHPExcel->getActiveSheet()->setCellValue('AD'.$column, $newsTicker);
            $objPHPExcel->getActiveSheet()->setCellValue('AE'.$column, $showSignupOption);
            $objPHPExcel->getActiveSheet()->setCellValue('AF'.$column, $showSimliarShops);
            $objPHPExcel->getActiveSheet()->setCellValue('AG'.$column, $showChains);
            $objPHPExcel->getActiveSheet()->setCellValue('AH'.$column, $customHeader);
            $objPHPExcel->getActiveSheet()->setCellValue('AI'.$column, $displayExtraProperties);
            $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$column, $lastUpdated);

            $localeVal = $key ;
            if ($key == 'en') {
                $localeVal = 'default' ;
            }

            $objPHPExcel->getActiveSheet()->setCellValue('AK'.$column, $localeVal);
            $column++;
            $row++;
        }
        if (!empty($localeExport )) {
            $column = 4;
            $row = 4;
        } else {
            $this->column = $column;
            $this->row = $row;
        }
        return $objPHPExcel;
    }


    public function getExcelSheet()
    {
        $objPHPExcel = $this->getExcelColumnsData();
        return $objPHPExcel;
    }

    public function getExcelColumnsData()
    {

        $objPHPExcel = $this->excelColumnsData();
        $objPHPExcel = $this->excelFormatting($objPHPExcel);
        return $objPHPExcel;
    }

    public function excelFormatting($objPHPExcel)
    {
        $column = 4;
        $row = 4;
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
                                'color' => array('argb' => '000000'),),),);
        $objPHPExcel->getActiveSheet()->getStyle('A3:'.'AK3')->applyFromArray($headerStyle);

        $objPHPExcel->getActiveSheet()->getStyle('A3:AK3')->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        );
        $objPHPExcel->getActiveSheet()->getStyle('B4:AK'.$row)->getAlignment()->setVertical(
            PHPExcel_Style_Alignment::VERTICAL_TOP
        );
        $objPHPExcel->getActiveSheet()->getStyle('A3:'.'AK3')->applyFromArray($borderStyle);
        $borderColumn =  (intval($column) -1 );
        $objPHPExcel->getActiveSheet()->getStyle('A3:'.'AK'.$borderColumn)->applyFromArray($borderStyle);
        $objPHPExcel = $this->setExcelSizeOfCell($objPHPExcel);
        return  $objPHPExcel;
    }

    public function setExcelSizeOfCell($objPHPExcel)
    {
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
        return  $objPHPExcel;
    }

    protected function exportShopsInExcel()
    {
        if (! empty($this->_shopsData)) {
            echo "\n";
            echo "Parse shops data and save it into excel file\n";
            $objPHPExcel = $this->getExcelSheet();

            $pathToFile = UPLOAD_EXCEL_TMP_PATH . 'excels/' ;

            if (!file_exists($pathToFile)) {
                mkdir($pathToFile, 0774, true);
            }

            $shopFile = $pathToFile."globalShopList.xlsx";
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save($shopFile);
            echo "\n";
            $key = 'excels/';
            CommonMigrationFunctions::copyDirectory($pathToFile, UPLOAD_DATA_FOLDER_EXCEL_PATH.$key);
            CommonMigrationFunctions::deleteDirectory($pathToFile);
        }
    }

    protected function exportLocaleShopsInExcel($shopData, $key, $doctrineSiteDbConnection, $manager)
    {
        if ($key == 'en') {
            $this->_localePath = '';
            $locale = "-NL";
        } else {
            $this->_localePath = $key . "/";
            $locale = "-".strtoupper($key);
        }
        $objPHPExcel = $this->shopExcelHeaders($key);
        $objPHPExcel = $this->perShopData($shopData, $key, $objPHPExcel, 'locale');
        $objectPHPExcel = $this->excelFormatting($objPHPExcel);
        $pathToFile = UPLOAD_EXCEL_TMP_PATH . strtolower($this->_localePath) . 'excels/' ;

        if (!file_exists($pathToFile)) {
            mkdir($pathToFile, 0774, true);
        }

        $filepath = $pathToFile . "shopList".$locale.".xlsx" ;
        $objWriter = PHPExcel_IOFactory::createWriter($objectPHPExcel, 'Excel2007');
        $objWriter->save($filepath);
        $manager->closeConnection($doctrineSiteDbConnection);
        echo "\n";
        print "$key - Shops have been exported successfully!!!";

        if ($key == 'en') {
            $key = 'excels';
        }

        CommonMigrationFunctions::copyDirectory(UPLOAD_EXCEL_TMP_PATH.$key, UPLOAD_DATA_FOLDER_EXCEL_PATH.$key);
        CommonMigrationFunctions::deleteDirectory(UPLOAD_EXCEL_TMP_PATH.$key);
    }
}
new GlobalShopExport();
