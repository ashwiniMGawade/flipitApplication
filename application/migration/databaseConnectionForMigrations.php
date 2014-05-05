 <?php
$application = new Zend_Application(APPLICATION_ENV,
                    APPLICATION_PATH . '/configs/application.ini');
$databaseConnections = $application->getOption('doctrine');
spl_autoload_register(array('Doctrine', 'autoload'));
$doctrineManager = Doctrine_Manager::getInstance();
$imbull = $databaseConnections['imbull'];
$doctrineManagerConnection = Doctrine_Manager::connection($databaseConnections['imbull'], 'doctrine');
foreach ($databaseConnections as $key => $databaseConnection ) {
    if ($key != 'imbull') {
        try {
                $this->deleteArticleCategory($databaseConnection ['dsn'], $key, $imbull);
            } catch (Exception $e) {
                echo $e->getMessage ();
                echo "\n\n";
            }
        echo "\n\n";
    }
}
$doctrineManager->closeConnection($doctrineManagerConnection);