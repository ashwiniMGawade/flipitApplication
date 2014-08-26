<?php
class BootstrapConstantsFunctions
{
    public static function constantForCacheDirectory($cacheDirectoryPath)
    {
        defined('HTTP_PATH') || define('HTTP_PATH', trim('http://' . HTTP_HOST . '/'));
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

    public static function constantsForLocale($moduleDirectoryName, $scriptName, $cdnUrl, $scriptFileName)
    {
        defined('LOCALE') || define('LOCALE', trim(strtolower($moduleDirectoryName)));
        define('HTTP_PATH_LOCALE', trim('http://' . HTTP_HOST . '/' . $moduleDirectoryName .'/'));
        self::constantsPublicPathForLocale($scriptName, $moduleDirectoryName, $cdnUrl);
        defined('ROOT_PATH') || define('ROOT_PATH', dirname($scriptFileName).'/'.strtolower($moduleDirectoryName).'/');
        self::constantsImagesForLocale($moduleDirectoryName);
    }
    
    public static function constantsPublicPathForLocale($scriptName, $moduleDirectoryName, $cdnUrl)
    {
        defined('PUBLIC_PATH') || define(
            'PUBLIC_PATH',
            'http://' . HTTP_HOST. dirname($scriptName) . '/'.strtolower($moduleDirectoryName) .'/'
        );
        if (isset($cdnUrl) && isset($cdnUrl[HTTP_HOST])) {
            define('PUBLIC_PATH_CDN', trim('http://'. $cdnUrl[HTTP_HOST].'/'. strtolower($moduleDirectoryName) .'/'));
        } else {
            define('PUBLIC_PATH_CDN', trim('http://' . HTTP_HOST. '/'. strtolower($moduleDirectoryName) .'/'));
        }
    }

    public static function constantsImagesForLocale($moduleDirectoryName)
    {
        defined('UPLOAD_PATH') || define('UPLOAD_PATH', strtolower($moduleDirectoryName) .'/images/');
        defined('UPLOAD_IMG_PATH') || define('UPLOAD_IMG_PATH', UPLOAD_PATH . 'upload/');
        defined('UPLOAD_EXCEL_PATH') || define(
            'UPLOAD_EXCEL_PATH',
            APPLICATION_PATH. '/../data/' .strtolower($moduleDirectoryName) .'/excels/'
        );
        defined('IMG_PATH') || define('IMG_PATH', PUBLIC_PATH . 'images/');
    }
    
    public static function constantsForAdminModule(
        $localeCookieData,
        $siteName,
        $scriptName,
        $scriptFileName,
        $moduleDirectoryName,
        $cdnUrl
    ) {
        $localeAbbreviation = '';
        if (isset($localeCookieData) && ($localeCookieData) != 'en') {
            $localeAbbreviation = $localeCookieData . '/';
            define('LOCALE', trim($localeAbbreviation, '/'));

        } else {
            define('LOCALE', '');
        }

        if (!defined('HTTP_PATH_FRONTEND')) {
            define('HTTP_PATH_FRONTEND', trim('http://www.' . $siteName .'/'));
        }
        self::constantsPublicPathForAdminModule($scriptName, $localeAbbreviation, $scriptFileName);
        self::constantsImagesForAdminModule($localeAbbreviation, $scriptName, $moduleDirectoryName);
        self::constantsCdnForAdminModule($cdnUrl);
    }

    public static function constantsPublicAndRootPathForAdminModule($scriptName, $localeAbbreviation, $scriptFileName)
    {
        defined('PUBLIC_PATH') || define('PUBLIC_PATH', 'http://' . HTTP_HOST . dirname($scriptName) . '/');
        defined('PUBLIC_PATH_LOCALE') || define(
            'PUBLIC_PATH_LOCALE',
            'http://' . HTTP_HOST. dirname($scriptName) . '/' . $localeAbbreviation
        );
        defined('ROOT_PATH') || define('ROOT_PATH', dirname($scriptFileName) . '/' . $localeAbbreviation);
    }

    public static function constantsImagesForAdminModule($localeAbbreviation, $scriptName, $moduleDirectoryName)
    {
        self::constantsUploadImagesForAdminModule($localeAbbreviation);
        defined('IMG_PATH') || define('IMG_PATH', PUBLIC_PATH . 'images/');
        defined('HTTP_PATH_LOCALE') || define(
            'HTTP_PATH_LOCALE',
            'http://' . HTTP_HOST
            . dirname($scriptName) . '/'. strtolower($moduleDirectoryName) .'/'
        );
    }

    public function constantsUploadImagesForAdminModule($localeAbbreviation)
    {
        defined('UPLOAD_PATH') || define('UPLOAD_PATH', 'images/');
        defined('UPLOAD_PATH1') || define('UPLOAD_PATH1', $localeAbbreviation);
        defined('UPLOAD_IMG_PATH') || define('UPLOAD_IMG_PATH', UPLOAD_PATH . 'upload/');
        defined('UPLOAD_EXCEL_PATH') || define(
            'UPLOAD_EXCEL_PATH',
            APPLICATION_PATH. '/../data/' . strtolower($localeAbbreviation) . 'excels/'
        );
    }

    public static function constantsCdnForAdminModule($cdnUrlForAdmin)
    {
        $localePath = LOCALE =='' ? '/' : '/'. strtolower(LOCALE) .'/';
        if (isset($cdnUrlForAdmin) && isset($cdnUrlForAdmin[HTTP_HOST])) {
            define('PUBLIC_PATH_CDN', trim('http://'. $cdnUrlForAdmin[HTTP_HOST] .$localePath));
        } else {
            define('PUBLIC_PATH_CDN', trim('http://' . HTTP_HOST . $localePath));
        }
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
