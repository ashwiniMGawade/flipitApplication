<?php
/**
 * Script for fetching clickouts of shops and offers for all locales
 *
 */
class UpdateShopAndOffersClickouts
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
            'get all clickouts data from databases of all locales'
        );

        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->updateTotalOfferShopViewCount($connection ['dsn'], $key);
                    $this->updateLastSevenDaysShopOfferViewCount($connection ['dsn'], $key);
                    $this->updateShopOfferCount($connection ['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }

        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function updateTotalOfferShopViewCount($dsn, $key)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Getting Clickouts for offers and Shops!!!"
        );
        Shop::updateTotalShopOfferViewCount();
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Total Shop and Offer clicks has been updated successfully!!!"
        );
    }

    protected function updateLastSevenDaysShopOfferViewCount($dsn, $key)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Getting Clickouts for offers and Shops!!!"
        );
        Shop::updateLastSevenDaysOfferShopViewCount();
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Last Seven Day Shop and Offer clicks has been updated successfully!!!"
        );
    }

    protected function updateShopOfferCount($dsn, $key)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Getting offers count of all the shops!!!"
        );
        Shop::updateOfferCount();
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Offer count has been updated successfully fot all shops!!!"
        );
    }
}
new UpdateShopAndOffersClickouts();
