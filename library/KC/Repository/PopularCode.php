<?php

namespace KC\Repository;

class PopularCode extends \KC\Entity\PopularCode
{
    #################################################
    ###### REFACTED CODE ############################
    #################################################
    public static function generatePopularCode($flagForCache)
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $past4Days = date($format, strtotime('-4 day' . $date));
        $nowDate = $date;

        $newPopularCodes = self::newPopularCode($nowDate, $past4Days, $date);
        self::deleteExpiredPopularCode($date, $flagForCache);

        $allExistingPopularCodes =  self::getAllExistingPopularCode();

        $manullyAddedCodes = self::getOldManuallyAddedPopularCode($allExistingPopularCodes);

        $lengthOfNewPopularCode = count($newPopularCodes);
        $lengthOfOldMainPopularCode = count($manullyAddedCodes);
        $totalPopupLength = $lengthOfNewPopularCode + $lengthOfOldMainPopularCode;

        $newArray = self::mergeNewAndOldPopularCode($totalPopupLength, $manullyAddedCodes, $newPopularCodes);
        self::changePositionPopularCode($newArray, $flagForCache);

        self::getAllPopularCodeByOrder();
        if ($flagForCache==true) {
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcode_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
        }
        return true;
    }

    public static function newPopularCode($nowDate, $past4Days, $date)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
        ->select('v.id, (sum(v.onClick) / (DATE_DIFF(CURRENT_TIMESTAMP(),o.startDate))) as pop, o.startDate')
        ->from('KC\Entity\ViewCount', 'v')
        ->leftJoin('v.viewcount', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->where('v.updated_at <='."'".$nowDate."'")
        ->andWhere('v.updated_at >='."'".$past4Days."'")
        ->andWhere('o.deleted = 0')
        ->andWhere('s.deleted = 0')
        ->andWhere('s.status = 1')
        ->andWhere('s.affliateProgram = 1')
        ->andWhere('o.offline = 0')
        ->andWhere('o.endDate >'."'".$date."'")
        ->andWhere('o.startDate <='."'".$date."'")
        ->setParameter(4, 'CD')
        ->andWhere('o.discountType = ?4')
        ->setParameter(5, 'MEM')
        ->andWhere('o.Visability != ?5')
        ->groupBy('v.viewcount')
        ->orderBy('pop', 'DESC')
        ->setMaxResults(10);
        $newPopularCodes = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $newPopularCodes;
    }

    public static function deleteExpiredPopularCode($date, $flagForCache)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
        ->select('offer.id as popularcode, p.position')
        ->from('KC\Entity\PopularCode', 'p')
        ->leftJoin('p.popularcode offer');
        $popIds = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        foreach($popIds as $popId):
            $query = $entityManagerLocale
            ->select('o.id')
            ->from('KC\Entity\Offer', 'o')
            ->where('o.id ='.$popId['popularcode'])
            ->andWhere('o.endDate <'."'".$date."'");
            $popIdsToDelete = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if($popIdsToDelete):
                self::deletePopular($popId['popularcode'], $popId['position'], $flagForCache);
            endif;
        endforeach;

        return true;
    }

    public static function getAllExistingPopularCode()
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
        ->select('p.id,o.id as offerId,p.type,p.position')
        ->from('KC\Entity\PopularCode', 'p')
        ->leftJoin('p.popularcode', 'o')
        ->orderBy('p.position');
        $allExistingPopularCodes = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $allExistingPopularCodes;
    }

    public static function getOldManuallyAddedPopularCode($allExistingPopularCodes)
    {
        $manullyAddedCodes = array();
        foreach ($allExistingPopularCodes as $popular) {
            if ($popular['type'] == 'MN') {
                $manullyAddedCodes[] = $popular;
            }
        }

        return $manullyAddedCodes;
    }

    public static function mergeNewAndOldPopularCode($totalLength, $manullyAddedCodes, $newPopularCodes)
    {
        $length = 0;
        $lenOldMN = 0;
        $lenNewPop = 0;
        $newArray = array();
        $position = 1;
        while ($length < $totalLength) {
            if (!empty($manullyAddedCodes[$lenOldMN])) {
                if ($manullyAddedCodes[$lenOldMN]['position'] == $position) {
                    $Ar = array('type' => $manullyAddedCodes[$lenOldMN]['type'],
                            'popularcode' => $manullyAddedCodes[$lenOldMN]['offer']['id'],
                            'position' => $manullyAddedCodes[$lenOldMN]['position']);

                    $newArray[$manullyAddedCodes[$lenOldMN]['offer']['id']] = $Ar;
                    $lenOldMN++;
                    $position++;
                } elseif (!array_key_exists(@$newPopularCodes[$lenNewPop]['offerId'], $newArray)) {
                    $Ar = array('type' => 'AT', 'popularcode' => @$newPopularCodes[$lenNewPop]['offerId'],
                            'position' => $position);
                    @$newArray[$newPopularCodes[$lenNewPop]['offerId']] = $Ar;
                    $lenNewPop++;
                    $position++;
                } else {
                    $lenNewPop++;
                }
            } elseif (!array_key_exists($newPopularCodes[$lenNewPop]['offerId'], $newArray)) {
                $Ar = array('type' => 'AT', 'popularcode' => $newPopularCodes[$lenNewPop]['offerId'],
                        'position' => $position);
                $newArray[$newPopularCodes[$lenNewPop]['offerId']] = $Ar;
                $lenNewPop++;
                $position++;
            } else {
                $lenNewPop++;
            }
            $length++;
        }
        return $newArray;
    }

    public static function changePositionPopularCode($newArray, $flagForCache)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->delete('KC\Entity\PopularCode', 's')
            ->setParameter(1, 'AT')
            ->where('s.type = ?1')
            ->getQuery();
        $query->execute();
        foreach ($newArray as $p) {
            if ($p['type']!='MN' && $p['offerId']!='') {
                //save popular code in database if new
                $entityManagerLocale  = \Zend_Registry::get('emLocale');
                $pc = new \KC\Entity\PopularCode();
                $pc->type = $p['type'];
                $pc->popularcode = $entityManagerLocale->find('KC\Entity\Offer', $p['offerId']);
                $pc->position = $p['position'];
                $pc->deleted = 0;
                $pc->status = 1;
                $pc->created_at = new \DateTime('now');
                $pc->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($pc);
                $entityManagerLocale->flush();

                $offerID = $p['offerId'];
                $authorId = self::getAuthorId($offerID);

                $uid = $authorId[0]['authorId'];
                $popularcodekey ="all_". "popularcode".$uid ."_list";

                if ($flagForCache==true) {
                    $flag =  \FrontEnd_Helper_viewHelper::checkCacheStatusByKey($popularcodekey);
                    if ($flag) {

                    } else {
                        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($popularcodekey);
                    }
                }

            }
        }
        return true;
    }

    public static function getAllPopularCodeByOrder()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('p')
        ->from('KC\Entity\PopularCode', 'p')
        ->orderBy('p.position', 'ASC');
        $offers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        self::resetPositionOfAllCodes($offers);
        return true;
    }

    public static function resetPositionOfAllCodes($newOfferList)
    {
        $newPos = 1;
        foreach ($newOfferList as $newOffer) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $queryBuilder ->update('KC\Entity\PopularCode', 'pc')
            ->set('pc.position', $newPos)
            ->where('pc.id = '.$newOffer['id'])
            ->getQuery()->execute();
            $newPos++;
        }
        return true;
    }

    public static function deletePopularCodeAbove27()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->delete('KC\Entity\PopularCode', 'pc')
            ->where('pc.position > 27')
            ->getQuery();
        $query->execute();
    }
    ################################################################
    ########### END REFACTEDRED CODE ###############################
    ################################################################

    public static function searchTopTenOffer($keyword, $flag)
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('o.title as title')
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->where('o.deleted=0')
        ->andWhere('s.deleted = 0')
        ->andWhere('s.status = 1')
        ->andWhere('o.offline = 0')
        ->andWhere('o.endDate >'."'".$date."'")
        ->andWhere('o.startDate <='."'".$date."'")
        ->andWhere("o.title LIKE '$keyword%'")
        ->setParameter(4, 'CD')
        ->andWhere('o.discountType = ?4')
        ->setParameter(5, 'MEM')
        ->andWhere('o.Visability != ?5')
        ->andWhere('o.userGenerated = 0')
        ->setMaxResults(10);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function searchAllOffer($listOfPopularCode)
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('o.title as title, o.id as id, o.userGenerated')
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->where('o.deleted = 0')
        ->andWhere('s.deleted = 0')
        ->andWhere('o.offline = 0')
        ->andWhere('o.endDate >'."'".$date."'")
        ->andWhere('o.startDate <='."'".$date."'")
        ->setParameter(4, 'CD')
        ->andWhere('o.discountType = ?4')
        ->setParameter(5, 'MEM')
        ->andWhere('o.Visability != ?5')
        ->andWhere('o.userGenerated = 0');
        if (!empty($listOfPopularCode)) {
            $query = $query->setParameter(1, $listOfPopularCode)
                    ->andWhere($queryBuilder->expr()->notIn('o.id', '?1'));
        }
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getPopularCode($limit = 27)
    {
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p.id,o.title, p.type, p.position,o.id as offerId')
            ->from('KC\Entity\PopularCode', 'p')
            ->leftJoin('p.popularcode', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where('o.deleted = 0')
            ->andWhere("o.userGenerated = 0")
            ->andWhere('s.deleted = 0')
            ->andWhere('o.offline = 0')
            //->andWhere('o.endDate >'."'".$date."'")
            //->andWhere('o.startDate <='."'".$date."'")
            ->setMaxResults($limit)
            ->orderBy('p.position', 'ASC');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        return $data;
    }

    public static function gethomePopularvoucherCode($flag)
    {
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('o.title, o.id as offerId, o.couponCodeType,o.extendedOffer,o.startDate,o.endDate,o.extendedUrl,o.couponCode as couponcode ,o.exclusiveCode as exclusivecode ,o.discount,o.discountvalueType,s.name as shopName, s.id as shopId, s.permaLink,l.path,l.name, p.type, p.position')
        ->from('KC\Entity\PopularCode', 'p')
        ->leftJoin('p.popularcode', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'l')
        ->where('o.deleted = 0')
        ->andWhere(
            "(o.couponCodeType = 'UN' AND (SELECT count(cc.id)  FROM KC\Entity\CouponCode cc WHERE
            cc.offer = o.id and cc.status=1)  > 0) or o.couponCodeType = 'GN'"
        )
        ->andWhere('s.deleted = 0')
        ->andWhere('o.offline = 0')
        ->andWhere('s.status = 1')
        ->andWhere('o.endDate >'."'".$date."'")
        ->andWhere('o.startDate <='."'".$date."'")
        ->setParameter(4, 'CD')
        ->andWhere('o.discountType = ?4')
        ->setParameter(5, 'MEM')
        ->andWhere('o.Visability != ?5')
        ->andWhere('o.userGenerated = 0')
        ->orderBy('p.position', 'ASC')
        ->setMaxResults($flag);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function gethomePopularvoucherCodeForMarktplaatFeeds($flag, $id = array(6))
    {
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p.id,o.title,o.couponCodeType,term.content as terms,o.extendedOffer,o.endDate,o.extendedUrl,o.couponCode as couponcode ,o.exclusiveCode as exclusivecode,o.discount, o.discountvalueType,s.name,s.permaLink, s.id as shopId, l.path,l.name as logoName, p.type, p.position')
            ->from('KC\Entity\PopularCode', 'p')
            ->leftJoin('p.popularcode', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.logo', 'l')
            ->leftJoin('o.offertermandcondition', 'term')
            ->where('o.deleted =0')
            ->andWhere(
            "(o.couponCodeType = 'UN' AND (SELECT count(cc.id)  FROM KC\Entity\CouponCode cc WHERE
            cc.offer = o.id and cc.status=1)  > 0) or o.couponCodeType = 'GN'"
            )
            ->andWhere('s.deleted=0')
            ->setParameter(1, $id)
            ->andWhere($queryBuilder->expr()->notIn('s.id', '?1'))
            ->andWhere('o.offline = 0')
            ->andWhere('s.status = 1')
            ->andWhere('o.endDate >'."'".$date."'")
            ->andWhere('o.startDate <='."'".$date."'")
            ->setParameter(4, 'CD')
            ->andWhere('o.discountType = ?4')
            ->setParameter(5, 'MEM')
            ->andWhere('o.Visability != ?5')
            ->andWhere('o.userGenerated = 0')
            ->orderBy('p.position', 'ASC')
            ->setMaxResults($flag);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function addOfferInList($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('o')
        ->from('KC\Entity\Offer', 'o')
        ->where('o.id =' . $id)
        ->setMaxResults(1);
        $Offer = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $flag = '0';

        if (sizeof($Offer) > 0) {
            $queryBuilderOffer = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $queryBuilderOffer->update('KC\Entity\Offer', 'o')
            ->set('o.editorPicks', '1')
            ->where('o.id = '.$id)
            ->getQuery()->execute();

            //check offer exist or not
            $queryBuilderPopularCode = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderPopularCode
            ->select('pc')
            ->from('KC\Entity\PopularCode', 'pc')
            ->where('pc.popularcode =' . $id);
            $pc = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if (sizeof($pc) > 0) {
                $flag = '2';
            } else {
                $flag = '1';
                //find last postion  from database
                $queryBuilderPosition = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilderPosition
                ->select('p.position')
                ->from('KC\Entity\PopularCode', 'p')
                ->orderBy('p.position', 'DESC')
                ->setMaxResults(1);
                $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                if (sizeof($data) > 0) {
                    $NewPos = $data[0]['position'];
                } else {
                    $NewPos =  0 ;
                }
                $entityManagerLocale  = \Zend_Registry::get('emLocale');
                $pc = new \KC\Entity\PopularCode();
                $pc->type = 'MN';
                $pc->popularcode = $entityManagerLocale->find('KC\Entity\Offer', $id);
                $pc->position = (intval($NewPos) + 1);
                $pc->deleted = 0;
                $pc->status = 1;
                $pc->created_at = new \DateTime('now');
                $pc->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($pc);
                $entityManagerLocale->flush();
                
                $flag  = array('id'=>$pc->id,'type'=>'MN','popularcode'=>$id,'position'=>(intval($NewPos) + 1),'title'=>$Offer[0]['title']);
            }

        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShopsHome_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularOffersHome_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('shop_popularShopForWidget_list');
        return $flag;

    }

    public static function deletePapularCode($id, $position)
    {
        if ($id) {
            $queryBuilderPopularOffer = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderPopularOffer
            ->select('pcode')
            ->from('KC\Entity\PopularCode', 'pcode')
            ->where('pcode.id=' . $id)
            ->setMaxResults(1);
            $offerDetail = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            
            $queryBuilderOffer = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $queryBuilderOffer->update('KC\Entity\Offer', 'o')
            ->set('o.editorPicks', '0')
            ->where('o.id = '.$offerDetail[0]['offerId'])
            ->getQuery()->execute();

            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\PopularCode', 's')
            ->where('s.id ='.$id)
            ->getQuery();
            $query->execute();

            //change position by 1 of each below element
            $queryBuilder ->update('KC\Entity\PopularCode', 'pc')
            ->set('pc.position', 'pc.position -1')
            ->where('pc.position > '.$position)
            ->getQuery()->execute();

            // If any position is missing it fixes so that all positions must be there
            $query = $queryBuilder
                ->select('popularcode')
                ->from('KC\Entity\PopularCode', 'popularcode')
                ->orderBy('popularcode.position', 'ASC');
            $newOfferList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            $newPos = 1;
            foreach ($newOfferList as $newOffer) {
                $queryBuilder ->update('KC\Entity\PopularCode', 'popularCode')
                    ->set('popularCodeposition', $newPos)
                    ->where('popularCodeid ='.$newOffer['id'])
                    ->getQuery()->execute();
                $newPos++;
            }
            //call cache function
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcode_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularOffersHome_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShopsHome_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            $key = 'all_widget5_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'all_widget6_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('shop_popularShopForWidget_list');
            return true ;
        }
        return false ;
    }

    public static function deletePopular($id, $position, $flagForCache)
    {
        if ($id) {
            //delete popular code from list
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\PopularCode', 's')
            ->where('s.id ='.$id)
            ->getQuery();
            $query->execute();

            if ($flagForCache==true) {
                //change position by 1 of each below element
                $queryBuilder ->update('KC\Entity\PopularCode', 'pc')
                ->set('pc.position', 'pc.position -1')
                ->where('pc.position > '.$position)
                ->getQuery()->execute();

                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcode_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
                $key = 'all_widget5_list';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                $key = 'all_widget6_list';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('shop_popularShopForWidget_list');
            }
        }
    }

    public static function moveUp($currentCodeId, $currentPosition, $previousCodeId, $previousCodePosition)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $queryBuilder ->update('KC\Entity\PopularCode', 'pc')
                ->set('pc.position', $previousCodePosition)
                ->where('pc.id = '.$currentCodeId)
                ->getQuery()->execute();

        $nextCodePosition = (intval($previousCodePosition) + 1);

        $queryBuilderForNewPosition = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $queryBuilderForNewPosition ->update('KC\Entity\PopularCode', 'p')
                ->set('p.position', $nextCodePosition)
                ->where('p.id = '.$previousCodeId)
                ->getQuery()->execute();
      
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        return true ;
    }

    public static function moveDown($currentCodeId, $currentPosition, $nextCodeId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $nextCodePosition = (intval($currentPosition) + 1);
        $queryBuilder ->update('KC\Entity\PopularCode', 'p')
            ->set('p.position', $nextCodePosition)
            ->where('p.id ='. $currentCodeId)
            ->getQuery()->execute();

        $queryBuilderForNewPosition = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $queryBuilderForNewPosition ->update('KC\Entity\PopularCode', 'pc')
            ->set('pc.position', $currentPosition)
            ->where('pc.id ='.$nextCodeId)
            ->getQuery()->execute();

        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('shop_popularShopForWidget_list');
        return true ;
    }

    public static function getAuthorId($offerId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('o.authorId')
        ->from('KC\Entity\Offer', 'o')
        ->where('o.id ='.$offerId);
        $userId = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $userId;
    }

    public static function lockElement($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('pc')
        ->from('KC\Entity\PopularCode', 'pc')
        ->where('pc.popularcode = '.$id);
        $lockStatus = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (count($lockStatus) > 0) {

            if ($lockStatus[0]['type'] == 'AT') {
                $type = "MN";
            } else {
                $type = "AT";
            }

            $queryBuilder ->update('KC\Entity\PopularCode', 'p')
            ->set('p.type', $queryBuilder->expr()->literal($type))
            ->where('p.id ='. $lockStatus[0]['id'])
            ->getQuery()->execute();
            return true ;
        }
        return false ;
    }

    public static function savePopularOffersPosition($offerId)
    {
        if (!empty($offerId)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\PopularCode', 's')
            ->where('s.id > 0')
            ->getQuery();
            $query->execute();
            $offerId = explode(',', $offerId);
            $i = 1;
            foreach ($offerId as $offerIdValue) {
                $entityManagerLocale  = \Zend_Registry::get('emLocale');
                $popularCode = new \KC\Entity\PopularCode();
                $popularCode->popularcode = $entityManagerLocale->find('KC\Entity\Offer', $offerIdValue);
                $popularCode->position = $i;
                $popularCode->type = "MN";
                $popularCode->status = 1;
                $popularCode->deleted = 0;
                $popularCode->created_at = new \DateTime('now');
                $popularCode->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($popularCode);
                $entityManagerLocale->flush();
                $i++;
            }
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularOffersHome_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShopsHome_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('shop_popularShopForWidget_list');
    }

}
