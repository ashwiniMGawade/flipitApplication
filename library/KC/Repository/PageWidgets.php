<?php
namespace KC\Repository;

class PageWidgets extends \KC\Entity\PageWidgets
{
    public static function getWidgetsByType($widgetsType)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('sw, w')
            ->from('KC\Entity\pageWidgets', 'sw')
            ->leftJoin('sw.widget', 'w')
            ->where($queryBuilder->expr()->eq('sw.widget_type', $queryBuilder->expr()->literal($widgetsType)))
            ->orderBy('sw.position', 'ASC');
        $pageWidets = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageWidets;
    }

    public static function addWidgetInList($widgetId, $widgetsType, $type = '')
    {
        $widget = self::offerExistance($widgetId);
        $result = '0';
        if (sizeof($widget) > 0) {
            $pageWidgets = self::getPageWidget($widgetId, $widgetsType);
            if (!empty($pageWidgets)) {
                $result = '2';
            } else {
                $result = '1';
                $pageWidgetMaxPosition = self::getPageWidgetMaxPosition($widgetsType);
                if (!empty($pageWidgetMaxPosition)) {
                    $newPosition = $pageWidgetMaxPosition[0]['position'];
                } else {
                    $newPosition =  0;
                }
                $specialPageOfferId = self::savePageWidget($widgetId, $widgetsType, $newPosition);
                $result  = array(
                    'id'=>$specialPageOfferId,
                    'type'=>'MN',
                    'widgetId'=>$widgetId,
                    'position'=>(intval($newPosition) + 1),
                    'title'=>$widget['title']
                );
            }
        }
        return $result;
    }

    public static function offerExistance($widgetId)
    {
        $widget = array();
        if (!empty($widgetId)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('w')
                ->from('KC\Entity\Widget', 'w')
                ->where('w.id=' . $widgetId)
                ->setMaxResults(1);
            $widget = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $widget;
    }

    public static function getPageWidget($widgetId, $widgetsType)
    {
        $specialPageOffers = array();
        if (!empty($offerId) && !empty($pageId)) {
            $specialPageOffersQueryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $specialPageOffersQueryBuilder
                ->select('sw')
                ->from('KC\Entity\pageWidgets', 'sw')
                ->where('sw.widget=' . $widgetId)
                ->andWhere('sw.widget_type=' . $queryBuilder->expr()->literal($widgetsType));
            $specialPageOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $specialPageOffers;
    }

    public static function getPageWidgetMaxPosition($widgetsType)
    {
        if (!empty($widgetsType)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('p.position')
                ->from('KC\Entity\pageWidgets', 'p')
                ->where('p.widget_type =' .$queryBuilder->expr()->literal($widgetsType))
                ->orderBy('p.position', 'DESC')
                ->setMaxResults(1);
            $maxPosition = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $maxPosition;
    }

    public static function savePageWidget($widgetId, $widgetsType, $newPosition)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $pageWidgets = new \KC\Entity\pageWidgets();
        $pageWidgets->widget_type = $widgetsType;
        $pageWidgets->widget = $entityManagerLocale->find('KC\Entity\Widget', $widgetId);
        $pageWidgets->position = (intval($newPosition) + 1);
        $pageWidgets->deleted = 0;
        $pageWidgets->created_at = new \DateTime('now');
        $pageWidgets->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($pageWidgets);
        $entityManagerLocale->flush();
        return $pageWidgets->id;
    }

    public static function deleteSpecialPageOffer($id)
    {
        if (!empty($id)) {
            $queryBuilderDelete = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderDelete
                ->delete('KC\Entity\pageWidgets', 'spl')
                ->where('spl.id ='.$id)
                ->getQuery();
            $query->execute();
        }
        return true;
    }

    public static function getNewOfferList($widgetsType)
    {
        $newOffersList = array();
        if (!empty($pageId)) {
            $queryBuilderSelect = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderSelect
                ->select('spo')
                ->from('KC\Entity\pageWidgets', 'spo')
                ->where('spo.widget_type='. $queryBuilder->expr()->literal($widgetsType))
                ->orderBy('spo.position', 'ASC');
            $newOffersList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $newOffersList;
    }

    public static function updateWithNewPosition($newPosition, $newOffer)
    {
        if (isset($newOffer['id'])) {
            $queryBuilderSpecialPage = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderSpecialPage
                ->update('KC\Entity\pageWidgets', 'p')
                ->set('p.position', $newPosition)
                ->where('p.id = '.$newOffer['id'])
                ->getQuery();
            $query->execute();
        }
        return true;
    }

    public static function deleteCode($id, $position, $pageId, $type = '')
    {
        if ($id) {
            self::deleteSpecialPageOffer($id);
            $newOffersList = self::getNewOfferList($pageId);
            $newPosition = 1;
            $queryBuilderSpecialPage = \Zend_Registry::get('emLocale')->createQueryBuilder();
            foreach ($newOffersList as $newOffer) {
                self::updateWithNewPosition($newPosition, $newOffer);
                $newPosition++;
            }
            return true;
        }
        return false;
    }

    public static function savePosition($widgetIds, $widgetType)
    {
        if (!empty($widgetIds)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->delete('KC\Entity\PageWidgets', 'spl')
                ->where('spl.widget_type ='.$queryBuilder->expr()->literal($widgetType))
                ->getQuery();
            $query->execute();
            $widgetIds = explode(',', $widgetIds);
            $i = 1;
            foreach ($widgetIds as $widgetId) {
                $entityManagerLocale  = \Zend_Registry::get('emLocale');
                $pageWidget = new \KC\Entity\PageWidgets();
                $pageWidget->widget_type = $widgetType;
                $pageWidget->widget = $entityManagerLocale->find('KC\Entity\Widget', $widgetId);
                $pageWidget->position = $i;
                $pageWidget->deleted = 0;
                $pageWidget->created_at = new \DateTime('now');
                $pageWidget->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($pageWidget);
                $entityManagerLocale->flush();
                $i++;
            }
        }
    }

    public static function addNewSpecialPageOffers()
    {
        $currentDate = date("Y-m-d H:i");
        $specialListPages = \KC\Repository\SpecialList::getSpecialPages();
        if (!empty($specialListPages)) {
            foreach ($specialListPages as $specialListPage) {
                $pageRelatedOffers = \KC\Repository\Offer::getSpecialOffersByPage($specialListPage[0]['page']['id'], $currentDate);
                $constraintsRelatedOffers = \KC\Repository\Offer::getOffersByPageConstraints($specialListPage[0]['page'], $currentDate);
                $pageRelatedOffersAndPageConstraintsOffers = array_merge($pageRelatedOffers, $constraintsRelatedOffers);
                \KC\Repository\SpecialList::updateTotalOffersAndTotalCoupons(
                    count($pageRelatedOffersAndPageConstraintsOffers),
                    0,
                    $specialListPage[0]['page']['id']
                );
                foreach ($pageRelatedOffersAndPageConstraintsOffers as $pageRelatedOffersAndPageConstraintsOffer) {
                    self::addOfferInList(
                        $pageRelatedOffersAndPageConstraintsOffer['id'],
                        $specialListPage[0]['page']['id'],
                        'cron'
                    );
                }
            }
        }
        return true;
    }
}
