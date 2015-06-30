<?php
class BackEnd_Helper_importShopsExcel
{
    public static function importExcelShops($excelFile)
    {
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load($excelFile);
        $worksheet = $objPHPExcel->getActiveSheet();
        $excelData = array();
        $shopsCounter = 0;
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $excelData[$cell->getRow()][$cell->getColumn()] = $cell->getCalculatedValue();
            }
            $shopName = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['A']);
            $shopNavigationUrl = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['B']);
            $moneyShop = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['C']);
            $shopOnline = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['D']);
            $overwriteTitle = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['E']);
            $metaDescription = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['F']);
            $allowUserGeneratedContent = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['G']);
            $allowDiscussions = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['H']);
            $shopTitle = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['I']);
            $shopSubTitle = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['J']);
            $shopNotes = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['K']);
            $shopRefURL = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['L']);
            $actualURL = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['M']);
            $shopText = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['N']);
            $displaySignupOptions = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['O']);
            $displaySimilarShops = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['P']);
            if (
                (!empty($shopName)
                    && $shopName != 'Shop Name | Text | Required'
                )
                && (
                    !empty($shopNavigationUrl) && $shopNavigationUrl != 'Navigational URL | URL| Required'
                )
            ) {
                $shopId = KC\Repository\Shop::getShopIdByShopName($shopName);
                if (!empty($shopId)) {
                    $shopList = \Zend_Registry::get('emLocale')
                        ->getRepository('KC\Entity\Shop')
                        ->find($shopId);
                    $shopList->created_at = $shopList->created_at;
                    $shopList->name = $shopName;
                    $shopList->permaLink = $shopNavigationUrl;

                    if ($overwriteTitle !='') {
                        $shopList->overriteTitle = $overwriteTitle;
                    }
                    if ($metaDescription !='') {
                        $shopList->metaDescription = $metaDescription;
                    }
                    if ($allowUserGeneratedContent !='') {
                        $shopList->usergenratedcontent = $allowUserGeneratedContent = 0 ? 0 : 1;
                    }
                    if ($allowDiscussions !='') {
                        $shopList->discussions = $allowDiscussions = 0 ? 0 : 1;
                    }
                    if ($shopTitle !='') {
                        $shopList->title = $shopTitle;
                    }
                    if ($shopSubTitle !='') {
                        $shopList->subTitle = $shopSubTitle;
                    }
                    if ($shopNotes !='') {
                        $shopList->notes = $shopNotes;
                    }
                    if ($shopRefURL !='') {
                        $shopList->refUrl = $shopRefURL;
                    }
                    if ($actualURL != '') {
                        $shopList->actualUrl = $actualURL;
                    }
                    if ($moneyShop != '') {
                        $shopList->affliateProgram = $moneyShop = 0 ? false : true;
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
                        $shopList->shopText = $shopText;
                    }
                    $shopList->deleted = 0;
                    $shopList->updated_at = new \DateTime('now');
                    $shopsCounter++;
                    $entityManagerLocale->persist($shopList);
                    $entityManagerLocale->flush();
                }
            }
            unlink($excelFile);
        }
        return $shopsCounter;
    }
}