<?php
new EmailSettingsValues();
class EmailSettingsValues
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
                    $this->insertEmailSettings($connection ['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage()."\n\n";
                }
            }
        }
    }

    protected function insertEmailSettings($dsn, $keyIn)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        $logContent = '';
        try {
            $this->addEmailSettingsValues('sender_email_address');
            $this->addEmailSettingsValues('sender_name');
            $logContent .= $keyIn.' - Added'."\n";
        } catch (Exception $e) {
            $logContent .= 'Error: '.$e."\n";
        }

        echo $logContent;
        $manager->closeConnection($doctrineSiteDbConnection);
    }

    protected function addEmailSettingsValues($emailSettingsValue)
    {
        $emailSettings = $this->getEmailSettings($emailSettingsValue);
        if (empty($emailSettings)) {
            $settings = new Settings();
            $settings->name = $emailSettingsValue;
            $settings->created_at = date('Y-m-d H:i:s');
            $settings->updated_at = date('Y-m-d H:i:s');
            $settings->save();
        }
    }

    protected function getEmailSettings($emailSettingsValue)
    {
        $emailSettings = Doctrine_Core::getTable('Settings')->findBy('name', $emailSettingsValue)->toArray();
        return $emailSettings;
    }
}
