 <?php
$application = new Zend_Application(APPLICATION_ENV,
                    APPLICATION_PATH . '/configs/application.ini');
$databaseConnections = $application->getOption('doctrine');
spl_autoload_register(array('Doctrine', 'autoload'));
$doctrineManager = Doctrine_Manager::getInstance();
$imbull = $databaseConnections['imbull'];
$doctrineManagerConnection = Doctrine_Manager::connection($databaseConnections['imbull'], 'doctrine');
