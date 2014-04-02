<?php

/**
 * Script for generating popular codes for all locales
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
       
        // cycle htoruh all site database
        foreach ($connections as $key => $connection) {
            // check database is being must be site
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
        //uncommnet this line when you run for one locale
        //$this->genereatePopularOffers($connections['be']['dsn'], 'be', $imbull);
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
