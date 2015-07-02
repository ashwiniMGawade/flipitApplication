<?php
class BackEnd_Helper_ImportShopsExcel
{
    public static function importExcelShops($excelFile)
    {
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load($excelFile);
        $worksheet = $objPHPExcel->getActiveSheet();
        $excelData = array();
        $shopsPassCounter = 0;
        $shopsFailCounter = 0;
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $excelData[$cell->getRow()][$cell->getColumn()] = $cell->getCalculatedValue();
            }
            $shopName = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['A']);
            $moneyShop = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['B']);
            $shopOnline = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['C']);
            $overwriteTitle = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['D']);
            $metaDescription = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['E']);
            $allowUserGeneratedContent = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['F']);
            $allowDiscussions = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['G']);
            $shopTitle = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['H']);
            $shopSubTitle = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['I']);
            $shopNotes = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['J']);
            $shopRefURL = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['K']);
            $actualURL = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['L']);
            $shopText = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['M']);
            $displaySignupOptions = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['N']);
            $displaySimilarShops = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['O']);
            if (
                (!empty($shopName)
                    && $shopName != 'Shop Name | Text | Required'
                )
            ) {
                $shopId = Shop::getShopIdByShopName($shopName);
                if (!empty($shopId)) {

                    if ($overwriteTitle !='') {
                        $overriteTitle = $overwriteTitle;
                    }
                    if ($metaDescription !='') {
                        $metaDescription = $metaDescription;
                    }
                    if ($allowUserGeneratedContent !='') {
                        if ($allowUserGeneratedContent == 1) {
                            $usergenratedcontent = 1;
                        } else {
                            $usergenratedcontent = 0;
                        }
                    }
                    if ($allowDiscussions !='') {
                        if ($allowDiscussions == 1) {
                            $discussions = 1;
                        } else {
                            $discussions = 0;
                        }
                    }
                    if ($shopTitle !='') {
                        $title = $shopTitle;
                    }
                    if ($shopSubTitle !='') {
                        $subTitle = $shopSubTitle;
                    }
                    if ($shopNotes !='') {
                        $notes = $shopNotes;
                    }
                    if ($shopRefURL !='') {
                        $refUrl = $shopRefURL;
                    }
                    if ($actualURL != '') {
                        $actualUrl = $actualURL;
                    }
                    if ($moneyShop != '') {
                        if ($moneyShop == 0) {
                            $affliateProgram = 0;
                        } else {
                            $affliateProgram = 1;
                        }
                    }
                    if ($shopOnline != '') {
                        if ($shopOnline == 1) {
                            $status = 1;
                            $offlineSicne = null;
                        } else {
                            $status = 0;
                            $offlineSicne = new \DateTime('now');
                        }
                    } else {
                        $status = 1;
                    }
                    if ($shopText != "") {
                        $shopText = $shopText;
                    }
                    if ($displaySignupOptions != '') {
                        if ($displaySignupOptions == 1) {
                            $showsignupoption = 1;
                        } else {
                            $showsignupoption = 0;
                        }
                    }
                    if ($displaySimilarShops != '') {
                        if ($displaySimilarShops == 1) {
                            $showSimliarShops = 1;
                        } else {
                            $showSimliarShops = 0;
                        }
                    }
                    $created_at = date('Y-m-d H:i:s');
                    $updated_at = date('Y-m-d H:i:s');
                    $deletd = 0;
                    $screenshotid = 0;
                    $shopData = array(
                        'name'=> $shopName,
                        'affliateProgram'=> $moneyShop == '' ? 1 : intval($moneyShop),
                        'shopOnline'=> $shopOnline,
                        'overriteTitle'=> $overwriteTitle != '',
                        'metaDescription'=> $metaDescription,
                        'usergenratedcontent'=> $allowUserGeneratedContent == 0 ? 0 : 1,
                        'discussions'=> $allowDiscussions == 0 ? 0 : 1,
                        'title'=> $shopTitle,
                        'subTitle'=> $shopSubTitle,
                        'notes'=> $shopNotes,
                        'refUrl'=> $shopRefURL,
                        'actualUrl'=> $actualURL,
                        'shopText'=> $shopText,
                        'showsignupoption'=> $displaySignupOptions == '' ? 0 : $displaySignupOptions,
                        'showSimliarShops'=> $displaySimilarShops == '' ? 0 : $displaySimilarShops,
                        'screenshotid'=> 0,
                        
                    );
                    self::updateShopData($shopId, $shopData);
                    $shopsPassCounter++;
                } else {
                    $shopsFailCounter++;
                }
            }
        }
        unlink($excelFile);
        return array('passCount'=>$shopsPassCounter, 'failCount'=>$shopsFailCounter);
    }

    public static function updateShopData($shopId, $shopData)
    {   $shop = new Shop();
        $shopId = $shop->CreateNewShop($shopData);
        /*$entityManagerLocale = \Zend_Registry::get('emLocale');
        $shopList = \Zend_Registry::get('emLocale')
            ->getRepository('KC\Entity\Shop')
            ->find($shopId);
        $shopList->created_at = $shopList->created_at;
        $shopList->name = $shopData['shopName'];
        $shopList->permaLink = $shopData['shopNavigationUrl'];

        if ($overwriteTitle !='') {
            $shopList->overriteTitle = $shopData['overwriteTitle'];
        }
        if ($metaDescription !='') {
            $shopList->metaDescription = $shopData['metaDescription'];
        }
        if ($allowUserGeneratedContent !='') {
            $shopList->usergenratedcontent = $shopData['allowUserGeneratedContent'] == 0 ? 0 : 1;
        }
        if ($allowDiscussions !='') {
            $shopList->discussions = $shopData['allowDiscussions'] == 0 ? 0 : 1;
        }
        if ($shopTitle !='') {
            $shopList->title = $shopData['shopTitle'];
        }
        if ($shopSubTitle !='') {
            $shopList->subTitle = $shopData['shopSubTitle'];
        }
        if ($shopNotes !='') {
            $shopList->notes = $shopData['shopNotes'];
        }
        if ($shopRefURL !='') {
            $shopList->refUrl = $shopData['shopRefURL'];
        }
        if ($actualURL != '') {
            $shopList->actualUrl = $shopData['actualURL'];
        }
        if ($moneyShop != '') {
            $shopList->affliateProgram = $shopData['moneyShop']== 0 ? false : true;
        }
        if (!empty($shopOnline)) {
            if ($shopOnline == 1) {
                $shopList->status = 1;
                $shopList->offlineSicne = null;
            } else {
                $shopList->status = 0;
                $shopList->offlineSicne = new \DateTime('now');
            }
        } else {
            $shopList->status = 1;
        }
        if ($shopText != "") {
            $shopList->shopText = $shopData['shopText'];
        }
        $shopList->deleted = 0;
        $shopList->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($shopList);
        $entityManagerLocale->flush();*/
        return true;
    }
}