<?php
namespace KC\Repository;

class Website extends \KC\Entity\Website
{
    public static function getAllWebsites()
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('w.id, w.name, w.status')
            ->from('KC\Entity\Website', 'w')
            ->setParameter(1, '0')
            ->where('w.deleted = ?1');
        $websites = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return \BackEnd_Helper_viewHelper::msort($websites, "name", array("kortingscode.nl"));
    }

    public static function getWebsiteDetails($websiteId = null, $websiteName = null)
    {
        $websiteId =  \FrontEnd_Helper_viewHelper::sanitize($websiteId);
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('w.id, w.name, w.url, w.chain')
            ->from('KC\Entity\Website', 'w')
            ->setParameter(1, '0')
            ->where('w.deleted = ?1');

        if ($websiteName) {
            $query->setParameter(2, $websiteName)
            ->andWhere("w.name = ?2");
        } else {
            $query->setParameter(3, $websiteId)
            ->andWhere("w.id = ?3");
        }
        return $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public static function setLocaleStatus($localeStatus, $websiteName)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Website', 'w')
                ->set('w.status', $queryBuilder->expr()->literal($localeStatus))
                ->where($queryBuilder->expr()->eq('w.name', $queryBuilder->expr()->literal($websiteName)))
                ->getQuery();
        $query->execute();
        return true;
    }

    public static function getLocaleStatus($websiteName)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('w.status')
            ->from('KC\Entity\Website', 'w')
            ->setParameter(1, $websiteName)
            ->where('w.name = ?1');
        $localeStatus = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $localeStatus;
    }

    public static function saveChain($chain, $websiteName)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Website', 'w')
                ->set('w.chain', $queryBuilder->expr()->literal($chain))
                ->where($queryBuilder->expr()->eq('w.name', $queryBuilder->expr()->literal($websiteName)))
                ->getQuery();
        $query->execute();
        return true;
    }
}
