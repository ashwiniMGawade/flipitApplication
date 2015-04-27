<?php
namespace KC\Repository;
class WidgetLocation Extends \KC\Entity\WidgetLocation
{
    public static function saveOrUpdateWidgetLocation($parameters)
    {
        $pageType = \FrontEnd_Helper_viewHelper::sanitize($parameters['pageType']);
        $pageTypeGloabalOrIndividual = !empty($pageType) ? $pageType : 'global';
        $widgetLocation = \FrontEnd_Helper_viewHelper::sanitize($parameters['widgetLocation']);
        $relatedId = \FrontEnd_Helper_viewHelper::sanitize($parameters['relatedId']);
        $widgetLocationId = self::validateWidgetLocation(
            $pageTypeGloabalOrIndividual,
            $widgetLocation,
            $relatedId
        );
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        if (!empty($widgetLocationId)) {
            $saveWidgetLocation = self::findWidgetById($widgetLocationId);
        } else {
            $saveWidgetLocation = new \KC\Entity\WidgetLocation();
            $saveWidgetLocation->created_at = new \DateTime('now');
            $saveWidgetLocation->deleted = false;
            $saveWidgetLocation->widgettype = 'signup';
        }
        $saveWidgetLocation->position = \FrontEnd_Helper_viewHelper::sanitize($parameters['widgetPostion']);
        $saveWidgetLocation->location = $widgetLocation;
        $saveWidgetLocation->relatedid = $relatedId;
        $saveWidgetLocation->pagetype = $pageTypeGloabalOrIndividual;
        $saveWidgetLocation->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($saveWidgetLocation);
        $entityManagerLocale->flush();
    }

    public static function findWidgetById($widgetLocationId)
    {
        return $entityManagerLocale->find('KC\Entity\WidgetLocation', $widgetLocationId);
    }

    public static function validateWidgetLocation($pageType, $widgetLocation, $relatedId)
    {
        $existInDatabase = '';
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        if (!empty($relatedId)) {
            $existInDatabase = self::getWidgetLocationIdByRelatedId($relatedId);
        } else {
            $existInDatabase = self::getWidgetLocationIdByPageTypeAndLocation($widgetLocation, $pageType);
        }
        $widgetLocationId = !empty($existInDatabase[0]['id']) ? $existInDatabase[0]['id'] : '';
        return $widgetLocationId;
    }

    public static function getWidgetLocationIdByRelatedId($relatedId)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder
            ->select('wl.id, wl.position')
            ->from('\KC\Entity\WidgetLocation', 'wl')
            ->where($queryBuilder->expr()->eq('wl.relatedid', $relatedId))
            ->setMaxResults(1);
        $existInDatabase = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $existInDatabase;
    }

    public static function getWidgetLocationIdByPageTypeAndLocation($widgetLocation, $pageType)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder
            ->select('wl.id, wl.position')
            ->from('\KC\Entity\WidgetLocation', 'wl')
            ->where($queryBuilder->expr()->eq('wl.location', $queryBuilder->expr()->literal($widgetLocation)))
            ->andWhere($queryBuilder->expr()->eq('wl.pagetype', $queryBuilder->expr()->literal($pageType)))
            ->setMaxResults(1);
        $existInDatabase = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $existInDatabase;
    }

    public static function getWidgetPosition($pageType, $widgetLocation, $relatedId)
    {
        $existInDatabase = '';
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        if (!empty($relatedId)) {
            $existInDatabase = self::getWidgetLocationIdByRelatedId($relatedId);
        }
        if (empty($existInDatabase)) {
            $existInDatabase = self::getWidgetLocationIdByPageTypeAndLocation($widgetLocation, $pageType);
        }
        $widgetPosition = !empty($existInDatabase[0]['position']) ? $existInDatabase[0]['position'] : '';
        return $widgetPosition;
    }
}
