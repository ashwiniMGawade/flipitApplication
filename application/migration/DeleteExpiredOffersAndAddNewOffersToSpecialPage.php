<?php
/**
 * Script for deleting expired and adding new offers for all locales
 *
 */
class DeleteExpiredOffersAndAddNewOffersToSpecialPage
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
            'Deleting all Expired offers and saving new into databases of all locales'
        );
        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->deleteAndSaveSpecialPageOffers($connection['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }

        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function deleteAndSaveSpecialPageOffers($dsn, $key)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Deleting the Expired and Adding New Special Page Offers!!!"
        );
        SpecialPagesOffers::deleteExpiredOffers();
        SpecialPagesOffers::addNewSpecialPageOffers();
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Expired Offers have been Deleted and New are saved successfully!!!"
        );
    }
}
new DeleteExpiredOffersAndAddNewOffersToSpecialPage();
