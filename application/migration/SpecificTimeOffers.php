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
            "$key - Inserting popular articles in database!!!"
        );
        
        $futureOffersOnline = Offer::getFutureOnlineOffers();
        self::refreshVarnish($futureOffersOnline);
        
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Popular articles has been created successfully!!!"
        );
    }

    protected static function refreshVarnish($futureOffersOnline)
    {
        foreach ($futureOffersOnline as $futureOfferIndex => $futureOffer) {
            self::refreshVarnishByRefreshTime($futureOffer['startdate']);
        }
    }

    protected static function refreshVarnishByRefreshTime($offerStartDate)
    {
        if (!empty($offerStartDate)) {
            $varnishObj = new Varnish();
            $refreshTime = '';
            if ($varnishIndex == 'refreshTime') {
                $refreshTime = BackEnd_Helper_viewHelper::convertOfferTimeToServerTime($offerStartDate);
                Varnish::refreshVarnishUrlsByCron($refreshTime);
            }
        }
    }
}
new SpecificTimeOffers();
