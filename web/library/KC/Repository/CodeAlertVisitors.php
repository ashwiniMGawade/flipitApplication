<?php
namespace KC\Repository;
class CodeAlertVisitors extends \Core\Domain\Entity\CodeAlertVisitors
{
    public static function getVisitorsToRemoveInCodeAlert($visitorId, $offerId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('c.visitorId')
            ->from('\Core\Domain\Entity\CodeAlertVisitors', 'c')
            ->where('c.offerId = '.$offerId)
            ->andWhere('c.visitorId = '.$visitorId);
        $visitors = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $visitors;
    }

    public static function saveCodeAlertVisitors($visitorIds, $offerId)
    {
        if (isset($visitorIds) && $visitorIds != '') {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('c')
                ->from("\Core\Domain\Entity\CodeAlertVisitors", 'c')
                ->where('c.offerId = '.$offerId);
            $codeAlertInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if (empty($codeAlertInformation)) {
                $visitorIds = explode(',', $visitorIds);
                foreach ($visitorIds as $visitorValue) {
                    $entityManagerLocale  = \Zend_Registry::get('emLocale');
                    $codeAlertQueue = new \Core\Domain\Entity\CodeAlertVisitors();
                    $codeAlertQueue->offerId = $offerId;
                    $codeAlertQueue->visitorId = $visitorValue;
                    $entityManagerLocale->persist($codeAlertQueue);
                    $entityManagerLocale->flush();
                }
            }
        }
        return true;
    }
}