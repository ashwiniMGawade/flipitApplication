<?php
/*
 * Script for Generating similar shops viewed widget for all locales
 */
class GenerateSimilarShopsViewed
{
    public function __construct()
    {
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
        $doctrineImbullDbConnection = CommonMigrationFunctions::getGlobalDbConnection($connections);
        echo CommonMigrationFunctions::showProgressMessage(
            'generate similar shops viewed from databases of all locales'
        );
        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->genereateSimilarShopsViwed($connection['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function genereateSimilarShopsViwed($dsn, $key)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - fetching similar shops also viewed Ids!!!"
        );
        Shop::updateSimilarShopsViewedIds();
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - shops also viewed has been created successfully!!!"
        );
    }
}
new GenerateSimilarShopsViewed();
