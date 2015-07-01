<?php
namespace KC\Repository;
class ShopExcelInformation extends \Core\Domain\Entity\ShopExcelInformation
{
$entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $entityManagerUser->select('about')
            ->from('\Core\Domain\Entity\About', 'about')
            ->setParameter(1, $aboutStatus)
            ->where($entityManagerUser->expr()->in('about.status', '?1'))
            ->setParameter(2, $aboutPageContentIds)
            ->andWhere($entityManagerUser->expr()->in('about.id', '?2'));
            $aboutContent = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $aboutContent;
}