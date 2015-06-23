<?php
namespace KC\Repository;
class PageAttribute extends \Core\Domain\Entity\PageAttribute
{
    #########################################################
    ############### REFACTORED CODE         ##################
    #########################################################
    public function __contruct($connName = false)
    {
        if (! $connName) {
            $connName = "doctrine_site" ;
        }
        Doctrine_Manager::getInstance()->bindComponent($connName, $connName);
    }

    public static function getPageAttributeIdByName($attributeName)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p.id')
            ->from('KC\Entity\pageAttribute', 'p')
            ->where('p.name="'.$attributeName.'"');
        $pageAttribute = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return isset($pageAttribute['id']) ? (int) $pageAttribute['id'] : 0;
    }

    public static function insertPageAttributes()
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $pageAttributeObject  = new \KC\Entity\PageAttribute();
        for ($i = 0; $i<3; $i++) {
            if ($i == 0) {
                $name = "contact";
            } else if ($i == 1) {
                $name = "faq";
            } else {
                $name = "default";
            }
            $pageAttributeObject->name = $name;
            $entityManagerLocale->persist($pageAttributeObject);
            $entityManagerLocale->flush();
        }
        return true;
    }

    public static function deletePageAttributes()
    {
        $databaseConnection = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS=0');
        $databaseConnection->query('TRUNCATE TABLE page_attribute');
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS=1');
        unset($databaseConnection);
        return true;
    }
    #########################################################
    ############# END REFACTORED CODE     ###################
    #########################################################
    public function getPageAttributes()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p')
            ->from('KC\Entity\pageAttribute', 'p');
        $attrList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return  $attrList;
    }

    public static function getPageIdByName($name)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p.id')
            ->from('KC\Entity\pageAttribute', 'p')
            ->where('p.name="'.$name.'"');
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return  $data['id'];
    }
}