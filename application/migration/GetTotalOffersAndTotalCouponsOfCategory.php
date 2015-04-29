<?php
/**
 * Script for fetching and storing total offers and coupons for all locales
 *
 */
class GetTotalOffersAndTotalCouponsOfCategory
{
    public function __construct()
    {
        require_once('ConstantForMigration.php');
        require_once('CommonMigrationFunctions.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
        $doctrineImbullDbConnection = CommonMigrationFunctions::getGlobalDbConnection($connections);
        echo CommonMigrationFunctions::showProgressMessage(
            'Fetching total number of coupons and offers for home page categories of all locales'
        );
        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->fetchAndSaveCategoryOffersCount($connection['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }

        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function fetchAndSaveCategoryOffersCount($dsn, $key)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        PopularCategory::saveCategoryOfferCount();
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Offer counts for Homepage Categories have been saved!!!"
        );
    }
}
new GetTotalOffersAndTotalCouponsOfCategory();
