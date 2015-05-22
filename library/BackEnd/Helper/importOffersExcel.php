<?php
class BackEnd_Helper_importOffersExcel
{
    public static function importExcelOffers($excelFile)
    {
        defined('DOCTRINE_PATH') || define('DOCTRINE_PATH', LIBRARY_PATH . '/Doctrine1');
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load($excelFile);
        $worksheet = $objPHPExcel->getActiveSheet();
        $excelData = array();
        $offerCounter = 0;
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $excelData[$cell->getRow()][$cell->getColumn()] = $cell->getCalculatedValue();
            }
            $offerTitle = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['A']);
            $shopName = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['B']);
            $offerType = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['C']);
            $offerVisibility = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['D']);
            $offerExtended = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['E']);
            $offerStartDate = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['F']);
            $offerEndDate = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['G']);
            $offerClickouts = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['H']);
            $offerAuthorName = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['I']);
            $offerCouponCode = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['J']);
            $offerExclusive = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['K']);
            $offerEditorPick = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['L']);
            $offerUserGenerated = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['M']);
            $offerOffline = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['N']);
            $offerCreatedAt = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['O']);
            $offerDeeplink = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['P']);
            $offerTileId = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['Q']);
            if (
                (!empty($offerTitle)
                    && $offerTitle != FrontEnd_Helper_viewHelper::__form('form_backend_Offer Title | Text | Required')
                )
                && (
                    !empty($shopName) && $shopName != FrontEnd_Helper_viewHelper::__form('form_backend_Shop Name | Text | Required')
                )
                && (
                    !empty($offerStartDate)
                    && $offerStartDate != FrontEnd_Helper_viewHelper::__form('form_backend_Shop Name | Text | Required')
                )
                && (
                    !empty($offerEndDate)
                    && $offerEndDate != FrontEnd_Helper_viewHelper::__form('form_backend_End Date | DD-MM-YYYY (01-01-1970) | Required | Must be in future')
                )
            ) {
                $shopId = KC\Repository\Shop::getShopIdByShopName($shopName);
                if (!empty($shopId)) {
                    $currentDate = date('Y-m-d');
                    $startDate = date('Y-m-d', strtotime($offerStartDate));
                    $endDate = date('Y-m-d', strtotime($offerEndDate));
                    if ($endDate >= $currentDate) {
                        $offerList = new \KC\Entity\Offer();
                        $offerList->title = $offerTitle;
                        $offerList->shopOffers = $entityManagerLocale->find('KC\Entity\Shop', $shopId);
                        $offerList->discountType = !empty($offerCouponCode) ? 'CD' : 'SL';
                        $offerList->Visability = !empty($offerVisibility) ? 'DE' : 'MEM';
                        $offerList->extendedOffer = 0;
                        $offerList->startDate = new \DateTime($startDate);
                        $offerList->endDate = new \DateTime($endDate.' 23:59:00');
                        $offerList->totalViewcount = !empty($offerClickouts) ? $offerClickouts : 0;
                        $offerList->authorName = !empty($offerAuthorName) ? $offerAuthorName : 'Arthur Goldman';
                        $offerList->couponCode = !empty($offerCouponCode) ? $offerCouponCode : '';
                        $offerList->exclusiveCode = $offerExclusive == 1 ? 1 : 0;
                        $offerList->editorPicks = $offerEditorPick == 1 ? 1 : 0;
                        $offerList->userGenerated = 0;
                        $offerList->offline = 0;
                        $offerList->created_at = new \DateTime('now');
                        $offerList->refURL = !empty($offerDeeplink) ? $offerDeeplink : '';
                        $offerList->tilesId = !empty($offerTileId) ? $offerTileId : '';
                        $offerList->maxcode = 0;
                        $offerList->deleted = 0;
                        $offerList->maxlimit = 0;
                        $offerList->approved = '0';
                        $offerList->updated_at = new \DateTime('now');
                        $offerCounter++;
                        $entityManagerLocale->persist($offerList);
                    }
                }
            }
            unlink($excelFile);
        }
        $entityManagerLocale->flush();
        return $offerCounter;
    }
}