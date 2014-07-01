<?php

class deletePageAttributes
{
    public function __construct()
    {
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');

        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        echo CommonMigrationFunctions::showProgressMessage(
            'Insert Page Attributes'
        );

        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->deletePageAttributes($connection['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
    }

    protected function deletePageAttributes($dsn, $key)
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
new deletePageAttributes();
