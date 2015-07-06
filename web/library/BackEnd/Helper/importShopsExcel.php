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
                    $shopData = array();

                    if ($overwriteTitle !='') {
                        $shopData['overriteTitle'] = $overwriteTitle;
                    }
                    if ($metaDescription !='') {
                        $shopData['metaDescription'] = $metaDescription;
                    }
                    if ($allowUserGeneratedContent !='') {
                        if ($allowUserGeneratedContent == 1) {
                            $usergenratedcontent = 1;
                        } else {
                            $usergenratedcontent = 0;
                        }
                        $shopData['usergenratedcontent'] = $usergenratedcontent;
                    }
                    if ($allowDiscussions !='') {
                        if ($allowDiscussions == 1) {
                            $discussions = 1;
                        } else {
                            $discussions = 0;
                        }
                        $shopData['discussions'] = $discussions;
                    }
                    if ($shopTitle !='') {
                        $shopData['title'] = $shopTitle;
                    }
                    if ($shopSubTitle !='') {
                        $shopData['subTitle'] = $shopSubTitle;
                    }
                    if ($shopNotes !='') {
                        $shopData['notes'] = $shopNotes;
                    }
                    if ($shopRefURL !='') {
                        $shopData['refUrl'] = $shopRefURL;
                    }
                    if ($actualURL != '') {
                        $shopData['actualUrl'] = $actualURL;
                    }
                    if ($moneyShop != '') {
                        if ($moneyShop == 0) {
                            $affliateProgram = 0;
                        } else {
                            $affliateProgram = 1;
                        }
                        $shopData['affliateProgram'] = $affliateProgram;
                    }
                    if ($shopOnline != '') {
                        if ($shopOnline == 1) {
                            $status = 1;
                            $offlineSicne = null;
                        } else {
                            $status = 0;
                            $offlineSicne = new \DateTime('now');
                        }
                        $shopData['offlineSicne'] = $offlineSicne;
                        $shopData['status'] = $status;
                    }
                    if ($shopText != "") {
                        $shopData['shopText'] = $shopText;
                    }
                    if ($displaySignupOptions != '') {
                        if ($displaySignupOptions == 1) {
                            $showsignupoption = 1;
                        } else {
                            $showsignupoption = 0;
                        }
                        $shopData['showsignupoption'] = $showsignupoption;
                    }
                    if ($displaySimilarShops != '') {
                        if ($displaySimilarShops == 1) {
                            $showSimliarShops = 1;
                        } else {
                            $showSimliarShops = 0;
                        }
                        $shopData['showSimliarShops'] = $showSimliarShops;
                    }
                    $shopData['updated_at'] = date('Y-m-d H:i:s');
                    $shopData['deleted'] = 0;
                    $shopData['screenshotid'] = 0;
                    Shop::updateShopFromExcelData($shopData, $shopId);
                    $shopsPassCounter++;
                } else {
                    $shopsFailCounter++;
                }
            }
        }
        unlink($excelFile);
        return array('passCount'=>$shopsPassCounter, 'failCount'=>$shopsFailCounter);
    }
}