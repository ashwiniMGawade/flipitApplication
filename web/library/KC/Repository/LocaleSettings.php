<?php
namespace KC\Repository;

class LocaleSettings extends \Core\Domain\Entity\LocaleSettings
{

    public function __contruct($connectionName = "")
    {
        if (!$connectionName) {
            $connectionName = "doctrine_site" ;
        }
        Doctrine_Manager::getInstance()->bindComponent($connectionName, $connectionName);
    }

    public static function setLocaleSettings($locale, $timezone)
    {
        $localeSettings = new LocaleSettings();
        $localeSettings->locale = $locale;
        $localeSettings->timezone = $timezone;
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $entityManagerLocale->persist($localeSettings);
        $entityManagerLocale->flush();
        return true;
    }

    public static function getLocaleSettings()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('locale')
            ->from('KC\Entity\LocaleSettings', 'locale');
        $localeInfo = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $localeInfo;
    }

    public static function getLocaleSettingsById()
    {
        $localeSettings = \Zend_Registry::get('emLocale')->find('KC\Entity\LocaleSettings', 1);
        return $localeSettings;
    }

    public static function saveTimezone($timezone)
    {
        $localeFindById = self::getLocaleSettingsById();
        if (empty($localeFindById)) {
            $localeSettings = new LocaleSettings();
            $localeSettings->timezone = $timezone;
            $localeSettings->id = 1;
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $entityManagerLocale->persist($localeSettings);
            $entityManagerLocale->flush();
            return;
        } else {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->update('KC\Entity\LocaleSettings', 'ls')
                    ->set('ls.timezone', $queryBuilder->expr()->literal($timezone))
                    ->setParameter(1, 1)
                    ->where('ls.id = ?1')
                    ->getQuery();
            $query->execute();
        }
    }

    public static function savelocale($locale)
    {
        $localeFindById = self::getLocaleSettingsById();
        if (empty($localeFindById)) {
            $localeSettings = new LocaleSettings();
            $localeSettings->locale = $locale;
            $localeSettings->id = 1;
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $entityManagerLocale->persist($localeSettings);
            $entityManagerLocale->flush();
            return;
        }
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\LocaleSettings', 'ls')
                ->set('ls.locale', $queryBuilder->expr()->literal($locale))
                ->setParameter(1, 1)
                ->where('ls.id = ?1')
                ->getQuery();
        $query->execute();
    }
}
