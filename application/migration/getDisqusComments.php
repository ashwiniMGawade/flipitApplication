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
        require_once(LIBRARY_PATH.'/DisqusComments/DQRecentComments.php');

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
        $siteName = $key == 'en' ? 'kortingscodes' : 'flipitcom'.$key;
        $DisqusParameters = array(
            'APIKey' => $disqusAPIKey,
            'forumName' => $siteName,
            'commentCount' => 100,
            'commentLength' => 255
        );
        //get Recent Comments with API
        $DisqusComments = DQGetRecentComments($DisqusParameters);
        if (!empty($DisqusComments)) {
            DisqusComments::saveComments($DisqusComments);
        }
        $manager->closeConnection($doctrineSiteDbConnection);
        echo CommonMigrationFunctions::showProgressMessage(
            "$key - Comments have been saved successfully!!!"
        );
    }
}
new GetDisqusComments();
