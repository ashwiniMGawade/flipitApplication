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
            $image = new \KC\Entity\Image();
            $image->path = $params['hidimage'];
            $image->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['hidimageorg']);
            $image->type = 'menuIcon';
            $image->created_at = new \DateTime('now');
            $image->updated_at = new \DateTime('now');
            $image->deleted = '0';
            $entityManagerLocale->persist($image);
            $entityManagerLocale->flush();
        }

        $menu = new \KC\Entity\Menu();
        $menu->name = \BackEnd_Helper_viewHelper::stripSlashesFromString(htmlentities($params['label'], ENT_QUOTES, 'UTF-8'));
        $menu->url = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['url']);
        $menu->parentId = 0;
        $menu->level = 0;

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

        $repo = $entityManagerLocale->getRepository('KC\Entity\Menu');
        $updateMenu = $repo->find($menu->id);
        $updateMenu->root_id = $menu->id;
        $entityManagerLocale->persist($menu);
        $entityManagerLocale->flush();

        if ($params['url']!='') {
            $repo = $entityManagerLocale->getRepository('KC\Entity\RoutePermalink');
            $getRecord = $repo->findOneBy(array('exactlink' => $params['url']));
            if (!empty($getRecord)) {
                $entityManagerLocale->remove($getRecord);
                $entityManagerLocale->flush();
            }
            
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $route = new \KC\Entity\RoutePermalink();
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
            $image = new \KC\Entity\Image();
            $image->path = $params['hidimage'];
            $image->name = $params['hidimageorg'];
            $image->type = 'menuIcon';
            $image->deleted = '0';
            $image->created_at = new \DateTime('now');
            $image->updated_at = new \DateTime('now');
            $entityManagerLocale->persist($image);
            $entityManagerLocale->flush();
        }

        $child1 = new \KC\Entity\Menu();
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
            ->where('menu.level = 0')
            ->orderBy('menu.position', 'ASC');
        $menuList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $menuList;

    }

    public static function getmenuRecord($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('m, i.path, i.name')
            ->from('KC\Entity\Menu', 'm')
            ->leftJoin("m.menuIcon", "i")
            ->where('m.id =' . $id);
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function editmenuRecord($params)
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        if ($params['position']=='' || $params['position']==0) {
            $position = '1';
        } else {
            $position = $params['position'];
        }

        $imageid = null;
        if ($params['imageid']!= 0) {
            $imageid = $params['imageid'];
            $image =  $entityManagerLocale->find('KC\Entity\Image', $params['imageid']);
            $image->path = $params['hidimage'];
            $image->name = $params['hidimageorg'];
            $image->deleted = $image->deleted;
            $image->created_at = $image->created_at;
            $image->updated_at = new \DateTime('now');
            $entityManagerLocale->persist($image);
            $entityManagerLocale->flush();
        }

        if ($params['imageid']=='' && $params['hidimage']!='') {
            $image = new KC\Entity\Image();
            $image->path = '"'.$params['hidimage'].'"';
            $image->name = '"'.$params['hidimageorg'].'"';
            $image->type = 'menuIcon';
            $image->deleted = 0;
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
            ->where('m.root_id ='. $id)
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
            ->where('m.level = 0');
        $menuList = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $menuList;
    }

    public static function deleteMenuRecord($params)
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $menu =  $entityManagerLocale->find('KC\Entity\Menu', $params['id']);
        $entityManagerLocale->remove($menu);
        $entityManagerLocale->flush();
        if ($params['parentId']==0 || $params['parentId']==null) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\Menu', 'm')
                ->where("m.root_id=" .$params['id'])
                ->getQuery()->execute();
        }

        if ($params['parentId']!=0 && $params['parentId']!=null) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\Menu', 'm')
                    ->where("m.parentId=" .$params['id'])
                    ->getQuery()->execute();
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
   
    public static function getFirstLevelMenu($navigation = '')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('menu')
            ->from('KC\Entity\Menu', 'menu')
            ->where('menu.parentId = 0')
            ->orderBy('menu.position', 'ASC');
        if ($navigation == 'mobile') {
            $query = $query->andWhere("menu.name != 'plus'");
        }
        $mainMenu = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $mainMenu;
    }
}
