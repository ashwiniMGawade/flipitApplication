<?php
class BackEnd_Helper_CacheManager
{
    public static function clearResultCache($cacheId) {
        $entityManagerLocale = \Zend_Registry::get('emLocale')
            ->getConfiguration()
            ->getResultCacheImpl()
            ->delete($cacheId);
    }
}