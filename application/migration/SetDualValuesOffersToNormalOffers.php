<?php
class SetDualValuesOffersToNormalOffers
{
    public function __construct()
    {
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $connectionManager = CommonMigrationFunctions::getGlobalDbConnectionManger();
        $doctrineImbullDbConnection = CommonMigrationFunctions::getGlobalDbConnection($connections);
        echo CommonMigrationFunctions::showProgressMessage(
            'set all exclusive and editorPick double selected codes to normal Codes for all locales'
        );
        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->setDoubleSelectedOffersToNormalOffers($connection['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
        $connectionManager->closeConnection($doctrineImbullDbConnection);
    }

    protected function setDoubleSelectedOffersToNormalOffers($dsn, $key)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $connectionManager = CommonMigrationFunctions::loadDoctrineModels();
        Offer::setNoneOptionForDualSelectedEditorAndExclusiveOffers();
        $connectionManager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Set None option for double selected editor and exclusive offers!!!"
        );
    }
}
new SetDualValuesOffersToNormalOffers();
