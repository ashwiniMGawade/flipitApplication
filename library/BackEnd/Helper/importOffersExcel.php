<?php
class BackEnd_Helper_importOffersExcel
{
    public static function importExcelOffers()
    {
        ini_set('max_execution_time', 115200);
        $params = $this->_getAllParams();
        if ($this->getRequest()->isPost()) {
            if (isset($_FILES['excelFile']['name']) && $_FILES['excelFile']['name'] != '') {
                $uploadResult = BackEnd_Helper_viewHelper::uploadExcel($_FILES['excelFile']['name']);
                $flashMessage = $this->_helper->getHelper('FlashMessenger');
                if ($uploadResult['status'] == 200) {
                    $excelFilePath = $uploadResult['path'];
                    $excelFile = $excelFilePath.$uploadResult['fileName'];
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                    $objPHPExcel = $objReader->load($excelFile);
                    $worksheet = $objPHPExcel->getActiveSheet();
                    $excelData = array();
                    $offerList = new Doctrine_Collection('Offer');
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
                                && $offerTitle != $this->view->translate('backend_Offer Title(Must be filled)')
                            )
                            && (
                                !empty($shopName) && $shopName != $this->view->translate('backend_Shop Name(Must be filled)')
                            )
                            && (
                                !empty($offerStartDate)
                                && $offerStartDate != $this->view->translate('backend_Start Date(Must be filled)')
                            )
                            && (
                                !empty($offerEndDate)
                                && $offerEndDate != $this->view->translate('backend_End Date(Must be filled)')
                            )
                        ) {
                            $shopId = Shop::getShopIdByShopName($shopName);
                            if (!empty($shopId)) {
                                $currentDate = date('Y-m-d');
                                $startDate = date('Y-m-d', strtotime($offerStartDate));
                                $endDate = date('Y-m-d', strtotime($offerEndDate));
                                if ($startDate >= $currentDate && $endDate >= $currentDate) {
                                    $offerList[$shopId]->title = $offerTitle;
                                    $offerList[$shopId]->shopId = $shopId;
                                    $offerList[$shopId]->discountType= !empty($offerCouponCode) ? 'CD' : 'SL';
                                    $offerList[$shopId]->Visability = !empty($offerVisibility) ? 'DE' : 'MEM';
                                    $offerList[$shopId]->extendedOffer = 0;
                                    $offerList[$shopId]->startDate = $startDate;
                                    $offerList[$shopId]->endDate = $endDate.' 23:59:00';
                                    $offerList[$shopId]->totalViewcount = !empty($offerClickouts) ? $offerClickouts : 0;
                                    $offerList[$shopId]->authorName = !empty($offerAuthorName) ? $offerAuthorName : 'Arthur Goldman';
                                    $offerList[$shopId]->couponCode = !empty($offerCouponCode) ? $offerCouponCode : '';
                                    $offerList[$shopId]->exclusiveCode = $offerExclusive == 1 ? 1 : 0;
                                    $offerList[$shopId]->editorPicks = $offerEditorPick == 1 ? 1 : 0;
                                    $offerList[$shopId]->userGenerated = 0;
                                    $offerList[$shopId]->offline = 0;
                                    $offerList[$shopId]->created_at = $currentDate;
                                    $offerList[$shopId]->refURL = !empty($offerDeeplink) ? $offerDeeplink : '';
                                    $offerList[$shopId]->tilesId = LOCALE == 'es' ? 135 : 0;
                                    $offerList[$shopId]->maxcode = 0;
                                    $offerList[$shopId]->deleted = 0;
                                    $offerList[$shopId]->maxlimit = 0;
                                    $offerList[$shopId]->updated_at = $currentDate;
                                    $dataSaved = 1;
                                }
                            }
                        }
                        $offerList->save();
                        unlink($excelFile);
                    }
                    if ($dataSaved) {
                        $message = $this->view->translate('backend_Offers have been imported Successfully!!');
                        $flashMessage->addMessage(array('success' => $message));
                        $this->_redirect(HTTP_PATH . 'admin/offer');
                    } else {
                        $message = $this->view->translate('backend_Problem in your Data!!');
                        $flashMessage->addMessage(array('error' => $message));
                        $this->_redirect(HTTP_PATH . 'admin/offer');
                    }
                } else {
                    $message = $this->view->translate('backend_Problem in your file size!!');
                    $flashMessage->addMessage(array('error' => $message));
                    $this->_redirect(HTTP_PATH . 'admin/offer');
                }
            }
        }
    }
}