<?php
class UpdateShopsFromExcel
{
    public function __construct()
    {
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
        $doctrineImbullDbConnection = CommonMigrationFunctions::getGlobalDbConnection($connections);
        $imbull = $connections['imbull'];
        echo CommonMigrationFunctions::showProgressMessage(
            'get all articles data from databases of all locales'
        );

        foreach ($connections as $key => $connection) {
            if ($key == 'en') {
                try {
                    $this->updateShopsInformation($connection['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }

        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function updateShopsInformation($dsn, $key)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Updating Shops Information in database!!!"
        );
        $excelData = ShopExcelInformation::getExcelData();
        if (!empty($excelData)) {
            $folderPath = $key == 'en' ? UPLOAD_DATA_FOLDER_EXCEL_PATH.'excels/'
                : UPLOAD_DATA_FOLDER_EXCEL_PATH.$key.'excels/';
            $fileName = $excelData['fileName'];
            $excelFile = $folderPath.$fileName;
            $updatedInformation = BackEnd_Helper_ImportShopsExcel::importExcelShops($excelFile);
            $passCount = $updatedInformation['passCount'];
            $failCount = $updatedInformation['failCount'];
            $userName = $excelData['userName'];

            ShopExcelInformation::saveShopExcelData($passCount, $failCount, $userName, $fileName);
            echo CommonMigrationFunctions::showProgressMessage(
                "$key - Shops has been Updated successfully!!!"
            );
        } else {
            echo CommonMigrationFunctions::showProgressMessage(
                "$key - There is no file to upload!!!"
            );
        }
        $manager->closeConnection($doctrineSiteDbConnection);
        
    }
}
new UpdateShopsFromExcel();
