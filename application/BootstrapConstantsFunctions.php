<?php

class BootstrapConstantsFunctions
{
    public static function constantsForLocaleAndTimezoneSetting()
    {
        $localeSettings = KC\Repository\LocaleSettings::getLocaleSettings();
        $locale = !empty($localeSettings[0]['locale']) ? $localeSettings[0]['locale'] : 'nl_NL';
        $localeTimezone = !empty($localeSettings[0]['timezone']) ? $localeSettings[0]['timezone'] : 'Europe/Amsterdam';
        defined('COUNTRY_LOCALE') || define('COUNTRY_LOCALE', $locale);
        defined('LOCALE_TIMEZONE') || define('LOCALE_TIMEZONE', $localeTimezone);
    }

    public static function constantsForSettingRequestHeaders()
    {
        $httpScheme = FrontEnd_Helper_viewHelper::getServerNameScheme();
        defined('REQUEST_URI') || define('REQUEST_URI', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/');
        defined('HTTP_HOST') || define('HTTP_HOST', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $httpScheme.'.kortingscode.nl');
        defined('HTTP_PATH') || define('HTTP_PATH', trim('http://' . HTTP_HOST . '/'));
    }

    public static function constantForCacheDirectory($cacheDirectoryPath)
    {
        if (APPLICATION_ENV == 'testing') {
            defined('CACHE_DIRECTORY_PATH') || define('CACHE_DIRECTORY_PATH', $cacheDirectoryPath);
        } else {
            defined('CACHE_DIRECTORY_PATH') || define('CACHE_DIRECTORY_PATH', './tmp/');
        }
    }

    public static function httpPathConstantForCdn($cdnUrl, $contantsName)
    {
        if (isset($cdnUrl) && isset($cdnUrl[HTTP_HOST])) {
            defined($contantsName) || define($contantsName, trim('http://'. $cdnUrl[HTTP_HOST] . '/'));
        } else {
            defined($contantsName) || define($contantsName, trim('http://' . HTTP_HOST . '/'));
        }
    }

    public static function s3ConstantDefines($s3Credentials)
    {
        defined('S3BUCKET') || define('S3BUCKET', $s3Credentials['bucket']);
        defined('S3KEY') || define('S3KEY', $s3Credentials['key']);
        defined('S3SECRET') || define('S3SECRET', $s3Credentials['secret']);
    }

    public static function constantsForDefaultModule($scriptName, $cdnUrlForDefaultModule, $scriptFileName)
    {
        
        defined('LOCALE') || define('LOCALE', '');
        defined('HTTP_PATH_LOCALE') || define('HTTP_PATH_LOCALE', trim('http://' . HTTP_HOST . '/'));
        defined('PUBLIC_PATH') || define('PUBLIC_PATH', 'http://' . HTTP_HOST. dirname($scriptName) . '/');
        self::httpPathConstantForCdn($cdnUrlForDefaultModule, 'PUBLIC_PATH_CDN');
        self::constantsUploadAndRootPathForDefaultModule($scriptFileName);
        defined('IMG_PATH') || define('IMG_PATH', PUBLIC_PATH . 'images/');
    }
    
    public static function constantsUploadAndRootPathForDefaultModule($scriptFileName)
    {
        defined('ROOT_PATH') || define('ROOT_PATH', dirname($scriptFileName) . '/');
        defined('UPLOAD_PATH') || define('UPLOAD_PATH', 'images/');
        defined('UPLOAD_IMG_PATH') || define('UPLOAD_IMG_PATH', UPLOAD_PATH . 'upload/');
        defined('UPLOAD_EXCEL_PATH') || define('UPLOAD_EXCEL_PATH', 'excels/');
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
}
