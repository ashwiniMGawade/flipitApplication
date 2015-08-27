<?php
class GlobalShopExport
{
    protected $localePath = '/';
    protected $zendTranslation = null;
    protected $shopsData = array();
    protected $row = 4;
    protected $column = 4;
    public $mandrillKey = '';

    public function __construct()
    {
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        require_once(LIBRARY_PATH.'/FrontEnd/Helper/Mailer.php');
        $frontControlerObject = $application->getOption('resources');
        $this->mandrillKey = $frontControlerObject['frontController']['params']['mandrillKey'];
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
        $allShopsData = Shop::exportShopsList();
        $this->shopsData[$key]['data'] = $allShopsData;
        $this->shopsData[$key]['customLocale'] = $customLocale;
        $this->shopsData[$key]['dsn'] = $dsn;
        $manager->closeConnection($doctrineSiteDbConnection);
        echo "\n";
        echo $key." Shops have been fetched successfully!!!";
    }

    public function shopExcelHeaders($key)
    {
        if ($key == 'en') {
            $this->localePath = '';
            $suffix = "" ;
        } else {
            $this->localePath = $key . "/";
            $suffix = "_" . strtoupper($key);
        }
        $customLocale = $this->shopsData[$key]['customLocale'];
        $this->zendTranslation = new Zend_Translate(array(
                'adapter' => 'gettext',
                'disableNotices' => true));
        $this->zendTranslation->addTranslation(
            array(
                    'content' => APPLICATION_PATH.'/../public/'. strtolower($this->localePath)
                    .'language/fallback/frontend_php'
                    . $suffix . '.mo',
                    'locale' => $customLocale,
            )
        );

        $this->zendTranslation->addTranslation(
            array(
                    'content' => APPLICATION_PATH.'/../public/'.strtolower($this->localePath).'language/po_links'
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
        $objPHPExcel->getActiveSheet()->setCellValue('A1', $this->zendTranslation->translate('Generation Date and Time'));
        $objPHPExcel->getActiveSheet()->setCellValue('A3', $this->zendTranslation->translate('Shopname'));
        $objPHPExcel->getActiveSheet()->setCellValue('B3', $this->zendTranslation->translate('Navigation URL'));
        $objPHPExcel->getActiveSheet()->setCellValue('C3', $this->zendTranslation->translate('Money shop'));
        $objPHPExcel->getActiveSheet()->setCellValue('D3', $this->zendTranslation->translate('Classification'));
        $objPHPExcel->getActiveSheet()->setCellValue('E3', $this->zendTranslation->translate('Start'));
        $objPHPExcel->getActiveSheet()->setCellValue('F3', $this->zendTranslation->translate('Network'));
        $objPHPExcel->getActiveSheet()->setCellValue('G3', $this->zendTranslation->translate('Online'));
        $objPHPExcel->getActiveSheet()->setCellValue('H3', $this->zendTranslation->translate('Offline since'));
        $objPHPExcel->getActiveSheet()->setCellValue('I3', $this->zendTranslation->translate('Overwrite Title'));
        $objPHPExcel->getActiveSheet()->setCellValue('J3', $this->zendTranslation->translate('Meta Description'));
        $objPHPExcel->getActiveSheet()->setCellValue('K3', $this->zendTranslation->translate('Allow user generated content'));
        $objPHPExcel->getActiveSheet()->setCellValue('L3', $this->zendTranslation->translate('Allow Discussions'));
        $objPHPExcel->getActiveSheet()->setCellValue('M3', $this->zendTranslation->translate('Title'));
        $objPHPExcel->getActiveSheet()->setCellValue('N3', $this->zendTranslation->translate('Sub-title'));
        $objPHPExcel->getActiveSheet()->setCellValue('O3', $this->zendTranslation->translate('Notes'));
        $objPHPExcel->getActiveSheet()->setCellValue('P3', $this->zendTranslation->translate('Editor'));
        $objPHPExcel->getActiveSheet()->setCellValue('Q3', $this->zendTranslation->translate('Category'));
        $objPHPExcel->getActiveSheet()->setCellValue('R3', $this->zendTranslation->translate('Similar Shops'));
        $objPHPExcel->getActiveSheet()->setCellValue('S3', $this->zendTranslation->translate('Deeplinking code'));
        $objPHPExcel->getActiveSheet()->setCellValue('T3', $this->zendTranslation->translate('Ref URL'));
        $objPHPExcel->getActiveSheet()->setCellValue('U3', $this->zendTranslation->translate('Actual URL'));
        $objPHPExcel->getActiveSheet()->setCellValue('V3', $this->zendTranslation->translate('Shop Text'));
        $objPHPExcel->getActiveSheet()->setCellValue('W3', $this->zendTranslation->translate('Days Without Online Coupons'));
        $objPHPExcel->getActiveSheet()->setCellValue(
            'X3',
            $this->zendTranslation->translate('No. of Times Shop became Favourite')
        );
        $objPHPExcel->getActiveSheet()->setCellValue('Y3', $this->zendTranslation->translate('Last week Clickouts'));
        $objPHPExcel->getActiveSheet()->setCellValue('Z3', $this->zendTranslation->translate('Total Clickouts'));
        $objPHPExcel->getActiveSheet()->setCellValue('AA3', $this->zendTranslation->translate('Amount of Coupons'));
        $objPHPExcel->getActiveSheet()->setCellValue('AB3', $this->zendTranslation->translate('Amount of Offers'));
        $objPHPExcel->getActiveSheet()->setCellValue('AC3', $this->zendTranslation->translate('How To Guide'));
        $objPHPExcel->getActiveSheet()->setCellValue('AD3', $this->zendTranslation->translate('News Ticker'));
        $objPHPExcel->getActiveSheet()->setCellValue('AE3', $this->zendTranslation->translate('Display singup option'));
        $objPHPExcel->getActiveSheet()->setCellValue('AF3', $this->zendTranslation->translate('Display similar shops'));
        $objPHPExcel->getActiveSheet()->setCellValue('AG3', $this->zendTranslation->translate('Display chains'));
        $objPHPExcel->getActiveSheet()->setCellValue('AH3', $this->zendTranslation->translate('Custom Header Text'));
        $objPHPExcel->getActiveSheet()->setCellValue('AI3', $this->zendTranslation->translate('Extra opties'));
        $objPHPExcel->getActiveSheet()->setCellValue('AJ3', $this->zendTranslation->translate('Last Updated'));
        $objPHPExcel->getActiveSheet()->setCellValue('AK3', $this->zendTranslation->translate('Locale'));
        return $objPHPExcel;
    }

    public function excelColumnsData()
    {
        CommonMigrationFunctions::dateFormatConstants();
        $objPHPExcel = $this->shopExcelHeaders('en');
        foreach ($this->shopsData as $locale => $shop) {
            $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($shop['dsn']);
            $manager = CommonMigrationFunctions::loadDoctrineModels();
            $objPHPExcel =  $this->localeShopsData($shop['data'], $locale, $objPHPExcel);
            $this->exportLocaleShopsInExcel($shop['data'], $locale, $doctrineSiteDbConnection, $manager);
        }
        return $objPHPExcel;
    }

    public function localeShopsData($shopData, $key, $objPHPExcel, $localeExport = '')
    {
        $shopClassifications = array(
            1 => 'A',
            2 => 'A+',
            3 => 'AA',
            4 => 'AA+',
            5 => 'AAA'
        );
        if (!empty($localeExport )) {
            $column = 4;
            $row = 4;
        } else {
            $column = $this->column;
            $row = $this->row;
        }
        foreach ($shopData as $shop) {
            $affliateProgram = $shop['affliateProgram'] == true ? 'Yes' : 'No';
            $shopRating = $shopClassifications[$shop['classification']];
            $startDate =  date("d-m-Y", strtotime($shop['created_at']));
            $affilateNetwork = !empty($shop['affname']) ? $shop['affname'] : '';
            $offLine = $shop['status']==true ? 'Yes' : 'No';
            $offLineSince = !empty($shop['offlineSicne']) ? date("d-m-Y", strtotime($shop['offlineSicne'])) : '';
            $overriteTitle = !empty($shop['overriteTitle']) ? $shop['overriteTitle'] : '';
            $metaDesc = !empty($shop['metaDescription']) ? $shop['metaDescription'] : '';
            $userGenerated = $shop['usergenratedcontent']==true ? 'Yes' : 'No';
            $howToGuide = $shop['howToUse']==true ? 'Yes' : 'No';
            $newsTicker = $shop['newsTickerTime'] > 0 ? 'Yes' : 'No';
            $lastUpdated = '';
            $shopTime = strtotime($shop['updated_at']);
            $newTickerTime = isset($shop['newsTickerTime']) ? strtotime($shop['newsTickerTime']) : false;
            $offerTime = isset($shop['offerTime']) ? strtotime($shop['offerTime']) : false;
            $lastUpdated = max($shopTime, $newTickerTime, $offerTime);
            $lastUpdated = date("d-m-Y H:i:s", $lastUpdated);
            $discussion = $shop['discussions'] ==true ? 'Yes' : 'No';
            $title = !empty($shop['title']) ? FrontEnd_Helper_viewHelper::replaceStringVariable($shop['title']) : '';
            $subTitle = !empty($shop['subTitle'])
                ? FrontEnd_Helper_viewHelper::replaceStringVariable($shop['subTitle']) : '';
            $notes = !empty($shop['notes']) ? $shop['notes'] : '';
            $contentManagerName = !empty($shop['contentManagerId']) ? User::getUserName($shop['contentManagerId']) : '';
            $categories = '';
            if (!empty($shop['category'])) {
                $prefix = '';
                foreach ($shop['category'] as $category) {
                    $categories .= $prefix  . $category['name'];
                    $prefix = ', ';
                }
            }
            $relatedShops = '';
            if (!empty($shop['relatedshops'])) {
                $prefix = '';
                foreach ($shop['relatedshops'] as $relatedShop) {
                    $relatedShops .= $prefix  . $relatedShop['name'];
                    $prefix = ', ';
                }
            }
            $deeplink = !empty($shop['deepLink']) ? $shop['deepLink'] : '';
            $refUrl = !empty($shop['refUrl']) ? $shop['refUrl'] : '';
            $actualUrl = !empty($shop['actualUrl']) ? $shop['actualUrl'] : '';
            $shopText = !empty($shop['shopText']) ? $shop['shopText'] : '';
            $showSimilarShops = $shop['showSimliarShops'] == 1 ? 'Yes' : 'No';
            $showSignupOption = $shop['showSignupOption'] == 1 ? 'Yes' : 'No';
            $showChains = $shop['showChains'] == 1 ? 'Yes' : 'No';
            $customHeader = $shop['customHeader'] > 0 ? 'Yes' :  'No';
            $displayExtraProperties = $shop['displayExtraProperties'] == 1 ? 'Yes' : 'No';
            $shopId = $shop['id'];

            $objPHPExcel->getActiveSheet()->setCellValue('A2', date('Y-m-d H:i:s'));
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$column, $shop['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$column, $shop['permaLink']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$column, $affliateProgram);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$column, $shopRating);
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
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$column, $relatedShops);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$column, $deeplink);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$column, $refUrl);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$column, $actualUrl);
            $objPHPExcel->getActiveSheet()->setCellValue('V'.$column, $shopText);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'W'.$column,
                Shop::getDaysSinceShopWithoutOnlneOffers($shopId)
            );
            $objPHPExcel->getActiveSheet()->setCellValue('X'.$column, Shop::getFavouriteCountOfShop($shopId));
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
                Offer::getTotalAmountOfShopCoupons($shopId, 'CD')
            );
            $objPHPExcel->getActiveSheet()->setCellValue('AB'.$column, Offer::getTotalAmountOfShopCoupons($shopId));
            $objPHPExcel->getActiveSheet()->setCellValue('AC'.$column, $howToGuide);
            $objPHPExcel->getActiveSheet()->setCellValue('AD'.$column, $newsTicker);
            $objPHPExcel->getActiveSheet()->setCellValue('AE'.$column, $showSignupOption);
            $objPHPExcel->getActiveSheet()->setCellValue('AF'.$column, $showSimilarShops);
            $objPHPExcel->getActiveSheet()->setCellValue('AG'.$column, $showChains);
            $objPHPExcel->getActiveSheet()->setCellValue('AH'.$column, $customHeader);
            $objPHPExcel->getActiveSheet()->setCellValue('AI'.$column, $displayExtraProperties);
            $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$column, $lastUpdated);

            $localeName = $key ;
            if ($key == 'en') {
                $localeName = 'default' ;
            }

            $objPHPExcel->getActiveSheet()->setCellValue('AK'.$column, $localeName);
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
        if (! empty($this->shopsData)) {
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
            $this->localePath = '';
            $locale = "-NL";
        } else {
            $this->localePath = $key . "/";
            $locale = "-".strtoupper($key);
        }
        $objPHPExcel = $this->shopExcelHeaders($key);
        $objPHPExcel = $this->localeShopsData($shopData, $key, $objPHPExcel, 'locale');
        $objectPHPExcel = $this->excelFormatting($objPHPExcel);
        $pathToFile = UPLOAD_EXCEL_TMP_PATH . strtolower($this->localePath) . 'excels/' ;

        if (!file_exists($pathToFile)) {
            mkdir($pathToFile, 0774, true);
        }

        $filepath = $pathToFile . "shopList".$locale.".xlsx" ;
        $objWriter = PHPExcel_IOFactory::createWriter($objectPHPExcel, 'Excel2007');
        $objWriter->save($filepath);
        $manager->closeConnection($doctrineSiteDbConnection);
        echo "\n";
        print "$key - Shops have been exported successfully!!!";

        $folderName = $key;
        if ($key == 'en') {
            $folderName = 'excels';
        }

        CommonMigrationFunctions::copyDirectory(
            UPLOAD_EXCEL_TMP_PATH.$folderName,
            UPLOAD_DATA_FOLDER_EXCEL_PATH.$folderName
        );
        CommonMigrationFunctions::deleteDirectory(UPLOAD_EXCEL_TMP_PATH.$folderName);
    }
}
new GlobalShopExport();
