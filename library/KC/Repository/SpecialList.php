<?php
namespace KC\Repository;

class SpecialList extends \KC\Entity\SpecialList
{
    public static function getSpecialPages($limit = '')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDateAndTime = date('Y-m-d H:i:s');
        $specialPages = $queryBuilder->select('sp.type,sp.position,IDENTITY(sp.page) as specialpageId,p.pageTitle,p.permalink,l.name,l.path')
            ->addSelect("(SELECT count(roc) FROM KC\Entity\RefOfferPage roc LEFT JOIN roc.refoffers off LEFT JOIN off.shopOffers s  WHERE roc.offers = sp.page and off.deleted = 0 and s.deleted = 0 and off.endDate >'".$currentDateAndTime."' and off.startDate <= '".$currentDateAndTime."'  and off.discountType='CD'  and off.Visability!='MEM') as totalCoupons")
            ->addSelect("(SELECT count(roc1) FROM KC\Entity\RefOfferPage roc1 LEFT JOIN roc1.refoffers off1 LEFT JOIN off1.shopOffers s1  WHERE roc1.offers = sp.page and off1.deleted = 0 and s1.deleted = 0 and off1.endDate >'".$currentDateAndTime."' and off1.startDate <= '".$currentDateAndTime."' and off1.Visability!='MEM') as totalOffers")
            ->from('KC\Entity\SpecialList', 'sp')
            ->leftJoin('sp.page', 'p')
            ->leftJoin('p.logo', 'l')
            ->where('p.deleted = 0')
            ->andWhere('p.publish = 1')
            ->setMaxResults($limit)
            ->orderBy('sp.position', 'ASC')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $specialPages;
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
            ->andWhere("p.pageType = 'offer'")
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
            ->setParameter(1, $title)
            ->add('where', $queryBuilder->expr()->literal('p.pageTitle', '?1'))
            ->setMaxResults(1);
        $page = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $flag = '2';

        if (sizeof($page) > 0) {
            $pc = $entityManager->getRepository('KC\Entity\SpecialList')
                ->findBy(array('specialpageId' => $page[0]['id']));

            if (sizeof($pc) > 0) {
            } else {
                $flag = '1';
                $query = $queryBuilder->select('p.position')
                    ->from('KC\Entity\SpecialList', 'p')
                    ->orderBy('p.position', 'DESC')
                    ->setMaxResults(1);
                $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                if (sizeof($data) > 0) {
                    $NewPos = $data[0]['position'];
                } else {
                    $NewPos = 1;
                }
                $pc = new KC\Entity\SpecialList();
                $pc->type = 'MN';
                $pc->status = '1';
                $pc->specialpageId = $page[0]['id'];
                $pc->position = (intval($NewPos) + 1);
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
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $repo = $entityManagerLocale->getRepository('KC\Entity\SpecialList');
        $PrevPc = $repo->findOneBy(array('position' =>  $pos));

        if (!empty($PrevPc)) {
            $changePrevPc = $entityManagerLocale->getRepository('KC\Entity\SpecialList')->find($PrevPc->id);
            $changePrevPc->position = $position;
            \Zend_Registry::get('emLocale')->persist($changePrevPc);
            \Zend_Registry::get('emLocale')->flush();
            $pc = $entityManagerLocale->getRepository('KC\Entity\SpecialList')->find($id);
            $pc->position = $pos;
            \Zend_Registry::get('emLocale')->persist($pc);
            \Zend_Registry::get('emLocale')->flush();
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');
            return true;
        }
        return false;
    }

    public static function moveDownSpecial($id, $position)
    {
        $pos = (intval($position) + 1);
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $repo = $entityManagerLocale->getRepository('KC\Entity\SpecialList');
        $PrevPc = $repo->findOneBy(array('position' =>  $pos));

        if (!empty($PrevPc)) {
            $changePrevPc = $entityManagerLocale->getRepository('KC\Entity\SpecialList')->find($PrevPc->id);
            $changePrevPc->position = $position;
            \Zend_Registry::get('emLocale')->persist($changePrevPc);
            \Zend_Registry::get('emLocale')->flush();
            $pc = $entityManagerLocale->getRepository('KC\Entity\SpecialList')->find($id);
            $pc->position = $pos;
            \Zend_Registry::get('emLocale')->persist($pc);
            \Zend_Registry::get('emLocale')->flush();
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');
            return true;
        }
        return false;
    }
}
