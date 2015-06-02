<?php
/**
 * Script for getting disqus comments for all locales
 *
 */
class GetDisqusComments
{
    public function __construct()
    {
        require_once('ConstantForMigration.php');
        require_once('CommonMigrationFunctions.php');
        require_once(LIBRARY_PATH.'/DisqusComments/DisqusRecentComments.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
        $doctrineImbullDbConnection = CommonMigrationFunctions::getGlobalDbConnection($connections);
        echo CommonMigrationFunctions::showProgressMessage(
            'getting all Disqus comments and saving them into databases of all locales'
        );
        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->getAndSaveDisqusComments($connection['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }

        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function getAndSaveDisqusComments($dsn, $key)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - saving Disqus Comments!!!"
        );
        $application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        $frontControllerObject = $application->getOption('resources');
        $disqusAPIKey = $frontControllerObject['frontController']['params']['disqusKey'];
        if ($key == 'en') {
            $siteName = 'kortingscodes';
        } elseif ($key == 'in') {
            $siteName = 'wwwflipitcom'.$key;
        } else {
            $siteName = 'flipitcom'.$key;
        }
        $disqusParameters = array(
            'DISQUS_API_SECRET' => $disqusAPIKey,
            'DISQUS_FORUM_SHORTNAME' => $siteName,
            'DISQUS_FETCH_LIMIT' => 100,
            'DISQUS_FETCH_ORDER' => 'asc'
        );
        $disqusComments = getDisqusRecentComments($disqusParameters);
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - New Comments are saved successfully!!!"
        );
    }
}
new GetDisqusComments();
