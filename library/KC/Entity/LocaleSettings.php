<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="locale_settings")
 */
class LocaleSettings
{
    public function __contruct($connectionName = "")
    {
        if (!$connectionName) {
            $connectionName = "doctrine_site" ;
        }
        Doctrine_Manager::getInstance()->bindComponent($connectionName, $connectionName);
    }
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $locale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $timezone;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
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
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale->select('locale')
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
                    ->where('ls.id = ?1')
                    ->setParameter(1, 1)
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
                ->where('ls.id = ?1')
                ->setParameter(1, 1)
                ->getQuery();
        $query->execute();
    }
}