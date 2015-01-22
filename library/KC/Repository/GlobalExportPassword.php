<?php
namespace KC\Repository;
class GlobalExportPassword Extends \KC\Entity\GlobalExportPassword
{
    public static function savePasswordForExportDownloads($type)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('g.id')
            ->from('KC\Entity\GlobalExportPassword', 'g')
            ->where('g.exportType =  "'.$type.'"');
        $globalExportInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if (!empty($globalExportInformation)) {
            $queryBuilderUpdate = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $queryBuilderUpdate->update('KC\Entity\GlobalExportPassword', 'gep')
            ->set('gep.password', mt_rand())
            ->where('gep.id ='.$globalExportInformation[0]['id'])
            ->getQuery()->execute();
        } else {
            $entityManagerLocale  = \Zend_Registry::get('emLocale');
            $globalExportPassword = new \KC\Entity\GlobalExportPassword();
            $globalExportPassword->password = mt_rand();
            $globalExportPassword->exportType = $type;
            $entityManagerLocale->persist($globalExportPassword);
            $entityManagerLocale->flush();
        }
        return true;
    }

    public static function getPasswordForExportDownloads($type)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('g.password')
            ->from('KC\Entity\GlobalExportPassword', 'g')
            ->where('g.exportType =  "'.$type.'"');
        $globalExportInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $globalExportPassword = '';
        if (!empty($globalExportInformation)) {
            $globalExportPassword = $globalExportInformation[0]['password'];
        }

        return $globalExportPassword;
    }

    public function checkPasswordForExport($password, $type)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('g.password')
            ->from('KC\Entity\GlobalExportPassword', 'g')
            ->where('g.exportType =  "'.$type.'"')
            ->andWhere('g.password = "'.$password.'"');
        $globalExportInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if (!empty($globalExportInformation)) {
            return true;
        } else {
            return false;
        }
    }

}