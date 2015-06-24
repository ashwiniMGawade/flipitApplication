<?php

namespace KC\Repository;

class Special extends \Core\Domain\Entity\Special
{
    public static function getSpecialContent()
    {
        $retVal = self::checkSpecialContent();
        if ($retVal) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('p')
                ->from('\Core\Domain\Entity\Special', 'p')
                ->where('p.id="'.$retVal.'"');
            $special = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $special ;
        }
        return false;
    }

    public static function update($params)
    {

        $retVal = self::checkSpecialContent() ;
        # check if it has integer id of footer
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        if ($retVal) {
            # create object of previous data
            $special = $entityManagerLocale->find("\KC\Entity\Special", $retVal);
            $special->created_at = $special->created_at;
            $special->updated_at = new \DateTime('now');
        } else {
            # new object
            $special = new \KC\Entity\Special();
            $special->created_at = new \DateTime('now');
            $special->updated_at = new \DateTime('now');
        }
        $special->title = $params['title'];
        $special->status = @$params['status'] ? 1 : 0 ;
        $entityManagerLocale->persist($special);
        $entityManagerLocale->flush();
        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homespecial_list');
        if (!$retVal) {
            self::newSpecialSetting($special->id);
        }
        return $special->id;
    }

    public static function checkSpecialContent()
    {
        return \KC\Repository\Settings::getSettings(\KC\RepositorySettings::SPECIAL);
    }


    public static function newSpecialSetting($id)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $settings =  new \KC\Entity\Settings();
        $settings->name =  \KC\Repository\Settings::SPECIAL;
        $settings->value = $id ;
        $settings->created_at = new \DateTime('now');
        $settings->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($settings);
        $entityManagerLocale->flush();
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homespecial_list');
    }
}
