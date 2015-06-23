<?php
namespace KC\Repository;

class PageWidgets extends \Core\Domain\Entity\PageWidgets
{
    public static function getWidgetsByType($widgetsType)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('sw, w')
            ->from('KC\Entity\pageWidgets', 'sw')
            ->leftJoin('sw.widget', 'w')
            ->where($queryBuilder->expr()->eq('sw.widget_type', $queryBuilder->expr()->literal($widgetsType)))
            ->andWhere('w.status= 1')
            ->orderBy('sw.position', 'ASC');
        $pageWidets = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageWidets;
    }

    public static function addWidgetInList($widgetId, $widgetsType, $type = '')
    {
        $widget = self::widgetExistance($widgetId);
        $errorStatus = '0';
        if (sizeof($widget) > 0) {
            $pageWidgets = self::getPageWidget($widgetId, $widgetsType);
            if (!empty($pageWidgets)) {
                $errorStatus = '2';
            } else {
                $errorStatus = '1';
                $pageWidgetMaxPosition = self::getPageWidgetMaxPosition($widgetsType);
                if (!empty($pageWidgetMaxPosition)) {
                    $newPosition = $pageWidgetMaxPosition[0]['position'];
                } else {
                    $newPosition =  0;
                }
                $pageWidgetId = self::savePageWidget($widgetId, $widgetsType, $newPosition);
                $errorStatus  = array(
                    'id'=>$pageWidgetId,
                    'type'=>'MN',
                    'widgetId'=>$widgetId,
                    'position'=>(intval($newPosition) + 1),
                    'title'=>$widget['title']
                );
            }
        }
        return $errorStatus;
    }

    public static function widgetExistance($widgetId)
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
        $pageWidgets = array();
        if (!empty($widgetId) && !empty($widgetsType)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('sw')
                ->from('KC\Entity\pageWidgets', 'sw')
                ->where('sw.widget=' . $widgetId)
                ->andWhere('sw.widget_type=' . $queryBuilder->expr()->literal($widgetsType));
            $pageWidgets = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $pageWidgets;
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
        $pageWidgets->widget_type = \FrontEnd_Helper_viewHelper::sanitize($widgetsType);
        $pageWidgets->widget = $entityManagerLocale->find(
            'KC\Entity\Widget',
            \FrontEnd_Helper_viewHelper::sanitize($widgetId)
        );
        $pageWidgets->position = \FrontEnd_Helper_viewHelper::sanitize((intval($newPosition) + 1));
        $pageWidgets->deleted = 0;
        $pageWidgets->created_at = new \DateTime('now');
        $pageWidgets->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($pageWidgets);
        $entityManagerLocale->flush();
        return $pageWidgets->id;
    }

    public static function deletePageWidgets($id)
    {
        if (!empty($id)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->delete('KC\Entity\pageWidgets', 'spl')
                ->where('spl.id ='.$id)
                ->getQuery();
            $query->execute();
        }
        return true;
    }

    public static function getNewPageWidgetsList($widgetsType)
    {
        $newWidgetsList = array();
        if (!empty($widgetsType)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('spo')
                ->from('KC\Entity\pageWidgets', 'spo')
                ->where('spo.widget_type='. $queryBuilder->expr()->literal($widgetsType))
                ->orderBy('spo.position', 'ASC');
            $newWidgetsList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $newWidgetsList;
    }

    public static function updateWithNewPosition($newPosition, $newWidget)
    {
        if (isset($newWidget['id'])) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->update('KC\Entity\pageWidgets', 'p')
                ->set('p.position', \FrontEnd_Helper_viewHelper::sanitize($newPosition))
                ->where('p.id = '.$newWidget['id'])
                ->getQuery();
            $query->execute();
        }
        return true;
    }

    public static function deleteWidget($pageWidgetId, $position, $widgetsType, $type = '')
    {
        if ($pageWidgetId) {
            self::deletePageWidgets($pageWidgetId);
            $newPageWidgetsList = self::getNewPageWidgetsList($widgetsType);
            $newPosition = 1;
            foreach ($newPageWidgetsList as $newWidget) {
                self::updateWithNewPosition($newPosition, $newWidget);
                $newPosition++;
            }
            return true;
        }
        return false;
    }

    public static function savePosition($widgetIds, $widgetType)
    {
        if (!empty($widgetIds)) {
            self::deletePageWidgetsByWidgetType($widgetType);
            $widgetIds = explode(',', $widgetIds);
            $widgetPosition = 0;
            foreach ($widgetIds as $widgetId) {
                self::savePageWidget($widgetId, $widgetType, $widgetPosition);
                $widgetPosition++;
            }
        }
    }

    public static function deletePageWidgetsByWidgetType($widgetType)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->delete('KC\Entity\PageWidgets', 'spl')
            ->where('spl.widget_type ='.$queryBuilder->expr()->literal($widgetType))
            ->getQuery();
        $query->execute();
        return true;
    }
}
