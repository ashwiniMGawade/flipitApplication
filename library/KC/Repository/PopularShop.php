<?php
namespace KC\Repository;
class PopularShop Extends \KC\Entity\PopularShop
{
    public static function searchTopTenshop($keyword, $flag)
    {
        $lastdata = self::getPopularShop();

        if (sizeof($lastdata)>0) {
            for ($i=0; $i<sizeof($lastdata); $i++) {
                $shopdata[$i]=$lastdata[$i]['shopId'];
            }
            $shopvalues = implode(",", $shopdata);
        } else {
            $shopvalues = '0';
        }

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('o.name as name')
            ->from('KC\Entity\Shop', 'o')
            ->where('o.deleted ='.$flag)
            ->andWhere('o.status = 1')
            ->andWhere("o.name LIKE '$keyword%'")
            ->setParameter(1, $queryBuilder->expr()->literal($shopvalues))
            ->andWhere($queryBuilder->expr()->notIn('o.id', '?1'))
            ->orderBy('o.name', 'ASC')
            ->setMaxResults(10);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getPopularShop()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('p.id,s.name,p.type,p.position,s.id as shopId')
        ->from('KC\Entity\PopularShop', 'p')
        ->leftJoin('p.popularshops', 's')
        ->orderBy('p.position', 'ASC');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function addShopInList($title)
    {
        //find SHOP by title
        $title = addslashes($title);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('s')
        ->from('KC\Entity\Shop', 's')
        ->where('s.name = '."'".$title."'")
        ->andWhere('s.status = 1')
        ->andWhere('s.deleted = 0')
        ->setMaxResults(1);
        $shop = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $flag = '2';
        if (sizeof($shop) > 0) {

            //check offer exist or not
            $queryBuildershopId = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuildershopId
            ->select('px')
            ->from('KC\Entity\PopularShop', 'px')
            ->leftJoin('px.popularshops', 'ps')
            ->where('ps.id ='.$shop[0]['id']);
            $pc = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if (sizeof($pc) > 0) {
            } else {

                $flag = '1';
            //find last postion  from database
                $queryBuilderPosition = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilderPosition
                ->select('p.position')
                ->from('KC\Entity\PopularShop', 'p')
                ->orderBy('p.position', 'DESC')
                ->setMaxResults(1);
                $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                if (sizeof($data) > 0) {
                    $NewPos = $data[0]['position']+1;
                } else {
                    $NewPos = 1;
                }
                //add new offer if not exist in datbase
                $entityManagerLocale  = \Zend_Registry::get('emLocale');
                $pc = new \KC\Entity\PopularShop();
                $pc->type = 'MN';
                $pc->popularshops = $entityManagerLocale->find('KC\Entity\Shop', $shop[0]['id']);
                $pc->position = (intval($NewPos));
                $pc->deleted = 0;
                $pc->status = 1;
                $pc->created_at = new \DateTime('now');
                $pc->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($pc);
                $entityManagerLocale->flush();
                $lastInsertedId = $pc->getId();
                return $flag =
                array('type'=>'MN', 'position'=>intval($NewPos), 'shopId'=>$shop[0]['id'], 'id'=>$lastInsertedId);
            }

        }
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        return $flag;
    }

    public static function deletePapularCode($id, $position)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\PopularShop', 'p')
                ->where("p.id=" . $id)
                ->getQuery()->execute();
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('search_pageHeader_image');
        }
    }

    public static function moveUp($id, $position)
    {
        $pos = (intval($position) - 1);
        //find prev element from database based of current
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('ps')
        ->from('KC\Entity\PopularShop', 'ps')
        ->where('ps.position = '.$pos);
        $PrevPc = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $pid = @$PrevPc[0]['id'];
        if ($pid) {
            $queryBuilder->update('KC\Entity\PopularShop', 'p')
                    ->set('p.position', $position)
                    ->where('p.id = '.$pid)
                    ->getQuery()->execute();
            //change position of current element with postition + 1
            $queryBuilder->update('KC\Entity\PopularShop', 'pshop')
                    ->set('pshop.position', $pos)
                    ->where('pshop.id = '.$id)
                    ->getQuery()->execute();
            //call cache function
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
            return true ;
        }
        return false ;
    }

    public static function moveDown($id, $position)
    {
        $pos = (intval($position) + 1);
        //find next element from database based of current
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('ps')
        ->from('KC\Entity\PopularShop', 'ps')
        ->where('ps.position = '.$pos);
        $PrevPc = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $pid = @$PrevPc[0]['id'];
        //change position of next element with current
        if ($pid) {
            $queryBuilder->update('KC\Entity\PopularShop', 'p')
                    ->set('p.position', $position)
                    ->where('p.id = '.$pid)
                    ->getQuery()->execute();
            //change position of current element with postition + 1
            $queryBuilder->update('KC\Entity\PopularShop', 'pshop')
                    ->set('pshop.position', $pos)
                    ->where('pshop.id = '.$id)
                    ->getQuery()->execute();
            //call cache function
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
            return true ;
        }
        return false ;
    }

    public static function savePopularShopsPosition($shopId)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        if (!empty($shopId)) {
            $query = $queryBuilder->delete('KC\Entity\PopularShop', 'p')
                ->where('p.id > 0')
                ->getQuery()->execute();
            $shopId = explode(',', $shopId);
            $i = 1;
            foreach ($shopId as $shopIdValue) {
                $popularShop = new \KC\Entity\PopularShop();
                $popularShop->popularshops = $entityManagerLocale->find('KC\Entity\Shop', $shopIdValue);
                $popularShop->position = $i;
                $popularShop->type = "MN";
                $popularShop->deleted = 0;
                $popularShop->status = 1;
                $popularShop->created_at = new \DateTime('now');
                $popularShop->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($popularShop);
                $entityManagerLocale->flush();
                $i++;
            }
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularShopsForDropdown_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('shop_popularShopForWidget_list');
    }

    public static function generatePopularCode()
    {
        $format = 'Y-m-j 00:00:00';
        $date = date($format);
        // - 4 days from today
        $past4Days = date($format, strtotime('-4 day' . $date));
        $nowDate = $date;
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('v.id,o.id as offerId')
        ->from('KC\Entity\ViewCount', 'v')
        ->leftJoin('v.viewcount', 'o')
        ->where('v.updated_at <='."'".$nowDate."'")
        ->andWhere('v.updated_at >='."'".$past4Days."'")
        ->groupBy('v.viewcount')
        ->orderBy('v.onClick', 'DESC')
        ->setMaxResults(10);
        $NewpapularCode = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        //get last position id from database
        $query = $queryBuilder
        ->select('p.position')
        ->from('KC\Entity\PopularCode', 'p')
        ->orderBy('p.position', 'DESC')
        ->setMaxResults(1);
        $lastPostionOffer = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (sizeof($lastPostionOffer) > 0) {
                    $lastPos = intval($lastPostionOffer[0]['position']) + 1;
        } else {
            $lastPos = 1;
        }

        //get all existing popular code from database
        $query = $queryBuilder
        ->select('pc.id,offer.id as offerId,pc.type,pc.position')
        ->from('KC\Entity\PopularCode', 'pc')
        ->leftJoin('pc.popularcode', 'offer')
        ->orderBy('pc.position');
        $allExistingOffer = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    
        $temp = array();
        //loop for generate temp array for maching purpose
        foreach ($NewpapularCode as $popular) {
            $temp[$popular['offerId']] = $popular;
        }
        $newArray = array();
        //generate new array ( combine with existing and new popular code
        foreach ($temp as $key => $t) {
            if (sizeof($allExistingOffer) > 0) {
                foreach ($allExistingOffer as $exist) {
                    if ($key == $exist['offer']['id']) {
                        $Ar = array(
                            'type' => $exist['type'],
                            'popularcode' => $exist['offer']['id'],
                            'position' => $exist['position']
                        );
                        $newArray[$key] = $Ar;
                    } else {
                        if (!array_key_exists($key, $newArray)) {
                            $Ar = array(
                                'type' => 'AT',
                                'popularcode' => $key,
                                'position' => $lastPos
                            );
                            $newArray[$key] = $Ar;
                            if (!array_key_exists($exist['offer']['id'], $temp)) {
                                $lastPos++;
                            }
                        }
                    }
                }
            } else {
                $Ar = array(
                    'type' => 'AT',
                    'popularcode' => $key,
                    'position' => $lastPos
                );
                $newArray[$key] = $Ar;
                $lastPos++;

            }
        }
        foreach ($newArray as $p) {
            $query = $queryBuilder
            ->select('pcode')
            ->from('KC\Entity\PopularCode', 'pcode')
            ->where('pcode.popularcode ='.$p['offerId']);
            $pc = $query->getQuery()->getResult();
            if (sizeof($pc) > 0) {
            } else {
                $entityManagerLocale  = \Zend_Registry::get('emLocale');
                $pc = new \KC\Entity\PopularCode();
                $pc->type = 'AT';
                $pc->popularcode = $p['offerId'];
                $pc->position = $p['position'];
                $pc->deleted = 0;
                $pc->created_at = new \DateTime('now');
                $pc->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($pc);
                $entityManagerLocale->flush();
            }
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
    }
}
