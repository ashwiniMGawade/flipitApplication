<?php
namespace KC\Repository;

class Widget extends \KC\Entity\Widget
{
    public function addWidget($params)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $w = new Widget();
        $w->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params ['title']);
        $w->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params ['content']);
        $entityManagerLocale->persist($w);
        $entityManagerLocale->flush();
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
        return $w->id;
    }
    
    public static function getDefaultwidgetList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('w')
            ->from('KC\Entity\Widget', 'w')
            ->setParameter(1, '0')
            ->where('w.userdefined = ?1')
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
            ->setParameter(1, $params['id'])
            ->where('widget.id = ?1')
            ->getQuery();
            $query->execute();
            return $params['id'];
        }
        if ($params['state'] == 'offline') {
            $query = $queryBuilder->update('KC\Entity\Widget', 'widget')
            ->set('widget.status', 0)
            ->setParameter(1, $params['id'])
            ->where('widget.id = ?1')
            ->getQuery();
            $query->execute();
            return $params['id'];
        } else {
            $id = null;
        }
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
    }

    public static function getWidgetList($params)
    {
        $srh = @$params["SearchText"] != 'undefined' ? @$params["SearchText"] : '';
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('widget')
            ->from('KC\Entity\Widget', 'widget')
            ->setParameter(1, 0)
            ->where('widget.deleted = ?1')
            ->setParameter(2, $srh.'%')
            ->andWhere($entityManagerUser->expr()->like('widget.title', '?2'))
            ->orderBy("widget.id", "DESC")
            ->getQuery();
        //$srh = isset($params["searchText"]) ? $params["searchText"] : '';
        $list = \DataTable_Helper::generateDataTableResponse(
            $query,
            $params,
            array("__identifier" => 'widget.id', 'widget.id','widget.title','widget.content'),
            array(),
            array()
        );
        return $list;
    }

    public static function searchKeyword($keyword)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('w')
            ->from('KC\Entity\Widget', 'w')
            ->setParameter(1, $keyword.'%')
            ->where($queryBuilder->expr()->like('w.title', '?1'))
            ->setParameter(2, '1')
            ->andWhere('w.status = ?2')
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
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        //echo $data->getSqlQuery(); die;
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
        return $data;

    }

    public function editWidgetRecord($params)
    {
        $content = addslashes($params['content']);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Widget', 'w')
        ->set('w.title', $queryBuilder->expr()->literal(\BackEnd_Helper_viewHelper::stripSlashesFromString($params['title'])))
        ->set('w.content', $queryBuilder->expr()->literal(\BackEnd_Helper_viewHelper::stripSlashesFromString($content)))
        ->setParameter(1, $params['id'])
        ->where('w.id = ?1')
        ->getQuery();
        $query->execute();
        //echo $data->getSqlQuery(); die();
        //call cache function
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
           // print_r($query);die;
            $query->execute();

            

            
            /*$u = Doctrine_Core::getTable("Widget")->find($id);
            $u->delete();*/
        } else {
            $id = null;
        }
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
        return $id;
    }

    public static function getAllUrls($id)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('p.permaLink,w.id')
            ->from('KC\Entity\Widget', 'w')
            ->leftJoin('w.page p')
            ->setParameter(1, $id)
            ->where('w.id = ?1')
            ->setParameter(2, 0)
            ->andWhere('p.deleted = ?2');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $urlsArray = array();
        # check this widget has one or more related pages
        if (isset($data['page']) && count($data['page']) > 0) {
            # traverse through all shops
            foreach ($data['page'] as $value) {
                # check if a category has permalink then add it into array
                if (isset($value['permaLink']) && strlen($value['permaLink']) > 0) {
                    $urlsArray[] = $value['permaLink'];
                }
            }
        }
        return $urlsArray ;
    }
}
