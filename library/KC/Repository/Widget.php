<?php
namespace KC\Repository;

class Widget extends \KC\Entity\Widget
{
    public function addWidget($params)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $widget = new \KC\Entity\Widget();
        $widget->title = \FrontEnd_Helper_viewHelper::sanitize(
            \BackEnd_Helper_viewHelper::stripSlashesFromString($params ['title'])
        );
        $widget->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params ['content']);
        $widget->status = 1;
        $widget->deleted = 0;
        $widget->created_at = new \DateTime('now');
        $widget->updated_at = new \DateTime('now');
        $widget->showWithDefault = 1;
        $entityManagerLocale->persist($widget);
        $entityManagerLocale->flush();
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
        return $widget->id;
    }
    
    public static function getDefaultwidgetList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('w')
            ->from('KC\Entity\Widget', 'w')
            ->where('w.userDefined = 0')
            ->andWhere('w.deleted = 0')
            ->andWhere('w.status = 1');
        $widgetsList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $widgetsList;
    }

    public static function getUserDefinedWidgetList($widgetsIds = array())
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('w')
            ->from('KC\Entity\Widget', 'w')
            ->andWhere('w.deleted = 0')
            ->andWhere('w.status = 1');
        if (!empty($widgetsIds)) {
            $ids = implode(',', $widgetsIds);
            $query =  $query->andWhere($queryBuilder->expr()->notIn('w.id', $ids));
        }
        $query = $query->orderBy('w.title');
        $widgetsList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $widgetsList;
    }

    public static function changeStatus($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        if ($params['state'] == 'online') {
            $query = $queryBuilder->update('KC\Entity\Widget', 'widget')
            ->set('widget.status', 1)
            ->where('widget.id = '.$params['id'])
            ->getQuery();
            $query->execute();
            return $params['id'];
        }
        if ($params['state'] == 'offline') {
            $query = $queryBuilder->update('KC\Entity\Widget', 'widget')
            ->set('widget.status', 0)
            ->where('widget.id = '.$params['id'])
            ->getQuery();
            $query->execute();
            return $params['id'];
        } else {
            $id = null;
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
    }

    public static function getWidgetList($params)
    {
        $srh = @$params["searchText"] != 'undefined' ? @$params["searchText"] : '';
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->from('KC\Entity\Widget', 'widget')
            ->where('widget.deleted = 0')
            ->andWhere(
                $queryBuilder->expr()->like('widget.title', $queryBuilder->expr()->literal($srh.'%'))
            );
        $request = \DataTable_Helper::createSearchRequest($params, array());
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($query)
            ->add('number', 'widget.id')
            ->add('text', 'widget.title')
            ->add('text', 'widget.content')
            ->add('text', 'widget.status')
            ->add('text', 'widget.showWithDefault');
        $list = $builder->getTable()->getResponseArray();
        return $list;
    }

    public static function searchKeyword($keyword)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('w')
            ->from('KC\Entity\Widget', 'w')
            ->andWhere(
                $queryBuilder->expr()->like('w.title', $queryBuilder->expr()->literal($keyword.'%'))
            )
            ->andWhere('w.status = 0')
            ->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getWidgetInformation($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('w')
            ->from('KC\Entity\Widget', 'w')
            ->setParameter(1, $id)
            ->where('w.id = ?1');
        $widgetInformation = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
        return $widgetInformation;

    }

    public function updateWidget($parameters)
    {
        $content = addslashes($parameters['content']);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Widget', 'w')
            ->set(
                'w.title',
                $queryBuilder->expr()->literal(\BackEnd_Helper_viewHelper::stripSlashesFromString($parameters['title']))
            )
            ->set(
                'w.content',
                $queryBuilder->expr()->literal(\BackEnd_Helper_viewHelper::stripSlashesFromString($content))
            )
            ->setParameter(1, $parameters['id'])
            ->where('w.id = ?1')
            ->getQuery();
        $query->execute();
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
        return true;
    }

    public function permanentDeleteWidget($id)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\Widget', 'w')
            ->setParameter(1, $id)
            ->where('w.id = ?1')
            ->getQuery();
            $query->execute();
        } else {
            $id = null;
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
        return $id;
    }

    public static function getAllUrls()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('p.permalink')
            ->from('KC\Entity\Page', 'p')
            ->andWhere('p.deleted = 0');
        $pages = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $pageUrls = array();
        if (!empty($pages)) {
            foreach ($pages as $page) {
                if (!empty($page['permalink'])) {
                    $pageUrls[] = $page['permalink'];
                }
            }
        }
        return $pageUrls ;
    }
}
