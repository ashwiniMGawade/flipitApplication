<?php
class OneTimeMigrationAddWidgetsInSortList
{
    public function __construct()
    {
        require_once('ConstantForMigration.php');
        require_once('CommonMigrationFunctions.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
        $doctrineImbullDbConnection = CommonMigrationFunctions::getGlobalDbConnection($connections);
        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->addWidgetsInSortList($connection['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function addWidgetsInSortList($dsn, $key)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        echo CommonMigrationFunctions::showProgressMessage("Fetching all widgets of $key");
        $widgets = new Widget();
        $widgetsList = $widgets->getUserDefinedwidgetList();
        PageWidgets::savePageWidgets($widgetsList);
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - widgets has been added sucessfully!!!"
        );
    }
}
new OneTimeMigrationAddWidgetsInSortList();
