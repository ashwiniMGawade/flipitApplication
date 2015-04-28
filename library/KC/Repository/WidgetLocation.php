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
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        return $entityManagerLocale->find('KC\Entity\WidgetLocation', $widgetLocationId);
    }

    public static function validateWidgetLocation($pageType, $widgetLocation, $relatedId)
    {
        $existInDatabase = '';
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        if (!empty($relatedId)) {
            $existInDatabase = self::getWidgetLocationIdByRelatedId($relatedId);
            self::clearCacheByPageTypeOrRelatedId($relatedId);
        } else {
            $existInDatabase = self::getWidgetLocationIdByPageTypeAndLocation($widgetLocation, $pageType);
            self::clearCacheByPageTypeOrRelatedId($pageType);
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

    public static function getWidgetPosition($pageType, $widgetLocation, $relatedId, $moneyShop = '')
    {
        $existInDatabase = '';
        if (!empty($relatedId)) {
            $cacheKey = 'widget_'. $relatedId . '_position';
            $existInDatabase = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)$cacheKey,
                array(
                    'function' => 'KC\Repository\WidgetLocation::getWidgetLocationIdByRelatedId',
                    'parameters' => array($relatedId)
                ),
                ''
            );
        }
        if (empty($existInDatabase)) {
            if (!empty($moneyShop)) {
                $existInDatabase = self::getWidgetLocationIdByPageTypeAndLocationByCache($moneyShop, $pageType);
            }
            if (empty($existInDatabase)) {
                $existInDatabase = self::getWidgetLocationIdByPageTypeAndLocationByCache($widgetLocation, $pageType);
            }
        }
        $widgetPosition = !empty($existInDatabase[0]['position']) ? $existInDatabase[0]['position'] : '';
        return $widgetPosition;
    }

    public static function getWidgetLocationIdByPageTypeAndLocationByCache($widgetLocation, $pageType)
    {
        $cacheKey = 'widget_'. $pageType . '_position';
        $existInDatabase = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$cacheKey,
            array(
                'function' => 'KC\Repository\WidgetLocation::getWidgetLocationIdByPageTypeAndLocation',
                'parameters' => array($widgetLocation, $pageType)
            ),
            ''
        );
        return $existInDatabase;
    }

    public static function clearCacheByPageTypeOrRelatedId($pageTypeOrId)
    {
        $cacheKey = 'widget_'. $pageTypeOrId . '_position';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($cacheKey);
    }

    public static function getWidgetPositionForFrontEnd(
        $pageType,
        $widgetLocation,
        $relatedId,
        $moneyShop = '',
        $offers = ''
    ) {
        $widgetPosition = self::getWidgetPosition($pageType, $widgetLocation, $relatedId, $moneyShop);
        $widgetOrDefaultPosition = !empty($widgetPosition) ? $widgetPosition : 1;
        $maxWidgetPosition =  $widgetOrDefaultPosition > count($offers) ? count($offers) : $widgetOrDefaultPosition;
        return $maxWidgetPosition;
    }
}
