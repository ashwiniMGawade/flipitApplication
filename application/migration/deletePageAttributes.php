<?php

class deletePageAttributes
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
            'Insert Page Attributes'
        );

        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->deletePageAttributes($connection['dsn'], $key, $imbull);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function deletePageAttributes($dsn, $key, $imbull)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Deleting page attributes!!!"
        );
        PageAttribute::deletePageAttributes();
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Attributes have been deleted successfully!!!"
        );
    }
}
new DeletePageAttributes();
