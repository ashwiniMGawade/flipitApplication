<?php
class BackEnd_Helper_importShopsExcel
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
                    $shopData = array(
                        'shopName'=>$shopName,
                        'shopNavigationUrl'=>$shopNavigationUrl,
                        'moneyShop'=>$moneyShop,
                        'shopOnline'=>$shopOnline,
                        'overwriteTitle'=>$overwriteTitle,
                        'metaDescription'=>$metaDescription,
                        'allowUserGeneratedContent'=>$allowUserGeneratedContent,
                        'allowDiscussions'=>$allowDiscussions,
                        'shopTitle'=>$shopTitle,
                        'shopSubTitle'=>$shopSubTitle,
                        'shopNotes'=>$shopNotes,
                        'shopRefURL'=>$shopRefURL,
                        'actualURL'=>$actualURL,
                        'shopText'=>$shopText,
                        'displaySignupOptions'=>$displaySignupOptions,
                        'displaySimilarShops'=>$displaySimilarShops
                    );
                    self::updateShopData($shopId, $shoData);
                    $shopsPassCounter++;
                } else {
                    $shopsFailCounter++;
                }
            }
            unlink($excelFile);
        }
        return array('passCount'=>$shopsPassCounter, 'failCount'=>$shopsFailCounter);
    }

    public static function updateShopData($shopId, $shopData)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
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
        $entityManagerLocale->flush();
        return true;
    }
}