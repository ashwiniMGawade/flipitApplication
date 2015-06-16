<?php
new specialPagesOffersCount();
class specialPagesOffersCount
{
    public function __construct()
    {
        require_once('ConstantForMigration.php');
        require_once('CommonMigrationFunctions.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->specialPagesOffersCount($connection ['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage()."\n\n";
                }
            }
        }
    }

    protected function specialPagesOffersCount($dsn, $keyIn)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        $logContent = '';
        try {
            $specialPagesList = Page::getSpecialListPages();
            foreach ($specialPagesList as $specialPageKey => $specialPage) {
                Page::addSpecialPagesOffersCount($specialPage['id'], count(Offer::getSpecialPageOffersByFallBack($specialPage)));
            }

            $logContent .= $keyIn.' - Added'."\n";
        } catch (Exception $e) {
            $logContent .= 'Error: '.$e."\n";
        }

        echo $logContent;
        $manager->closeConnection($doctrineSiteDbConnection);
    }
}
