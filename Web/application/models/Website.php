<?php
class Website extends BaseWebsite
{
    public static function getAllWebsites()
    {
        $websites = Doctrine_Query::create()->select("id, name, status")
            ->from("Website")
            ->where("deleted=0")
            ->fetchArray();
        return BackEnd_Helper_viewHelper::msort($websites, "name", array("kortingscode.nl"));
    }

    public static function getWebsiteDetails($websiteId = null, $websiteName = null)
    {
        $websiteId =  FrontEnd_Helper_viewHelper::sanitize($websiteId);
        $websites = Doctrine_Query::create()->select("id, name, url, chain")
            ->from("Website")
            ->where("deleted=0");

        if ($websiteName) {
            $websites->andWhere("name = ? ", $websiteName);
        } else {
            $websites->andWhere("id = ? ", $websiteId);
        }
        return  $websites->fetchOne(null, Doctrine::HYDRATE_ARRAY);
    }

    public static function setLocaleStatus($localeStatus, $websiteName)
    {
        Doctrine_Query::create()
            ->update('Website w')
            ->set("w.status", '"'.$localeStatus.'"')
            ->where("w.name = "."'".$websiteName."'")
            ->execute();
        return true;
    }

    public static function getLocaleStatus($websiteName)
    {
        $localeStatus = Doctrine_Query::create()->select("status")
            ->from("Website")
            ->where("name = "."'".$websiteName."'")
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        return $localeStatus;
    }

    public function saveChain($chain, $websiteName)
    {
        return Doctrine_Query::create()->update('Website')
            ->set('chain', "'". $chain . "'")
            ->where("name = "."'".$websiteName."'")
            ->execute();
    }
}
