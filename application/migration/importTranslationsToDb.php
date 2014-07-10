<?php

new ImportTranslationsToDb();
class ImportTranslationsToDb
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
                    $this->importTranslationsToDb($connection ['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage()."\n\n";
                }
            }
        }
    }

    protected function importTranslationsToDb($dsn, $keyIn)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        $pathToTranslationsCsvFolder = APPLICATION_PATH . '/../public'. ($keyIn != 'en' ? '/'.$keyIn : '') .
            '/language/translations.csv';
        $csvReader = new Application_Service_Infrastructure_Csv_Reader($pathToTranslationsCsvFolder);
        $message = '';

        while ($translationsRow = $csvReader->getRow()) {
            try {
                $translationInformation = Doctrine_Core::getTable('Translations')
                    ->findBy('translationKey', $translationsRow[0])->toArray();
                if (empty($translationInformation)) {
                    $translationInsert = new Translations();
                    $translationInsert->translationKey = $translationsRow[0];
                    $translationInsert->translation = $translationsRow[1];
                    $translationInsert->save();
                    $message = strtoupper($keyIn). ' translations are added sucessfully.';
                } else {
                    $updateTranslations = Doctrine_Core::getTable('Translations')
                        ->find($translationInformation[0]['id']);
                    $updateTranslations->translation = $translationsRow[1];
                    $updateTranslations->updated_at = date('Y-m-d H:i:s');
                    $updateTranslations->save();
                    $message = strtoupper($keyIn). ' translations are updated sucessfully.';
                }
            } catch (Exception $e) {
                $message = 'Error: '.$e."\n";
            }
        }
        echo $message;
        $manager->closeConnection($doctrineSiteDbConnection);
    }
}
