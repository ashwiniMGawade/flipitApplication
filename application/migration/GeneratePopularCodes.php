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
        //date_default_timezone_set('Asia/Calcutta');
        //uncommnet this line when you run for one locale
        //$this->genereatePopularOffers($connections['in']['dsn'], 'in', $imbull);
        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function genereatePopularOffers($dsn, $key, $imbull)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
       
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - geneating new popular codes!!!"
        );
        
        PopularCode::generatePopularCode(false);
        
        $manager->closeConnection($doctrineSiteDbConnection);

        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Popular codes has been created successfully!!!"
        );
    }
}
new GeneratePopularCodes();
