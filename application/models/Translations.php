<?php

class Translations extends BaseTranslations
{

    public static function getAllDatabaseTranslations()
    {
        return Doctrine_Query::create()
            ->select()
            ->from('translations')
            ->where("translations.deleted='0'")
            ->fetchArray();
    }

    public static function getDbTranslationsForZendTranslate()
    {
        $allDbTranslations = self::getAllDatabaseTranslations();
        $poTranslations = array();

        foreach ($allDbTranslations as $dbTranslation) {
            $poTranslation = $dbTranslation['translation'];
            $poTranslationKey = $dbTranslation['translationKey'];
            $poTranslations[$poTranslationKey] =  $poTranslation;
        }

        return $poTranslations;
    }

    public static function getCsvWritableTranslations()
    {
        $poTranslations = '';
        $allDbTranslations = self::getAllDatabaseTranslations();

        foreach ($allDbTranslations as $translationNumber => $dbTranslation) {
            $poTranslations[$translationNumber] = array($dbTranslation['translationKey'], $dbTranslation['translation']);
            $translationNumber++;
        }

        return $poTranslations;
    }

    public function saveTranslations($translations)
    {
        $existingTranslation =  self::getExistingTranslation($translations);
        if (!empty($existingTranslation[0]['id'])) {
            $translationQuery = Doctrine_Query::create()
                ->update('translations')
                ->set(
                    'translation',
                    "'".mysqli_real_escape_string(
                        FrontEnd_Helper_viewHelper::getDbConnectionDetails(),
                        htmlentities($translations[(string)Zend_Registry::get('Zend_Locale')])
                    )."'"
                )
                ->where('id = '.$existingTranslation[0]['id'])
                ->execute();
        } else {
            $translation = $this;
            $translation->translationKey = $translations['translationKey'];
            $translation->translation = htmlentities($translations[(string)Zend_Registry::get('Zend_Locale')]);
            $translation->save();
        }
    }

    public function getExistingTranslation($translation)
    {
        return Doctrine_Query::create()
            ->select()
            ->from('translations')
            ->where(" BINARY translationKey = '".$translation['translationKey']."'")
            ->fetchArray();
    }

}
