<?php
class BootstrapLocaleConstantsFunctions
{
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
}
