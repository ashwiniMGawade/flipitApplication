<?php

class LocaleSettings extends BaseLocaleSettings
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
        $localeSettings->save();
    }

    public static function getLocaleSettings()
    {
        $localeSettings = Doctrine_Query::create()
        ->select("ls.*")
        ->from('LocaleSettings ls')
        ->fetchArray();
        return $localeSettings;
    }

    public static function saveTimezone($timezone)
    {
        $localeSettings = Doctrine_Query::create()
            ->select()
            ->from("LocaleSettings")
            ->where('id = 1')
            ->fetchArray();

        if (empty($localeSettings)) {
            $localeSettingsInformation = new LocaleSettings();
            $localeSettingsInformation->id = 1;
            $localeSettingsInformation->timezone = $timezone;
            $localeSettingsInformation->save();
            return;
        } else {
            Doctrine_Query::create()
                ->update('LocaleSettings')
                ->set('timezone', "'". $timezone . "'")
                ->where('id=1')
                ->execute();
        }
    }

    public static function savelocale($locale)
    {
        $localeSettings = Doctrine_Query::create()
            ->select()
            ->from("LocaleSettings")
            ->where('id = 1')
            ->fetchArray();

        if (empty($localeSettings)) {
            $localeSettingsInformation = new LocaleSettings();
            $localeSettingsInformation->id = 1;
            $localeSettingsInformation->locale = $locale;
            $localeSettingsInformation->save();
            return;
        }
        Doctrine_Query::create()->update('LocaleSettings')
            ->set('locale', "'". $locale . "'")
            ->where('id=1')
            ->execute();
    }

    public static function setLocaleStatus($localeStatus)
    {
        Doctrine_Query::create()
            ->update('LocaleSettings')
            ->set("status", '"'.$localeStatus.'"')
            ->where('id = 1')
            ->execute();
        return true;
    }

    public static function getLocaleStatus($locale)
    {
        $localeStatus = Doctrine_Query::create()->select("status")
            ->from("LocaleSettings")
            ->where("locale = "."'".$locale."'")
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        return $localeStatus;
    }
}
