<?php

/**
 * Script for deleting expired popular codes for all locales
 *
 */
class GeneratePopularCodes
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
            'get all popular codes data from databases of all locales'
        );

        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->genereatePopularOffers($connection['dsn'], $key, $imbull);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }

        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function genereatePopularOffers($dsn, $key, $imbull)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - deleting expired  popular codes!!!"
        );
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        PopularCode::deleteExpiredPopularCode($date, false);
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Popular codes has been created successfully!!!"
        );
    }
}
new GeneratePopularCodes();
