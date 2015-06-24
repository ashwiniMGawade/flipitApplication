<?php
namespace KC\Repository;
class Translations extends \Core\Domain\Entity\Translations
{
    public static function getAllDatabaseTranslations()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('translations')
        ->from('\Core\Domain\Entity\Translations', 'translations')
        ->where('translations.deleted = 0');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
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
        $translationsAfterRemovingTags = \BackEnd_Helper_viewHelper::removeScriptTag($translations);
        $existingTranslation = self::getExistingTranslation($translationsAfterRemovingTags);
        if (!empty($existingTranslation[0]['id'])) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $queryBuilder->update('KC\Entity\Translations', 't')
                ->set(
                    't.translation',
                    "'".$translationsAfterRemovingTags[(string)\Zend_Registry::get('Zend_Locale')]."'"
                )
                ->where('t.id = '.$existingTranslation[0]['id'])
                ->getQuery()->execute();
        } else {
            $entityManagerLocale  = \Zend_Registry::get('emLocale');
            $translation = new \KC\Entity\Translations();
            $translation->translationKey = $translationsAfterRemovingTags['translationKey'];
            $translation->translation = $translationsAfterRemovingTags[(string) \Zend_Registry::get('Zend_Locale')];
            $translation->deleted = 0;
            $translation->created_at = new \DateTime('now');
            $translation->updated_at = new \DateTime('now');
            $entityManagerLocale->persist($translation);
            $entityManagerLocale->flush();
        }
    }

    public function getExistingTranslation($translation)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('t')
        ->from('\Core\Domain\Entity\Translations', 't')
        ->where("t.translationKey = '".$translation['translationKey']."'");
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }
}