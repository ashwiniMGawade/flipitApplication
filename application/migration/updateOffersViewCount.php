<?php
/**
 * Script for updating view count for last 3 days
 *
 */
class UpdateOffersViewCount
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
            'get all offer view counts data from databases of all locales'
        );

        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->updateOfferViewCount($connection ['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }

        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function updateOfferViewCount($dsn, $key)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Getting view counts for offers!!!"
        );
        $viewCounts = Offer::getOfferViewCountForLast3Days();
        Offer::updateViewCountsForOffers($viewCounts);
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Offer view counts for last three days has been updated successfully!!!"
        );
    }
}
new UpdateOffersViewCount();
