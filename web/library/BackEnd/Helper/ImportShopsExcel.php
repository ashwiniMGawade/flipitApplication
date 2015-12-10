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
            $metaDescription = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['B']);
            if (
                (!empty($shopName)
                    && $shopName != 'Shop Name | Text | Required'
                )
            ) {
                $shopId = Shop::getShopIdByShopName($shopName);
                if (!empty($shopId)) {
                    $shopData = array();
                    if ($metaDescription !='') {
                        $shopData['metaDescription'] = $metaDescription;
                    }
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