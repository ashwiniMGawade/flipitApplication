<?php
class ShopExcelInformation extends BaseShopExcelInformation
{
    public static function saveShopExcelData($passCount, $failCount, $fileName)
    {
        $explodedFlieName = explode('.', $fileName);
        Doctrine_Query::create()
            ->update('shopExcelInformation')
            ->set('passCount', $passCount)
            ->set('failCount', $failCount)
            ->set('deleted', 1)
            ->where("filename LIKE '$explodedFlieName[0]%'")
            ->execute();
        return true;
    }

    public static function updatePreviousEntry($fileName)
    {
        $explodedFlieName = explode('.', $fileName);
        Doctrine_Query::create()
            ->update('shopExcelInformation')
            ->set('filename', 0)
            ->where("filename LIKE '$explodedFlieName[0]%'")
            ->execute();
            return true;
    }

    public static function getExcelData()
    {
        $excelFileName = Doctrine_Query::create()
            ->select('filename, userName')
            ->from("shopExcelInformation s")
            ->where('s.deleted = 0')
            ->fetchArray();
        if (!empty($excelFileName)) {
            return array('fileName'=>$excelFileName[0]['filename'], 'userName'=>$excelFileName[0]['userName']);
        } else {
            return;
        }
    }
}