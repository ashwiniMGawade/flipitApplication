<?php
class Zend_Controller_Action_Helper_Error extends Zend_Controller_Action_Helper_Abstract
{
    public static function getPageParmalink($pagePermalinkWithoutLeftSlash)
    {
        $pagePermalink = rtrim($pagePermalinkWithoutLeftSlash, '/');
        $permalink = explode('/page/', $pagePermalink);
        if (count($permalink) > 0) {
            $pagePermalink = $permalink[0];
        }
        return $pagePermalink;
    }

    public static function getPageNumbering($pagePermalink)
    {
        preg_match("/[^\/]+$/", $pagePermalink, $matches);
        return $matches[0];
    }

    public static function getDefaultPermalink($pagePermalink)
    {
        if (HTTP_PATH != "www.kortingscode.nl") {
            $splitParmalink = explode('/', $pagePermalink[0]);
            if(!empty($splitParmalink[1])):
                 $pagePermalink = $splitParmalink[1];
            else:
                 $pagePermalink = $splitParmalink[0];
            endif;
        } else {
             $pagePermalink = $splitParmalink[0];
        }
        return  $pagePermalink;
    }

    public static function getPermalinkForFlipit($pagePermalink)
    {
        if (LOCALE!='en') {
            $frontEndControllersDirectory = Zend_Controller_Front::getInstance();
            $moduleDirectories = $frontEndControllersDirectory->getControllerDirectory();
            $moduleNames = array_keys($moduleDirectories);
            $routeProperties = explode('/', $pagePermalink);
            if (in_array($routeProperties[0], $moduleNames)) {
                $pagePermalink = "";
                foreach ($routeProperties as $routeIndex => $route) {
                    if ($routeIndex > 0) {
                        $pagePermalink .= $route .'/';
                    }
                }
            }
        }
        return $pagePermalink;
    }
}
