<?php
class BackEnd_Helper_importOffersExcel
{
    public static function importExcelOffers($excelFile)
    {
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load($excelFile);
        $worksheet = $objPHPExcel->getActiveSheet();
        $excelData = array();
        $offerList = new Doctrine_Collection('Offer');
        $offerCounter = 0;
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
                $shopId = Shop::getShopIdByShopName($shopName);
                if (!empty($shopId)) {
                    $currentDate = date('Y-m-d');
                    $startDate = date('Y-m-d', strtotime($offerStartDate));
                    $endDate = date('Y-m-d', strtotime($offerEndDate));
                    if ($endDate >= $currentDate) {
                        $offerList[$offerCounter]->title = $offerTitle;
                        $offerList[$offerCounter]->shopId = $shopId;
                        $offerList[$offerCounter]->discountType= !empty($offerCouponCode) ? 'CD' : 'SL';
                        $offerList[$offerCounter]->Visability = !empty($offerVisibility) ? 'DE' : 'MEM';
                        $offerList[$offerCounter]->extendedOffer = 0;
                        $offerList[$offerCounter]->startDate = $startDate;
                        $offerList[$offerCounter]->endDate = $endDate.' 23:59:00';
                        $offerList[$offerCounter]->totalViewcount = !empty($offerClickouts) ? $offerClickouts : 0;
                        $offerList[$offerCounter]->authorName = !empty($offerAuthorName) ? $offerAuthorName : 'Arthur Goldman';
                        $offerList[$offerCounter]->couponCode = !empty($offerCouponCode) ? $offerCouponCode : '';
                        $offerList[$offerCounter]->exclusiveCode = $offerExclusive == 1 ? 1 : 0;
                        $offerList[$offerCounter]->editorPicks = $offerEditorPick == 1 ? 1 : 0;
                        $offerList[$offerCounter]->userGenerated = 0;
                        $offerList[$offerCounter]->offline = 0;
                        $offerList[$offerCounter]->created_at = $currentDate;
                        $offerList[$offerCounter]->refURL = !empty($offerDeeplink) ? $offerDeeplink : '';
                        $offerList[$offerCounter]->tilesId = !empty($offerTileId) ? $offerTileId : '';
                        $offerList[$offerCounter]->maxcode = 0;
                        $offerList[$offerCounter]->deleted = 0;
                        $offerList[$offerCounter]->maxlimit = 0;
                        $offerList[$offerCounter]->updated_at = $currentDate;
                        $offerCounter++;
                    }
                }
            }
            $offerList->save();
            unlink($excelFile);
        }
        return $offerCounter;
    }
}