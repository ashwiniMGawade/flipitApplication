<?php
new localeTimezoneValues();
class localeTimezoneValues
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
                    $this->localeSettings($connection ['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage()."\n\n";
                }
            }
        }
    }

    protected function localeSettings($dsn, $keyIn)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        $logContent = '';
        try {
            $SignupmaxaccountInformation = Signupmaxaccount::getAllMaxAccounts('localeScript');
            $locale =
                isset($SignupmaxaccountInformation[0]['locale']) ? $SignupmaxaccountInformation[0]['locale'] : '';
            $timezone =
                isset($SignupmaxaccountInformation[0]['timezone']) ? $SignupmaxaccountInformation[0]['timezone'] : '';
            $localeSettings = LocaleSettings::getLocaleSettings();
            if (empty($localeSettings)) {
                LocaleSettings::setLocaleSettings($locale, $timezone);
                Signupmaxaccount::alterSignupMaxAccountTable();
                $logContent .= $keyIn.' - Added'."\n";
            }
        } catch (Exception $e) {
            $logContent .= 'Error: '.$e."\n";
        }

        echo $logContent;
        $manager->closeConnection($doctrineSiteDbConnection);
    }
}
