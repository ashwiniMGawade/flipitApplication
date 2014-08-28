<?php
class BootstrapAdminConstantsFunctions
{
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
        self::constantsPublicAndRootPathForAdminModule($scriptName, $localeAbbreviation, $scriptFileName);
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

    public static function constantsUploadImagesForAdminModule($localeAbbreviation)
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
}
