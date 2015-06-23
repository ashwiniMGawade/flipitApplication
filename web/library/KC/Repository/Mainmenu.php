<?php
namespace KC\Repository;

class Mainmenu extends \Core\Domain\Entity\Mainmenu
{
    public static function insertOne($params)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        if ($params['hidimage']!='') {
            $image = new \KC\Entity\Image();
            $image->path = $params['hidimage'];
            $image->name = $params['hidimageorg'];
            $image->type = 'menuIcon';
            $image->deleted = 0;
            $image->created_at = new \DateTime('now');
            $image->updated_at = new \DateTime('now');
            $entityManagerLocale->persist($image);
            $entityManagerLocale->flush();
        }
        $menu = new \KC\Entity\mainmenu();
        $menu->name = $params['label'];
        $menu->url = $params['url'];
        if ($params['position']!='') {
            $menu->position = $params['position'];
        }
        if ($params['position']=='') {
            $menu->position = 1;
        }
        if ($params['hidimage']!='') {
            $menu->iconId = $image->id;
        }
        $menu->deleted = 0;
        $menu->created_at = new \DateTime('now');
        $menu->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($menu);
        $entityManagerLocale->flush();
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
            $image->deleted = 0;
            $image->created_at = new \DateTime('now');
            $image->updated_at = new \DateTime('now');
            $entityManagerLocale->persist($image);
            $entityManagerLocale->flush();
        }

        $child1 = new \KC\Entity\Mainmenu();
        $child1->name = $params['label'];
        $child1->url = $params['url'];
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
        $repo = $entityManagerLocale->getRepository('KC\Entity\Mainmenu');
        $menu = $repo->findOneBy(array('Name' =>  'Child menu 1'));
        $entityManagerLocale->remove($menu);
        $entityManagerLocale->flush();

    }

    public static function moveOne()
    {
        $menu       = new \KC\Entity\Mainmenu();
        $menu->name = 'Root menu 2';
        $menu->deleted = 0;
        $menu->created_at = new \DateTime('now');
        $menu->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($menu);
        $entityManagerLocale->flush();
    }
  

    public static function getMainMenuList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('menu')
            ->from('KC\Entity\Mainmenu', 'menu')
            ->orderBy('menu.position', 'ASC');
        $menuList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $menuList;
    }

    public static function getmenuList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('menu')
            ->from('KC\Entity\Mainmenu', 'menu')
            ->where('menu.level = 0')
            ->orderBy('menu.position', 'ASC');
        $menuList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $menuList;
    }

    public static function getmenuRecord($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('m, i.path, i.name')
            ->from('KC\Entity\Mainmenu', 'm')
            ->leftJoin("m.menuIcon", "i")
            ->where('m.id ='. $id);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;

    }

    public static function editmenuRecord($params)
    {
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

        $menu =  $entityManagerLocale->find('KC\Entity\Mainmenu', $params['hid']);
        $menu->name = \BackEnd_Helper_viewHelper::stripSlashesFromString(htmlentities($params['label'], ENT_QUOTES, 'UTF-8'));
        $menu->url = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['url']);
        $menu->position = $position;
        $menu->iconId = $imageid;
        $entityManagerLocale->persist($menu);
        $entityManagerLocale->flush();

        return $params;
    }

    public static function getrtmainmenuRecord($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('m')
            ->from('KC\Entity\Mainmenu', 'm')
            ->where('m.root_id =' . $id)
            ->addOrderBy('m.position', 'ASC');
        $menuList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $menuList;

    }

    public static function getmainhighposition()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('m, MIN(m.position)')
            ->from('KC\Entity\Mainmenu', 'm')
            ->where('m.level = 0');
        $menuList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $menuList;
    }

    public static function getSecondLevel($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('m')
            ->from('KC\Entity\Mainmenu', 'm')
            ->where('m.parentId =' . $id)
            ->addOrderBy('m.position', 'ASC');
        $menuList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $menuList;
    }

    public static function getThirdLevel($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('m')
            ->from('KC\Entity\Mainmenu', 'm')
            ->where('m.parentId =' . $id)
            ->addOrderBy('m.position', 'ASC');
        $menuList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $menuList;
    }

    public static function deleteMenuRecord($params)
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $menu =  $entityManagerLocale->find('KC\Entity\Mainmenu', $params['id']);
        $entityManagerLocale->remove($menu);
        $entityManagerLocale->flush();

        if ($params['parentId']==0 || $params['parentId']==null) {
            $repo = $entityManagerLocale->getRepository('KC\Entity\Mainmenu');
            $menu = $repo->findOneBy(array('root_id' => @$params['id']));
            $entityManagerLocale->remove($menu);
            $entityManagerLocale->flush();
        }

        if ($params['parentId']!=0 && $params['parentId']!=null) {
            $repo = $entityManagerLocale->getRepository('KC\Entity\Mainmenu');
            $menu = $repo->findOneBy(array('parentId' => @$params['id']));
            $entityManagerLocale->remove($menu);
            $entityManagerLocale->flush();
        }
        return true;
    }
}
