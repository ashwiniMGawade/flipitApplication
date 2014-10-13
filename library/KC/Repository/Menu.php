<?php
namespace KC\Repository;

class Menu extends \KC\Entity\Menu
{
    ######################## Refactored #############################
    public static function replaceBespaarwijzerWithPlus()
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $menu =  $entityManagerLocale->find('KC\Entity\Menu', 46);
        $menu->name = 'plus';
        $menu->url = 'plus';
        $entityManagerLocale->persist($menu);
        $entityManagerLocale->flush();
        return true;
    }

    public static function deleteUnusedMenus()
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $query = $entityManagerLocale->createQuery('DELETE FROM KC\Entity\Menu m WHERE m.level > ?1');
        $query->setParameter(1, 0);
        $query->execute();
        return true;
    }
    ######################## Refactored #############################

    public static function insertOne($params)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        if ($params['hidimage']!='') {
            $image = new KC\Entity\Image();
            $image->path = $params['hidimage'];
            $image->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['hidimageorg']);
            $image->type = 'menuIcon';
            $image->created_at = new \DateTime('now');
            $image->updated_at = new \DateTime('now');
            $image->deleted = '0';
            $entityManagerLocale->persist($image);
            $entityManagerLocale->flush();
        }

        $menu = new Menu();
        $menu->name = \BackEnd_Helper_viewHelper::stripSlashesFromString(htmlentities($params['label'], ENT_QUOTES, 'UTF-8'));
        $menu->url = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['url']);
        $menu->parentId = 0;

        if ($params['position']!='') {
            $menu->position = $params['position'];
        }
        if ($params['position']=='') {
            $menu->position = 1;
        }
        if ($params['hidimage']!='') {
            $menu->iconId = $image->id;
        }
        
        $entityManagerLocale->persist($menu);
        $entityManagerLocale->flush();

        if ($params['url']!='') {
            $repo = $entityManagerLocale->getRepository('KC\Entity\RoutePermalink');
            $getRecord = $repo->findOneBy(array('exactlink' => $params['url']));
            if (!empty($getRecord) > 0) {
                $repo = $entityManagerLocale->getRepository('KC\Entity\RoutePermalink');
                $routePermalink = $repo->findBy(array('exactlink' =>  $params['url']));
                $entityManagerLocale->remove($routePermalink);
                $entityManagerLocale->flush();
            }
            
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $route = new RoutePermalink();
            $route->permalink = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['url']);
            $route->type = 'PG';
            $route->created_at = new \DateTime('now');
            $route->updated_at = new \DateTime('now');
            $route->deleted = '0';
            $route->exactlink = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['url']);
            $entityManagerLocale->persist($route);
            $entityManagerLocale->flush();
        }
        return $params;
    }
     
    public static function insertNode($params)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        if ($params['hidimage']!='') {
            $image = new KC\Entity\Image();
            $image->path = $params['hidimage'];
            $image->name = $params['hidimageorg'];
            $image->type = 'menuIcon';
            $image->created_at = new \DateTime('now');
            $image->updated_at = new \DateTime('now');
            $entityManagerLocale->persist($image);
            $entityManagerLocale->flush();
        }

        $child1 = new Menu();
        $child1->name = \BackEnd_Helper_viewHelper::stripSlashesFromString(
            htmlentities($params['label'], ENT_QUOTES, 'UTF-8')
        );
        $child1->url = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['url']);
        $child1->position = $params['position'];
        $child1->parentId = $params['hid'];

        if ($params['hidimage']!='') {
            $child1->iconId = $image->id;
        }

        $entityManagerLocale->persist($child1);
        $entityManagerLocale->flush();
        if ($params['roothid']!='') {
            return $params['roothid'];
        } else {
            return $params['hid'];
        }
    }

    public static function deleteOne()
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $repo = $entityManagerLocale->getRepository('KC\Entity\Menu');
        $menu = $repo->findOneBy(array('Name' =>  'Child menu 1'));
        $entityManagerLocale->remove($menu);
        $entityManagerLocale->flush();
    }

    public static function getMainMenuList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('menu')
            ->from('KC\Entity\Menu', 'menu')
            ->orderBy('menu.position', 'ASC');
        $menuList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $menuList;
    }
    
    public static function getmenuList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('menu')
            ->from('KC\Entity\Menu', 'menu')
            ->setParameter(1, '0')
            ->where('menu.level = ?1')
            ->orderBy('menu.position', 'ASC');
        $menuList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $menuList;

    }

    public static function getmenuRecord($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('m.*, i.path, i.name')
            ->from('KC\Entity\Menu', 'm')
            ->leftJoin("m.menuIcon", "i")
            ->setParameter(1, $id)
            ->where('m.id = ?1');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function editmenuRecord($params)
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        if ($params['position']=='' || $params['position']==0) {
            $position = '1';
        } else {
            $position = '"'.$params['position'].'"';
        }
        $imageid = null;
        if ($params['imageid']!='') {
            $imageid = $params['imageid'];
            $image =  $entityManagerLocale->find('KC\Entity\Image', $params['imageid']);
            $image->path = $params['hidimage'];
            $image->name = $params['hidimageorg'];
            $entityManagerLocale->persist($image);
            $entityManagerLocale->flush();
        }

        if ($params['imageid']=='' && $params['hidimage']!='') {
            $image = new KC\Entity\Image();
            $image->path = '"'.$params['hidimage'].'"';
            $image->name = '"'.$params['hidimageorg'].'"';
            $image->type = 'menuIcon';
            $image->created_at = new \DateTime('now');
            $image->updated_at = new \DateTime('now');
            $entityManagerLocale->persist($image);
            $entityManagerLocale->flush();
            $imageid = $image->id;
        }

        $menu =  $entityManagerLocale->find('KC\Entity\Menu', $params['hid']);
        $menu->name = \BackEnd_Helper_viewHelper::stripSlashesFromString(htmlentities($params['label'], ENT_QUOTES, 'UTF-8'));
        $menu->url = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['url']);
        $menu->position = $position;
        $menu->iconId = $imageid;
        $entityManagerLocale->persist($menu);
        $entityManagerLocale->flush();
        return $params;
    }

    public static function getrtmenuRecord($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('m')
            ->from('KC\Entity\Menu', 'm')
            ->setParameter(1, $id)
            ->where('m.root_id = ?1')
            ->orderBy('m.level', 'ASC')
            ->addOrderBy('m.position', 'ASC');
        $menuList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $menuList;
    }
    
    public static function gethighposition()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('m, MIN(m.position)')
            ->from('KC\Entity\Menu', 'm')
            ->setParameter(1, '0')
            ->where('m.level = ?1');
        $menuList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $menuList;
    }

    public static function deleteMenuRecord($params)
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $menu =  $entityManagerLocale->find('KC\Entity\Menu', $id);
        $entityManagerLocale->remove($menu);
        $entityManagerLocale->flush();
        if ($params['parentId']==0 || $params['parentId']==null) {
                $repo = $entityManagerLocale->getRepository('KC\Entity\Menu');
                $menu = $repo->findOneBy(array('root_id' => @$params['id']));
                $entityManagerLocale->remove($menu);
                $entityManagerLocale->flush();
        }

        if ($params['parentId']!=0 && $params['parentId']!=null) {
            $repo = $entityManagerLocale->getRepository('KC\Entity\Menu');
            $menu = $repo->findOneBy(array('parentId' => @$params['id']));
            $entityManagerLocale->remove($menu);
            $entityManagerLocale->flush();
        }
        return true;
    }

    public static function deleteAllMenuRecord()
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $menu =  $entityManagerLocale->getRepository('KC\Entity\Menu');
        $entityManagerLocale->remove($menu);
        $entityManagerLocale->flush();
        return true;
    }

    //********************FRONT-END FUNCTION********************//
   
    public static function getFirstLevelMenu()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('menu')
            ->from('KC\Entity\Menu', 'menu')
            ->setParameter(1, '0')
            ->where('menu.parentId = ?1')
            ->orderBy('menu.position', 'ASC');
        $mainMenu = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $mainMenu;
    }
}
