<?php
namespace KC\Repository;

class GlobalExportPassword extends \Core\Domain\Entity\User\GlobalExportPassword
{
    public static function savePasswordForExportDownloads($type)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder
            ->select('g.id')
            ->from('KC\Entity\User\GlobalExportPassword', 'g')
            ->where($queryBuilder->expr()->eq('g.exportType', $queryBuilder->expr()->literal($type)));
        $globalExportInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if (!empty($globalExportInformation)) {
            $queryBuilderUpdate = \Zend_Registry::get('emUser')->createQueryBuilder();
            $queryBuilderUpdate->update('KC\Entity\User\GlobalExportPassword', 'gep')
            ->set('gep.password', mt_rand())
            ->where('gep.id ='.$globalExportInformation[0]['id'])
            ->getQuery()->execute();
        } else {
            $entityManagerLocale  = \Zend_Registry::get('emUser');
            $globalExportPassword = new \KC\Entity\User\GlobalExportPassword();
            $globalExportPassword->password = mt_rand();
            $globalExportPassword->exportType = $type;
            $globalExportPassword->created_at = new \DateTime('now');
            $globalExportPassword->updated_at = new \DateTime('now');
            $entityManagerLocale->persist($globalExportPassword);
            $entityManagerLocale->flush();
        }
        return true;
    }

    public static function getPasswordForExportDownloads($type)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder
            ->select('g.password')
            ->from('KC\Entity\User\GlobalExportPassword', 'g')
            ->where($queryBuilder->expr()->eq('g.exportType', $queryBuilder->expr()->literal($type)));
        $globalExportInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $globalExportPassword = '';
        if (!empty($globalExportInformation)) {
            $globalExportPassword = $globalExportInformation[0]['password'];
        }

        return $globalExportPassword;
    }

    public function checkPasswordForExport($password, $type)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder
            ->select('g.password')
            ->from('KC\Entity\User\GlobalExportPassword', 'g')
            ->where($queryBuilder->expr()->eq('g.exportType', $queryBuilder->expr()->literal($type)))
            ->andWhere($queryBuilder->expr()->eq('g.password', $queryBuilder->expr()->literal($password)));
        $globalExportInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if (!empty($globalExportInformation)) {
            return true;
        } else {
            return false;
        }
    }
}
