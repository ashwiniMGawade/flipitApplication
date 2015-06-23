<?php
class Zend_Controller_Action_Helper_About extends Zend_Controller_Action_Helper_Abstract
{
    public static function getWebsiteNameWithLocale()
    {
        $splitWebsiteName = explode("//", HTTP_PATH_LOCALE);
        $webSiteNameWithoutRightSlash = rtrim($splitWebsiteName[1], '/');
        return strstr($webSiteNameWithoutRightSlash, "acceptance")
            ? str_replace('acceptance', 'www', "http://".$webSiteNameWithoutRightSlash)
            : "http://".$webSiteNameWithoutRightSlash;
    }
}
