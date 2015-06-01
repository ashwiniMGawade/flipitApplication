<?php

class Application_Service_Translation_Cache
{
    public $cacheDriver;
    public $cacheId = 'translation_';

    public function __construct()
    {
        $this->cacheDriver = APPLICATION_ENV == 'testing'
            ? new Application_Service_Cache_FileCache()
            : Zend_Registry::get('emLocale')->getCacheDriver();
    }

    public function setCache($content, $locale)
    {
        $this->cacheDriver->save($this->cacheId.$locale, $content);
    }

    public function getCache($locale)
    {
        return $this->cacheDriver->fetch($this->cacheId.$locale);
    }

    public function cacheExists($locale)
    {
        return $this->cacheDriver->contains($this->cacheId.$locale);
    }

    public function clearCache($locale)
    {
        $this->cacheDriver->delete($this->cacheId.$locale);
    }
}
