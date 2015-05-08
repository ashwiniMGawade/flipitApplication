<?php
namespace KC\Repository;

class Widget extends \KC\Entity\Widget
{
    public function addWidget($params)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $w = new \KC\Entity\Widget();
        $w->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params ['title']);
        $w->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params ['content']);
        $w->status = 1;
        $w->deleted = 0;
        $w->created_at = new \DateTime('now');
        $w->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($w);
        $entityManagerLocale->flush();
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
        return $w->id;
    }
    
    public static function getDefaultwidgetList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('w')
            ->from('KC\Entity\Widget', 'w')
            ->setParameter(1, '0')
            ->where('w.userDefined = ?1')
            ->setParameter(2, '0')
            ->andWhere('w.deleted = ?2')
            ->setParameter(3, '1')
            ->andWhere('w.status = ?3');
        $widgetsList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $widgetsList;
    }

    public static function getUserDefinedwidgetList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('w')
            ->from('KC\Entity\Widget', 'w')
            ->setParameter(1, '0')
            ->andWhere('w.deleted = ?1')
            ->setParameter(2, '1')
            ->andWhere('w.status = ?2')
            ->orderBy('w.title');
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
            ->add('text', 'widget.status');
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

    public static function updateWidget($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('w')
            ->from('KC\Entity\Widget', 'w')
            ->setParameter(1, $id)
            ->where('w.id = ?1');
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
        return $data;

    }

    public function editWidgetRecord($params)
    {
        $content = @addslashes($params['content']);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Widget', 'w')
        ->set('w.title', $queryBuilder->expr()->literal(\BackEnd_Helper_viewHelper::stripSlashesFromString($params['title'])))
        ->set('w.content', $queryBuilder->expr()->literal(\BackEnd_Helper_viewHelper::stripSlashesFromString($content)))
        ->setParameter(1, $params['id'])
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

    public static function getAllUrls($id)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('w, p, wp')
            ->from('KC\Entity\Widget', 'w')
            ->leftJoin('w.Widget', 'p')
            ->leftJoin('p.widget', 'wp')
            ->setParameter(1, $id)
            ->where('w.id = ?1')
            ->setParameter(2, 0)
            ->andWhere('wp.deleted = ?2');
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $urlsArray = array();
        # check this widget has one or more related pages
        if (isset($data['Widget']) && count($data['Widget']) > 0) {
            # traverse through all shops
            foreach ($data['Widget'] as $value) {
                # check if a category has permalink then add it into array
                if (isset($value['widget']['permaLink']) && strlen($value['widget']['permaLink']) > 0) {
                    $urlsArray[] = $value['widget']['permaLink'];
                }
            }
        }
        return $urlsArray ;
    }
}
