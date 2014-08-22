<?php
class BootstrapConstants {
   public static function setCache() {
        $frontendOptions = array(
           'lifetime' => 300,
           'automatic_serialization' => true
        );
        $backendOptions = array('cache_dir' => CACHE_DIRECTORY_PATH);
        $cache = Zend_Cache::factory(
            'Output',
            'File',
            $frontendOptions,
            $backendOptions
        );
        Zend_Registry::set('cache', $cache);
   }
   public static function constantsForFacebookImageAndLocale()
    {
        if (LOCALE == '') {
            defined('FACEBOOK_IMAGE') || define('FACEBOOK_IMAGE', HTTP_PATH."public/images/logo_og.png");
            defined('FACEBOOK_LOCALE') || define('FACEBOOK_LOCALE', '');
        } else {
            defined('FACEBOOK_IMAGE') || define('FACEBOOK_IMAGE', HTTP_PATH."public/images/flipit.png");
            defined('FACEBOOK_LOCALE') || define('FACEBOOK_LOCALE', LOCALE);
        }
    }
    public static function constantForCacheDirectory($cDir)
    {
        defined('HTTP_PATH') || define('HTTP_PATH', trim('http://' . HTTP_HOST . '/'));

        if (APPLICATION_ENV == 'testing') {
            defined('CACHE_DIRECTORY_PATH') || define('CACHE_DIRECTORY_PATH', $cDir);
        } else {
            defined('CACHE_DIRECTORY_PATH') || define('CACHE_DIRECTORY_PATH', './tmp/');
        }
    }

    public static function httpPathConstantForCdn($cdnUrl, $cdnHttpPath)
    {
        if (isset($cdnUrll) && isset($cdnHttpPath)) {
            defined('HTTP_PATH_CDN') || define('HTTP_PATH_CDN', trim('http://'. $cdnHttpPath . '/'));
        } else {
            defined('HTTP_PATH_CDN') || define('HTTP_PATH_CDN', trim('http://' . HTTP_HOST . '/'));
        }
    }

    public static function s3ConstantDefines($s3Credentials)
    {
        defined('S3BUCKET') || define('S3BUCKET', $s3Credentials['bucket']);
        defined('S3KEY') || define('S3KEY', $s3Credentials['key']);
        defined('S3SECRET') || define('S3SECRET', $s3Credentials['secret']);
    }
}
