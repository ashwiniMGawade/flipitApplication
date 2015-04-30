<?php
namespace KC\Repository;

class SpecialList extends \KC\Entity\SpecialList
{
    public static function getSpecialPages($limit = '')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDateAndTime = date('Y-m-d H:i:s');
        $query = $queryBuilder->select('sp, p, l')
            ->addSelect("(SELECT count(roc) FROM KC\Entity\RefOfferPage roc LEFT JOIN roc.refoffers off LEFT JOIN off.shopOffers s  WHERE roc.offers = sp.page and off.deleted = 0 and s.deleted = 0 and off.endDate >'".$currentDateAndTime."' and off.startDate <= '".$currentDateAndTime."'  and off.discountType='CD'  and off.Visability!='MEM') as totalCoupons")
            ->addSelect("(SELECT count(roc1) FROM KC\Entity\RefOfferPage roc1 LEFT JOIN roc1.refoffers off1 LEFT JOIN off1.shopOffers s1  WHERE roc1.offers = sp.page and off1.deleted = 0 and s1.deleted = 0 and off1.endDate >'".$currentDateAndTime."' and off1.startDate <= '".$currentDateAndTime."' and off1.Visability!='MEM') as totalOffers")
            ->from('KC\Entity\SpecialList', 'sp')
            ->leftJoin('sp.page', 'p')
            ->leftJoin('p.logo', 'l')
            ->where('p.deleted = 0')
            ->andWhere('p.publish = 1');
        if ($limit!= '') {
            $query->setMaxResults($limit);
        }
        $query->orderBy('sp.position', 'ASC');
        $specialPages = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $specialPages;
    }

    public static function getSpecialPagesIds()
    {
        $currentDateAndTime = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p, sp, l')
            ->from('KC\Entity\SpecialList', 'sp')
            ->leftJoin('sp.page', 'p')
            ->leftJoin('p.logo', 'l')
            ->where('p.deleted = 0')
            ->andWhere('p.publish = 1')
            ->orderBy('sp.position', 'ASC');
        $specialPageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $specialPageDetails;
    }

    public static function updateTotalOffersAndTotalCoupons($totalOffers, $totalCoupons, $specialPageId)
    {
        if (!empty($specialPageId)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->update('KC\Entity\SpecialList', 'sl')
                ->set('sl.total_offers', $totalOffers)
                ->set('sl.total_coupons', $totalCoupons)
                ->where('sl.page ='.$specialPageId)
                ->getQuery()->execute();
        }
        return true;
    }

    public static function searchTopTenOffer($keyword, $flag)
    {
        $lastdata=self::getsplpage();

        if (sizeof($lastdata)>0) {
            for ($i=0; $i<sizeof($lastdata); $i++) {
                $codevalues[$i]=$lastdata[$i]['specialpageId'];
            }
            $codevalues = implode(",", $codevalues);
        } else {
            $codevalues = '0';
        }
 
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('p.pageTitle as title')
            ->from('KC\Entity\Page', 'p')
            ->where('p.deleted = 0')
            ->andWhere("p INSTANCE OF KC\Entity\OfferListPage")
            ->setParameter(1, $keyword."%")
            ->andWhere($queryBuilder->expr()->like('p.pageTitle', '?1'))
            ->setParameter(2, $codevalues)
            ->andWhere($queryBuilder->expr()->notIn('p.id', '?2'))
            ->setMaxResults(10);
        $topTenOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $topTenOffers;
    }

    public static function getsplpage()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('sp.type,sp.position,IDENTITY(sp.page) as specialpageId,p.pageTitle as title')
            ->from('KC\Entity\SpecialList', 'sp')
            ->leftJoin('sp.page', 'p')
            ->orderBy('sp.position', 'ASC');
        $specialPage = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $specialPage;
    }

    public static function addOfferInList($title)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $entityManager = \Zend_Registry::get('emLocale');
        $title = addslashes($title);
        $query = $queryBuilder->select('p')
            ->from('KC\Entity\Page', 'p')
            ->where('p.pageTitle ='."'".$title."'")
            ->setMaxResults(1);
        $page = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $flag = '2';
        if (sizeof($page) > 0) {
            $pc = $entityManager->getRepository('KC\Entity\SpecialList')
                ->findBy(array('page' => $page[0]['id']));

            if (sizeof($pc) > 0) {
            } else {
                $flag = '1';
                $query = $queryBuilder->select('sp.position')
                    ->from('KC\Entity\SpecialList', 'sp')
                    ->orderBy('sp.position', 'DESC')
                    ->setMaxResults(1);
                $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                if (sizeof($data) > 0) {
                    $NewPos = $data[0]['position'];
                } else {
                    $NewPos = 1;
                }
                $pc = new \KC\Entity\SpecialList();
                $pc->type = 'MN';
                $pc->status = '1';
                $pc->page = $entityManager->find('KC\Entity\Page', $page[0]['id']);
                $pc->position = (intval($NewPos) + 1);
                $pc->deleted = 0;
                $pc->total_offers = 0;
                $pc->total_coupons = 0;
                $pc->created_at = new \DateTime('now');
                $pc->updated_at = new \DateTime('now');
                $entityManagerLocale = \Zend_Registry::get('emLocale');
                $entityManagerLocale->persist($pc);
                $entityManagerLocale->flush();
                $flag = $pc;
            }

        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');
        return $flag;
    }

    public static function deletePapularCode($id, $position)
    {
        if ($id) {
            $queryBuilderDelete = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderDelete->delete('KC\Entity\SpecialList', 's')
                ->where('s.page ='.$id)
                ->getQuery();
            $query->execute();

            $entityManagerLocale  =\Zend_Registry::get('emLocale');
            $queryBuilder = $entityManagerLocale->createQueryBuilder();
            $query = $queryBuilder->update('KC\Entity\SpecialList', 'p')
                ->set('p.position', $queryBuilder->expr()->literal('p.position -1'))
                ->setParameter(1, $position)
                ->add('where', $queryBuilder->expr()->gt('p.position', '?1'))
                ->getQuery();
            $query->execute();
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');
            return true;
        }
        return false;
    }

    public static function moveUpSpecial($id, $position)
    {
        $pos = (intval($position) - 1);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('p')
            ->from('KC\Entity\SpecialList', 'p')
            ->where('p.position ='.$pos);
            
        $PrevPc = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (!empty($PrevPc)) {
            $queryBuilderUpdate = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderUpdate
                ->update('KC\Entity\SpecialList', 'sp')
                ->set('sp.position', $position)
                ->where('sp.id = '.$PrevPc[0]['id'])
                ->getQuery()->execute();
           
            $queryBuilderNewposition = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderNewposition
                ->update('KC\Entity\SpecialList', 'spl')
                ->set('spl.position', $pos)
                ->where('spl.page = '.$id)
                ->getQuery()->execute();
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');
            return true;
        }
        return false;
    }

    public static function moveDownSpecial($id, $position)
    {
        $pos = (intval($position) + 1);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('p')
            ->from('KC\Entity\SpecialList', 'p')
            ->where('p.position ='.$pos);
        $PrevPc = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if (!empty($PrevPc)) {
            $queryBuilderUpdate = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderUpdate
                ->update('KC\Entity\SpecialList', 'sp')
                ->set('sp.position', $position)
                ->where('sp.id = '.$PrevPc[0]['id'])
                ->getQuery()->execute();
            $queryBuilderNewposition = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderNewposition
                ->update('KC\Entity\SpecialList', 'spl')
                ->set('spl.position', $pos)
                ->where('spl.page = '.$id)
                ->getQuery()->execute();
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');
            return true;
        }
        return false;
    }
}
