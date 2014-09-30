<?php
namespace KC\Repository;

class SpecialList extends \KC\Entity\SpecialList
{
    ######################################################
    ################# REFACTORED CODE ####################
    ######################################################
    public static function getSpecialPages($limit = '')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDateAndTime = date('Y-m-d H:i:s');
        $query = $queryBuilder->select('sp,p,l')
            ->from('KC\Entity\SpecialList', 'sp')
            ->leftJoin('sp.page', 'p')
            ->leftJoin('p.logo', 'l')
            ->setParameter(1, '0')
            ->where('p.deleted = ?1')
            ->setParameter(2, '1')
            ->andWhere('p.publish = ?2');
            // $query->addSelect("(SELECT roc FROM KC\Entity\refOfferPage roc LEFT JOIN roc.Offer off LEFT JOIN off.shop s  WHERE roc.pageid = sp.specialpageId and off.deleted = 0 and s.deleted = 0 and off.enddate >'".$currentDateAndTime."' and off.startdate <= '".$currentDateAndTime."'  and off.discounttype='CD'  and off.Visability!='MEM') as totalCoupons");
            //->addSelect("(SELECT roc1 FROM KC\Entity\refOfferPage roc1 LEFT JOIN roc1.Offer off1 LEFT JOIN off1.shop s1  WHERE roc1.pageid = sp.specialpageId and off1.deleted = 0 and s1.deleted = 0 and off1.enddate >'".$currentDateAndTime."' and off1.startdate <= '".$currentDateAndTime."' and off1.Visability!='MEM') as totalOffers");
        if ($limit != '') {
            $query = $query->setMaxResults($limit);
        }

        $query = $query->orderBy('sp.position', 'ASC');
        $specialPages = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $queryBuilder1 = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query2 =  $queryBuilder1->select('roc')
            ->from('KC\Entity\refOfferPage', 'roc')
            ->leftJoin('roc.refoffers', 'off')
            ->leftJoin('off.shopOffers', 's');
        $query = $query2;
        return $specialPages;
    }
    ####################################################
    ############ END REFACTORED CODE ###################
    ####################################################

    /**
     * Search to five offer
     * @param string $keyword
     * @param boolean $flag
     * @version 1.0
     * @return array $data
     * @author Er.kundal
     */
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
            ->setParameter(1, '0')
            ->where('p.deleted = ?1')
            ->setParameter(2, 'offer')
            ->add('where', $queryBuilder->expr()->literal('p.pageType', '?2'))
            ->setParameter(3, $keyword.'%')
            ->add('where', $queryBuilder->expr()->like('p.pageTitle', '?3'))
            ->setParameter(4, $codevalues)
            ->add('where', $queryBuilder->expr()->notIn('p.id', '?4'))
            ->setMaxResults(10);
        $topTenOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $topTenOffers;
    }

    /**
     * get Special offer list from database
     * @author Er.kundal
     * @version 1.0
     * @return array $data
     */
    public static function getsplpage()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('sp.type,sp.position,sp.specialpageId,p.pageTitle as title')
            ->from('KC\Entity\SpecialList', 'sp')
            ->leftJoin('sp.page', 'p')
            ->orderBy('sp.position', 'ASC');
        $specialPage = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $specialPage;
    }
    /**
    * add offer in Special offer
    * @author Er.kundal
    * @version 1.0
    * @return integer $flag
    */
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
            //check offer exist or not
            $pc = $entityManager->getRepository('KC\Entity\SpecialList')
                ->findBy(array('specialpageId' => $page[0]['id']));

            if (sizeof($pc) > 0) {
            } else {
                $flag = '1';
                //find last postion  from database
                $query = $queryBuilder->select('p.position')
                            ->from('KC\Entity\SpecialList', 'p')
                            ->orderBy('p.position', 'DESC')
                            ->setMaxResults(1);
                $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                if (sizeof($data) > 0) {
                    $NewPos = $data[0]['position'];
                } else {
                    $NewPos = 1;
                }               //add new offer if not exist in datbase
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
        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');

        return $flag;

    }
    /**
    * delete Special offer
    * @param integer $id
    * @param integer $position
    * @author Er.kundal
    * @version 1.0
    */
    public static function deletePapularCode($id, $position)
    {
        if ($id) {
            $entityManagerLocale  =\Zend_Registry::get('emLocale');
            $repo = $entityManagerLocale->getRepository('KC\Entity\SpecialList');
            $pc = $repo->findOneBy(array('id' =>  $id));
            $entityManagerLocale->remove($pc);
            $entityManagerLocale->flush();
            $queryBuilder = $entityManagerLocale->createQueryBuilder();
            $query = $queryBuilder->update('KC\Entity\SpecialList', 'p')
                ->set('p.position', $queryBuilder->expr()->literal('p.position -1'))
                ->setParameter(1, $position)
                ->add('where', $queryBuilder->expr()->gt('p.position', '?1'))
                ->getQuery();
            $query->execute();
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');
            return true;
        }

        return false;
    }

    /**
    * move up Special offer from list
    * @param integer $id
    * @param integer $position
    * @author Er.kundal
    * @version 1.0
    */
    public static function moveUpSpecial($id, $position)
    {
        $pos = (intval($position) - 1);


        //find prev element from database based of current
        $PrevPc = Doctrine_Core::getTable('SpecialList')
        ->findBy('position', $pos)->toArray();
        //change position of prev element with current
        //$flag =  1;
        if (count($PrevPc) > 0) {

            //$flag =2;
            $changePrevPc = Doctrine_Core::getTable('SpecialList')
            ->find($PrevPc[0]['id']);
            $changePrevPc->position = $position;
            $changePrevPc->save();
            //change position of current element with postition + 1
            $pc = Doctrine_Core::getTable('SpecialList')->find($id);
            $pc->position = $pos;
            $pc->save();
            //call cache function
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');

            return true ;
        }

        return false ;
        //return $flag;
    }
        /**
        * move down Special offer from list
        * @param integer $id
        * @param integer $position
        * @author kraj
        * @version 1.0
        */
        public static function moveDownSpecial($id, $position)
        {
            $pos = (intval($position) + 1);
            //find next element from database based of current
            $PrevPc = Doctrine_Core::getTable('SpecialList')
            ->findBy('position', $pos)->toArray();
            //change position of next element with current
            if(count($PrevPc) > 0) {

                    $changePrevPc = Doctrine_Core::getTable('SpecialList')
                    ->find($PrevPc[0]['id']);
                    $changePrevPc->position = $position;
                    $changePrevPc->save();
                    //change position of current element with postition - 1
                    $pc = Doctrine_Core::getTable('SpecialList')->find($id);
                    $pc->position = $pos;
                    $pc->save();
                    //call cache function
                    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');

                    return true ;
            }
            return false ;
        }
}