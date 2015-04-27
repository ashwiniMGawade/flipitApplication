<?php
namespace KC\Repository;
class WidgetLocation Extends \KC\Entity\WidgetLocation
{
    public static function saveOrUpdateWidgetLocation($parameters)
    {
        $pageType = \FrontEnd_Helper_viewHelper::sanitize($parameters['pageType']);
        $pageTypeGloabalOrInvidual = !empty($pageType) ? $pageType : 'global';
        $widgetLocation  = \FrontEnd_Helper_viewHelper::sanitize($parameters['widgetLocation']);
        $relatedId  = \FrontEnd_Helper_viewHelper::sanitize($parameters['relatedId']);
        $widgetLocationId = self::validateWidgetLocation(
            $pageTypeGloabalOrInvidual,
            $widgetLocation,
            $relatedId
        );
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        if (!empty($widgetLocationId)) {
            $saveWidgetLocation = $entityManagerLocale->find('KC\Entity\WidgetLocation', $widgetLocationId);
        } else {
            $saveWidgetLocation = new \KC\Entity\WidgetLocation();
            $saveWidgetLocation->created_at = new \DateTime('now');
            $saveWidgetLocation->deleted = false;
        }
        $saveWidgetLocation->position = \FrontEnd_Helper_viewHelper::sanitize($parameters['widgetPostion']);
        $saveWidgetLocation->location = $widgetLocation;
        $saveWidgetLocation->relatedid = $relatedId;
        $saveWidgetLocation->pagetype = $pageTypeGloabalOrInvidual;
        $saveWidgetLocation->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($saveWidgetLocation);
        $entityManagerLocale->flush();
    }

    public static function validateWidgetLocation($pageType, $widgetLocation, $relatedId)
    {
        $existedRecord = '';
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        if (!empty($relatedId)) {
            $existedRecord = self::getWidgetLocationIdByRelatedId($relatedId);
        } else {
            $existedRecord = self::getWidgetLocationIdByPageTypeAndLocation($widgetLocation, $pageType);
        }
        $widgetLocationId = !empty($existedRecord[0]['id']) ? $existedRecord[0]['id'] : '';
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
        $existedRecord = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $existedRecord;
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
        $existedRecord = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $existedRecord;
    }

    public static function getWidgetPosition($pageType, $widgetLocation, $relatedId)
    {
        $existedRecord = '';
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        if (!empty($relatedId)) {
            $existedRecord = self::getWidgetLocationIdByRelatedId($relatedId);
        }
        if (empty($existedRecord)) {
            $existedRecord = self::getWidgetLocationIdByPageTypeAndLocation($widgetLocation, $pageType);
        }
        $widgetPosition = !empty($existedRecord[0]['position']) ? $existedRecord[0]['position'] : '';
        return $widgetPosition;
    }
}
