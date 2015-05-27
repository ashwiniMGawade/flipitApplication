<?php

class Application_Service_Translation_Cache
{
    public $cacheDriver;
    public $cacheId = 'translation_';

    public function __construct()
    {
        $this->cacheDriver = APPLICATION_ENV == 'development'
            ? new Application_Service_Cache_FileCache()
            : Zend_Registry::get('emLocale')->getCacheDriver();
    }

    public function setCache($content, $locale)
    {
        $this->cacheDriver->save($this->cacheId.$locale, $content);
    }

    public function getCache($locale)
    {
        $content = $this->cacheDriver->fetch($this->cacheId.$locale);
        return $content;
    }

    public function cacheExists($locale)
    {
        $cache = $this->cacheDriver->contains($this->cacheId.$locale);
        return $cache;
    }

    public function clearCache($locale)
    {
        $this->cacheDriver->delete($this->cacheId.$locale);
    }
}
