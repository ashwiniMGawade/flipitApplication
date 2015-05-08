<?php

namespace KC\Repository;

class PopularCategory extends \KC\Entity\PopularCategory
{
    public static function searchTopTenPopulerCategory($status = "", $keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $lastdata = self::getPopularCategories();

        if (sizeof($lastdata)>0) {
            for ($i=0; $i<sizeof($lastdata); $i++) {
                $shopdata[$i]=$lastdata[$i]['categoryId'];
            }
            $shopvalues = implode(",", $shopdata);
        } else {
            $shopvalues = '0';
        }

        $data = $queryBuilder
            ->select('o')
            ->from("KC\Entity\Category", "o")
            ->where($queryBuilder->expr()->eq('o.deleted', $queryBuilder->expr()->literal($flag)))
            ->andWhere($queryBuilder->expr()->eq('o.status', $queryBuilder->expr()->literal($status)))
            ->andWhere($queryBuilder->expr()->like('o.name', $queryBuilder->expr()->literal($keyword.'%')))
            ->andWhere($queryBuilder->expr()->notIn("o.id", $shopvalues))
            ->orderBy("o.name", "ASC")
            ->setMaxResults(10)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getPopularCategories()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder
            ->select('p.id,o.name,p.type,p.position,IDENTITY(p.category) as categoryId')
            ->from('KC\Entity\PopularCategory', 'p')
            ->leftJoin('p.category', 'o')
            ->where('o.deleted=0')
            ->orderBy('p.position', 'ASC')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;

    }

    public static function addCategoryInPopulerCategory($title)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $title = addslashes($title);
        $catg = $queryBuilder
            ->select('c')
            ->from('KC\Entity\Category', 'c')
            ->where($queryBuilder->expr()->eq('c.name', $queryBuilder->expr()->literal($title)))
            ->setMaxResults(1)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $flag = '2';

        if (sizeof($catg) > 0) {
            //check offer exist or not
            $pc = \Zend_Registry::get('emLocale')
            ->getRepository('KC\Entity\PopularCategory')
            ->findOneBy(array('category' => $catg[0]['id']));

            if (sizeof($pc) > 0) {
            } else {
                $flag = '1';
                //find last postion  from database
                $data = $queryBuilder
                    ->select('p.position')
                    ->from('KC\Entity\PopularCategory', 'p')
                    ->orderBy('p.position', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                if (sizeof($data) > 0) {
                    $NewPos = $data[0]['position'];
                } else {
                    $NewPos = 1;
                }

                $pc = new \KC\Entity\PopularCategory();
                $pc->type = 'MN';
                $pc->category = \Zend_Registry::get('emLocale')
                    ->getRepository('KC\Entity\Category')
                    ->find($catg[0]['id']);
                $pc->position = (intval($NewPos) + 1);
                $pc->deleted = 0;
                $pc->total_offers = 0;
                $pc->total_coupons = 0;
                $pc->created_at = new \DateTime('now');
                $pc->updated_at = new \DateTime('now');
                \Zend_Registry::get('emLocale')->persist($pc);
                \Zend_Registry::get('emLocale')->flush();
                $flag = array('position'=>intval($NewPos) + 1, 'id'=>$pc->id, 'categoryId'=>$catg[0]['id'], 'type'=>'MN');
            }
        }

        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
        return $flag;
    }

    public static function deletePopulerCategory($id, $position)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        if ($id) {
            //delete popular code from list
            $pc = $queryBuilder
                ->delete('KC\Entity\PopularCategory', 'pc')
                ->where('pc.id=' . $id)
                ->getQuery()
                ->execute();
            //change position by 1 of each below element
            $q = $queryBuilder
                ->update('KC\Entity\PopularCategory', 'p')
                ->set('p.position', 'p.position -1')
                ->where('p.position >' . $position)
                ->getQuery()
                ->execute();
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
            return true ;
        }

        return  false ;
    }

    public static function moveUpPopulerCategory($id, $position)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $pos = (intval($position) - 1);
        //find prev element from database based of current
        $PrevPc = \Zend_Registry::get('emLocale')
            ->getRepository('KC\Entity\PopularCategory')
            ->findOneBy(array('position' => $pos));
        //change position of prev element with current
        //$flag =  1;
        if (count($PrevPc) > 0) {
            //$flag =2;
            $changePrevPc = \Zend_Registry::get('emLocale')
                ->getRepository('KC\Entity\PopularCategory')
                ->find($PrevPc->id);
            $changePrevPc->position = $position;
            $changePrevPc->deleted = 0;
            $changePrevPc->created_at = $changePrevPc->created_at;
            $changePrevPc->updated_at = new \DateTime('now');
            \Zend_Registry::get('emLocale')->persist($changePrevPc);
            \Zend_Registry::get('emLocale')->flush();
            //change position of current element with postition + 1
            $pc = \Zend_Registry::get('emLocale')
                ->getRepository('KC\Entity\PopularCategory')
                ->find($id);
            $pc->position = $pos;
            $pc->deleted = 0;
            $pc->created_at = $pc->created_at;
            $pc->updated_at = new \DateTime('now');
            \Zend_Registry::get('emLocale')->persist($pc);
            \Zend_Registry::get('emLocale')->flush();
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
            return true ;
        }

        return false;
    }

    public static function moveDownPopulerCategory($id, $position)
    {
        $pos = (intval($position) + 1);
        //find next element from database based of current
        $PrevPc = \Zend_Registry::get('emLocale')
            ->getRepository('KC\Entity\PopularCategory')
            ->findOneBy(array('position' => $pos));
        //change position of next element with current
        if (count($PrevPc) > 0) {
            $changePrevPc = \Zend_Registry::get('emLocale')
                ->getRepository('KC\Entity\PopularCategory')
                ->find($PrevPc->id);
            $changePrevPc->position = $position;
            $changePrevPc->deleted = 0;
            $changePrevPc->created_at = $changePrevPc->created_at;
            $changePrevPc->updated_at = new \DateTime('now');
            \Zend_Registry::get('emLocale')->persist($changePrevPc);
            \Zend_Registry::get('emLocale')->flush();
            //change position of current element with postition - 1
            $pc = \Zend_Registry::get('emLocale')
                ->getRepository('KC\Entity\PopularCategory')
                ->find($id);
            $pc->position = $pos;
            $pc->deleted = 0;
            $pc->created_at = $pc->created_at;
            $pc->updated_at = new \DateTime('now');
            \Zend_Registry::get('emLocale')->persist($pc);
            \Zend_Registry::get('emLocale')->flush();
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
            return true ;
        }
        return false;
    }
}
