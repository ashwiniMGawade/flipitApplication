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
        $imbull = $connections['imbull'];
        echo CommonMigrationFunctions::showProgressMessage(
            'getting all Disqus comments and saving them into databases of all locales'
        );
        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->getAndSaveDisqusComments($connection['dsn'], $key, $imbull);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }

        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function getAndSaveDisqusComments($dsn, $key, $imbull)
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
        $DisqusParameters = array(
            'APIKey' => $disqusAPIKey,
            'forumName' => $siteName,
            'commentCount' => 100,
            'commentLength' => 255
        );
        //get Recent Comments with API
        $DisqusComments = getDisqusRecentComments($DisqusParameters);
        if (!empty($DisqusComments)) {
            DisqusComments::saveComments($DisqusComments);
        }
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Old Comments have been Deleted and New are saved successfully!!!"
        );
    }
}
new GetDisqusComments();
