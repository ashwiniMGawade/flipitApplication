<?php

class Translations extends BaseTranslations
{

    public static function fetchAllDatabaseTranslations()
    {
        return Doctrine_Query::create()
            ->select()
            ->from('translations')
            ->where("translations.deleted='0'")
            ->fetchArray();
    }

    public static function setDbTranslationsToPoTranslate()
    {
        $allDbTranslations = self::fetchAllDatabaseTranslations();

        $poTranslations = array();

        foreach ($allDbTranslations as $dbTranslation) {
            $poTranslation = $dbTranslation['translation'];
            $poTranslationKey = $dbTranslation['translationKey'];
            $poTranslations[$poTranslationKey] =  $poTranslation;
        }

        return $poTranslations;
    }

    public function saveTranslations($translations)
    {
        $existingTranslation =  self::getExistingTranslation($translations);

        if (!empty($existingTranslation[0]['id'])) {
            $translationQuery = Doctrine_Query::create()
                ->update('translations')
                ->set('translation', "'".$translations['nl_NL']."'")
                ->where('id = '.$existingTranslation[0]['id'])
                ->execute();
        } else {
            $translation = $this;
            $translation->translationKey = $translations['translationKey'];
            $translation->translation = $translations['nl_NL'];
            $translation->save();
        }
    }

    public function getExistingTranslation($translation)
    {
        return Doctrine_Query::create()
            ->select()
            ->from('translations')
            ->where("translationKey = '".$translation['translationKey']."'")
            ->fetchArray();
    }

    public function updateTranslationKey($translation)
    {

    }

}
