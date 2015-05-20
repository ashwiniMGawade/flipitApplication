<?php
class OneTimeMigrationAddPageWidgets
{
    public function __construct()
    {
        require_once('ConstantForMigration.php');
        require_once('CommonMigrationFunctions.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $connectionManager = CommonMigrationFunctions::getGlobalDbConnectionManger();
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
        $connectionManager->closeConnection($doctrineImbullDbConnection);
    }

    protected function addWidgetsInSortList($dsn, $key)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $connectionManager = CommonMigrationFunctions::loadDoctrineModels();
        echo CommonMigrationFunctions::showProgressMessage("Fetching all widgets of $key");
        $widgets = new Widget();
        $widgetsList = $widgets->getUserDefinedWidgetList();
        PageWidgets::savePageWidgets($widgetsList);
        Widget::addFunctionNames();
        $connectionManager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - widgets have been added sucessfully!!!"
        );
    }
}
new OneTimeMigrationAddPageWidgets();
