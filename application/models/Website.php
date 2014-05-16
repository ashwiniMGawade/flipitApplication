<?php
class Website extends BaseWebsite
{
    public static function getAllWebsites()
    {
        $websites = Doctrine_Query::create()->select("id, name")
            ->from("Website")
            ->where("deleted=0")
            ->fetchArray();
        return BackEnd_Helper_viewHelper::msort($websites, "name", array("kortingscode.nl"));
    }

    public static function getWebsiteDetails($websiteId = null, $websiteName = null)
    {
        $websiteId =  FrontEnd_Helper_viewHelper::sanitize($websiteId);
        $websites = Doctrine_Query::create()->select("id, name, url")
            ->from("Website")
            ->where("deleted=0");

        if ($websiteName) {
            $websites->andWhere("name = ? ", $websiteName);
        } else {
            $websites->andWhere("id = ? ", $websiteId);
        }      
        return  $websites->fetchOne(null, Doctrine::HYDRATE_ARRAY);
    }
}
