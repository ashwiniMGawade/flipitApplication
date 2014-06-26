<?php

class Translations extends BaseTranslations
{

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
