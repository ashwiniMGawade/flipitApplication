<?php
class ShopExcelInformation extends BaseShopExcelInformation
{
    public static function saveShopExcelData($passCount, $failCount, $userName, $fileName)
    {
        self::updatePreviousEntry($fileName);
        $excelInformation =  new ShopExcelInformation();
        $excelInformation->totalShopsCount = 0;
        $excelInformation->passCount = $passCount;
        $excelInformation->failCount = $failCount;
        $excelInformation->filename = '';
        $excelInformation->userName = $userName;
        $excelInformation->deleted = 0;
        $excelInformation->created_at = '';
        $excelInformation->updated_at = date('Y-m-d H:i:s');
        $excelInformation->save();
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
            ->andWhere('s.passCount = 0')
            ->andWhere('s.failCount = 0')
            ->fetchArray();
        if (!empty($excelFileName)) {
            return array('fileName'=>$excelFileName[0]['filename'], 'userName'=>$excelFileName[0]['userName']);
        } else {
            return;
        }
    }
}