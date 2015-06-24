<?php
namespace KC\Repository;
class PopularVouchercodes extends \Core\Domain\Entity\PopularVouchercodes
{
    public static function searchTopTenOffer($keyword, $flag)
    {
        $lastdata = self::getPopularvoucherCode();

        if (sizeof($lastdata)>0) {
            for ($i=0; $i<sizeof($lastdata); $i++) {
                $codevalues[$i] = $lastdata[$i]['offerId'];
            }
            $codevalues = implode(",", $codevalues);
        } else {
            $codevalues = '0';
        }
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('p')
        ->from('\Core\Domain\Entity\PopularCode', 'p')
        ->leftJoin('p.popularcode', 'offer')
        ->where('offer.deleted = 0')
        ->andWhere('offer.offline = 0')
        ->andWhere("offer.title LIKE '$keyword%'")
        ->setParameter(1, $queryBuilder->expr()->literal($codevalues))
        ->andWhere($queryBuilder->expr()->notIn('offer.id', '?1'))
        ->setMaxResults(10);
        $offer = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offer;
    }

    public static function getPopularvoucherCode()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('p.id,o.title,p.type,p.position,o.id as offerId')
        ->from('\Core\Domain\Entity\PopularVouchercodes', 'p')
        ->leftJoin('p.offer', 'o')
        ->orderBy('p.position', 'ASC');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function addOfferInVouchercode($title)
    {
        //find offer by title
        $title = addslashes($title);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('o')
        ->from('\Core\Domain\Entity\Offer', 'o')
        ->where('o.title = '."'".$title."'")
        ->setmaxResults(1);
        $Offer = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $flag = '2';


        if (sizeof($Offer) > 0) {
            //check offer exist or not
            $query = $queryBuilder
            ->select('px')
            ->from('\Core\Domain\Entity\PopularVouchercodes', 'px')
            ->leftJoin('px.offer', 'offer')
            ->where('offer.id ='.$Offer[0]['id']);
            $pc = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            if (sizeof($pc) > 0) {
            } else {
                $flag = '1';
                //find last postion  from database
                $query = $queryBuilder
                ->select('p.position')
                ->from('\Core\Domain\Entity\PopularVouchercodes', 'p')
                ->orderBy('p.position', 'DESC')
                ->setMaxResults(1);
                $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                if (sizeof($data) > 0) {
                    $NewPos = $data[0]['position'];
                } else {
                    $NewPos = 1;
                }               //add new offer if not exist in datbase
                $entityManagerLocale  = \Zend_Registry::get('emLocale');
                $pc = new \Core\Domain\Entity\PopularVouchercodes();
                $pc->type = 'MN';
                $pc->vaoucherofferId = $entityManagerLocale->find('\Core\Domain\Entity\Offer', $Offer[0]['id']);
                $pc->position = (intval($NewPos) + 1);
                $pc->deleted = 0;
                $pc->created_at = new \DateTime('now');
                $pc->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($pc);
                $entityManagerLocale->flush();
            }
        }
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        return $flag;
    }

    public static function deletePapularvocherCode($id, $position)
    {
        if ($id) {
            //delete popular code from list
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('\Core\Domain\Entity\PopularVouchercodes', 'p')
                ->where("p.id=" . $id)
                ->getQuery()->execute();

            //change position by 1 of each below element
            $queryBuilder->update('\Core\Domain\Entity\PopularVouchercodes', 'pvc')
                ->set('pvc.position', 'pvc.position - 1')
                ->where('pvc.position > '.$position)
                ->getQuery()->execute();
            //call cache function
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_popularShops_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll(
                'all_popularVoucherCodesList_feed'
            );
        }
    }

    public static function gethomePopularvoucherCode($flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('p.id,o.title,o.couponCode as couponcode,o.exclusiveCode as exclusivecode,o.discount,o.discountvalueType,s.id as shopId, s.name as shopName,s.permaLink,l.path,l.name,p.type,p.position')
        ->from('\Core\Domain\Entity\PopularVouchercodes', 'p')
        ->leftJoin('p.offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'l')
        ->where('o.deleted = 0')
        ->andWhere('s.deleted = 0')
        ->orderBy('p.position', 'ASC')
        ->setMaxResults($flag);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getNewstoffer($flag)
    {
        $date = date('Y-m-d H:i:s');
        //$memOnly = "MEM";
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('o.title,o.Visability,o.couponCodeType,o.discountType,o.couponCode,o.exclusiveCode,o.editorPicks,o.discount,o.startDate as startdate,o.endDate as enddate,o.discountvalueType,s.name as shopName,s.permaLink,s.views,l.name, l.path')
        ->from('\Core\Domain\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'l')
        ->setParameter(1, "MEM")
        ->where('o.Visability!= ?1')
        ->setParameter(2, "CD")
        ->andWhere('o.discountType = ?2')
        ->andWhere("(o.couponCodeType = 'UN' AND (SELECT count(cc.id)  FROM \Core\Domain\Entity\CouponCode cc WHERE cc.offer = o.id and cc.status=1)  > 0) or o.couponCodeType = 'GN'")
        ->andWhere('s.status = 1')
        ->andWhere('o.deleted = 0')
        ->andWhere("o.userGenerated = 0")
        ->andWhere('o.offline = 0')
        ->andWhere('o.endDate >'."'".$date."'")
        ->andWhere('o.startDate <='."'".$date."'")
        ->andWhere('s.deleted = 0')
        ->orderBy('o.startDate', 'DESC')
        ->setMaxResults($flag);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getSpecialoffer($flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('p.id,p.type,p.position,p.specialOfferId,o.title,o.Visability,o.discountType,o.exclusiveCode,o.discount,o.discountvalueType,s.name as shopName,s.permaLink,s.views,c.name,c.permaLink,i.name, i.path')
        ->from('\Core\Domain\Entity\SpecialList', 'p')
        ->leftJoin('p.offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('o.categoryoffres', 'category')
        ->leftJoin('category.category', 'c')
        ->leftJoin('s.logo', 'i')
        ->where('s.deleted = 0')
        ->orderBy('p.position', 'ASC')
        ->setMaxResults($flag);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function moveUpCode($id, $position)
    {
        $pos = (intval($position) - 1);
        //find prev element from database based of current
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('p')
        ->from('\Core\Domain\Entity\PopularVouchercodes', 'p')
        ->where('p.position = '.$pos);
        $PrevPc = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        //change position of prev element with current
        //$flag =  1;
        if (count($PrevPc) > 0) {
            //$flag =2;
            $queryBuilder->update('\Core\Domain\Entity\PopularVouchercodes', 'pvc')
            ->set('pvc.position', $position)
            ->where('pvc.id = '.$PrevPc[0]['id'])
            ->getQuery()->execute();
            //change position of current element with postition + 1
            $queryBuilder->update('\Core\Domain\Entity\PopularVouchercodes', 'pvcodes')
                ->set('pvcodes.position', $pos)
                ->where('pvcodes.id = '.$id)
                ->getQuery()->execute();
        }
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        //return $flag;
    }

    public static function moveDownCode($id, $position)
    {
        $pos = (intval($position) + 1);
        //find next element from database based of current
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('p')
        ->from('\Core\Domain\Entity\PopularVouchercodes', 'p')
        ->where('p.position = '.$pos);
        $PrevPc = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        //change position of next element with current
        if (count($PrevPc) > 0) {
            $queryBuilder->update('\Core\Domain\Entity\PopularVouchercodes', 'pvc')
            ->set('pvc.position', $position)
            ->where('pvc.id = '.$PrevPc[0]['id'])
            ->getQuery()->execute();
            //change position of current element with postition - 1
            $queryBuilder->update('\Core\Domain\Entity\PopularVouchercodes', 'pvcodes')
            ->set('pvcodes.position', $pos)
            ->where('pvcodes.id = '.$id)
            ->getQuery()->execute();
        }
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll(
            'all_popularvaouchercode_list_shoppage'
        );
    }
}
