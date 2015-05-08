<?php
/**
 * Script for setting code alerts to 1 of visitors for all locales
 *
 */
class SetCodeAlertForVisitors
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
            'Setting code alert to 1 for Visitors of all locales'
        );
        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->updateCodeAlertStatus($connection['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }

        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function updateCodeAlertStatus($dsn, $key)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        Visitor::updateVisitorCodeAlertStatus();
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Code alert status has been set!!!"
        );
    }
}
new SetCodeAlertForVisitors();
