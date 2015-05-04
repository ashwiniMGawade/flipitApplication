<?php
class SpecificTimeOffers
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
            if ($key != 'imbull') {
                try {
                    $this->specificTimeOffers($connection['dsn'], $key, $imbull);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function specificTimeOffers($dsn, $key, $imbull)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Getting offers from database!!!"
        );
        $refreshUrls = Varnish::getAllUrlsByRefreshTime();
        if (!empty($refreshUrls)) {
            Varnish::refreshVarnishUrlsByCron($refreshUrls);
            echo CommonMigrationFunctions::showProgressMessage(
                "$key - Varnish has been refreshed successfully!!!"
            );
        } else {
            echo CommonMigrationFunctions::showProgressMessage(
                "$key - Varnish has already been refreshed !!!"
            );
        }
        $manager->closeConnection($doctrineSiteDbConnection);
    }
}
new SpecificTimeOffers();
