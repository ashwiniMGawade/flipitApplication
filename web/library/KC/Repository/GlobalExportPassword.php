<?php
namespace KC\Repository;

class GlobalExportPassword extends \Core\Domain\Entity\User\GlobalExportPassword
{
    public static function savePasswordForExportDownloads($type, $password)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder
            ->select('g.id')
            ->from('\Core\Domain\Entity\User\GlobalExportPassword', 'g')
            ->where($queryBuilder->expr()->eq('g.exportType', $queryBuilder->expr()->literal($type)));
        $globalExportInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if (!empty($globalExportInformation)) {
            $queryBuilderUpdate = \Zend_Registry::get('emUser')->createQueryBuilder();
            $queryBuilderUpdate->update('\Core\Domain\Entity\User\GlobalExportPassword', 'gep')
            ->set('gep.password', $queryBuilderUpdate->expr()->literal($password))
            ->where('gep.id ='.$globalExportInformation[0]['id'])
            ->getQuery()->execute();
        } else {
            $entityManagerLocale  = \Zend_Registry::get('emUser');
            $globalExportPassword = new \Core\Domain\Entity\User\GlobalExportPassword();
            $globalExportPassword->password = $password;
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
            ->from('\Core\Domain\Entity\User\GlobalExportPassword', 'g')
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
            ->from('\Core\Domain\Entity\User\GlobalExportPassword', 'g')
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
