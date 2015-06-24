<?php
namespace KC\Repository;

class Footer extends \Core\Domain\Entity\Footer
{
    public static function getFooterContent()
    {
        $retVal = self::checkFooterContent() ;
        if ($retVal) {
            # create object of previous data
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->select('f')
                    ->from('\Core\Domain\Entity\Footer', 'f')
                    ->where('f.id = '.$retVal);
            $footer = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $footer ;
        }
        return false ;
    }

    public static function checkFooterContent()
    {
        $id = \KC\Repository\Settings::getSettings(\KC\Repository\Settings::FOOTER);
        if ($id!=2) {
            self::newFooterSetting(2);
            return 2;
        } else {
            return $id;
        }
    }

    public static function newFooterSetting($id)
    {
        $entityManagerUser  = \Zend_Registry::get('emLocale');
        $settings =  new \Core\Domain\Entity\Settings();
        $settings->name =  \KC\Repository\Settings::FOOTER;
        $settings->value = $id ;
        $entityManagerUser->persist($settings);
        $entityManagerUser->flush();
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_footer_page');
    }

    public static function update($params)
    {
        
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $repo = $entityManagerLocale->getRepository('\Core\Domain\Entity\Footer');
        $footer = $repo->find(2);
        $footer->topFooter = \BackEnd_Helper_viewHelper::removeScriptTag($params['topFooter']);
        $footer->middleColumn1 = \BackEnd_Helper_viewHelper::removeScriptTag($params['column1']);
        $footer->middleColumn2 = \BackEnd_Helper_viewHelper::removeScriptTag($params['column2']);
        $footer->middleColumn3 = \BackEnd_Helper_viewHelper::removeScriptTag($params['column3']);
        $footer->middleColumn4 = \BackEnd_Helper_viewHelper::removeScriptTag($params['column4']);
        $footer->bottomFooter = \BackEnd_Helper_viewHelper::removeScriptTag($params['bottomFooter']);
        $entityManagerLocale->persist($footer);
        $entityManagerLocale->flush();
        //self::checkFooterContent();
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_footer_page');
        return $footer->id ;
    }

    public static function getFooter()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('f.id,f.topFooter,f.middleColumn1,f.middleColumn2, f.middleColumn3, f.middleColumn4, f.bottomFooter')
        ->from('\Core\Domain\Entity\Footer', 'f')
        ->where('f.deleted = 0')
        ->setMaxResults(1);
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }
}