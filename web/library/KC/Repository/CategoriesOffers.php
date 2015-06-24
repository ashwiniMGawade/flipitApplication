<?php
namespace KC\Repository;
class CategoriesOffers extends \Core\Domain\Entity\CategoriesOffers
{
    public static function getCategoryOffersByCategoryIdForFrontEnd($categoryId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDate = date("Y-m-d H:i");
        $query = $queryBuilder
            ->select('op, o, s, l')
            ->from('\Core\Domain\Entity\CategoriesOffers', 'op')
            ->leftJoin('op.offers', 'o')
            ->andWhere(
                "(o.couponCodeType = 'UN' AND (SELECT count(cc.id) FROM KC\Entity\CouponCode cc WHERE cc.offer = o.id and o.status=1)  > 0)
                or o.couponCodeType = 'GN'"
            )
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.logo', 'l')
            ->where('op.categories = '.$categoryId)
            ->andWhere('o.endDate >'.$queryBuilder->expr()->literal($currentDate))
            ->andWhere('o.startDate <='.$queryBuilder->expr()->literal($currentDate))
            ->andWhere('o.deleted = 0')
            ->andWhere('s.deleted = 0')
            ->andWhere('s.status = 1')
            ->andWhere($queryBuilder->expr()->neq('o.Visability', $queryBuilder->expr()->literal("MEM")))
            ->orderBy('op.position');
        $categoryOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return self::removeDuplicateOffers($categoryOffers);
    }

    public static function removeDuplicateOffers($categoryOffers)
    {
        $categoryOffersWithoutDuplication = array();
        if (count($categoryOffers) > 0) {
            $countOfCategoryOffers = count($categoryOffers);
            for ($offerIndex = 0; $offerIndex < $countOfCategoryOffers; $offerIndex++) {
                $categoryOffersWithoutDuplication[$offerIndex] = $categoryOffers[$offerIndex]['offers'];
            }
        }
        return $categoryOffersWithoutDuplication;
    }

    public static function getCategoryOffersById($categoryId)
    {
        $categoryOffers = array();
        if (isset($categoryId)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('o, p, s, l, c')
                ->from('\Core\Domain\Entity\CategoriesOffers', 'p')
                ->leftJoin('p.offers', 'o')
                ->leftJoin('o.shopOffers', 's')
                ->leftJoin('s.logo', 'l')
                ->leftJoin('p.categories', 'c')
                ->where('p.categories =' .$categoryId)
                ->orderBy('p.position');
            $categoryOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }

        return $categoryOffers;
    }

    public static function addOfferInList($offerId, $categoryId)
    {
        $offer = self::offerExistance($offerId);
        $offerResult = '0';
        if (sizeof($offer) > 0) {
            $categoryOffers = self::getCategroyOffers($offerId, $categoryId);
            if (!empty($categoryOffers)) {
                $offerResult = '2';
            } else {
                $offerResult = '1';
                $getCategoryOfferMaxPosition = self::getCategoryOfferMaxPosition($categoryId);
                if (!empty($getCategoryOfferMaxPosition)) {
                    $newPosition = $getCategoryOfferMaxPosition[0]['position'];
                } else {
                    $newPosition =  0 ;
                }
                $categoryOfferId = self::saveCategoryOffers($offerId, $categoryId, $newPosition);
                $offerResult  = array(
                    'id'=>$categoryOfferId,
                    'type'=>'MN',
                    'offerId'=>$offerId,
                    'position'=>(intval($newPosition) + 1),
                    'title'=>$offer['title']
                );
            }
        }
        self::clearCacheOfCategoryOffers($categoryId);
        return $offerResult;
    }

    public static function offerExistance($offerId)
    {
        $offer = array();
        if (!empty($offerId)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('o')
                ->from('\Core\Domain\Entity\Offer', 'o')
                ->where('o.id=' . $offerId)
                ->setMaxResults(1);
            $offer = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $offer;
    }

    public static function getCategroyOffers($offerId, $categoryId)
    {
        $categoryOffers = array();
        if (!empty($offerId) && !empty($categoryId)) {
            $specialPageOffersQueryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $specialPageOffersQueryBuilder
                ->select('sl')
                ->from('\Core\Domain\Entity\CategoriesOffers', 'sl')
                ->where('sl.offers =' . $offerId)
                ->andWhere('sl.categories =' .$categoryId);
            $categoryOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $categoryOffers;
    }

    public static function getCategoryOfferMaxPosition($categoryId)
    {
        if (!empty($categoryId)) {
            $categoryOffersPositionQueryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $categoryOffersPositionQueryBuilder
                ->select('p.position')
                ->from('\Core\Domain\Entity\CategoriesOffers', 'p')
                ->where('p.categories=' .$categoryId)
                ->orderBy('p.position', 'DESC')
                ->setMaxResults(1);
            $maxPosition = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $maxPosition;
    }

    public static function saveCategoryOffers($offerId, $categoryId, $newPosition)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $categoryOffer = new \KC\Entity\CategoriesOffers();
        $categoryOffer->offers = $entityManagerLocale->find('KC\Entity\Offer', $offerId);
        $categoryOffer->categories = $entityManagerLocale->find('KC\Entity\Category', $categoryId);
        $categoryOffer->position = (intval($newPosition) + 1);
        $categoryOffer->deleted = 0;
        $categoryOffer->created_at = new \DateTime('now');
        $categoryOffer->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($categoryOffer);
        $entityManagerLocale->flush();
        return $categoryOffer->id;
    }

    public static function deleteCategoryOffer($id)
    {
        if (!empty($id)) {
            $queryBuilderDelete = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderDelete
                ->delete('KC\Entity\CategoriesOffers', 'spl')
                ->where('spl.id ='.$id)
                ->getQuery();
            $query->execute();
        }
        return true;
    }

    public static function getNewOfferList($categoryId)
    {
        $newOffersList = array();
        if (!empty($categoryId)) {
            $queryBuilderSelect = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderSelect
                ->select('catoffer')
                ->from('\Core\Domain\Entity\CategoriesOffers', 'catoffer')
                ->where('catoffer.categories='. $categoryId)
                ->orderBy('catoffer.position', 'ASC');
            $newOffersList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $newOffersList;
    }

    public static function updateWithNewPosition($newPosition, $newOffer)
    {
        if (isset($newOffer['id'])) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->update('KC\Entity\CategoriesOffers', 'p')
                ->set('p.position', $newPosition)
                ->where('p.id = '.$newOffer['id'])
                ->getQuery();
            $query->execute();
        }
        return true;
    }

    public static function deleteCode($id, $position, $categoryId)
    {
        if ($id) {
            self::deleteCategoryOffer($id);
            $newOffersList = self::getNewOfferList($categoryId);
            $newPosition = 1;
            $queryBuilderSpecialPage = \Zend_Registry::get('emLocale')->createQueryBuilder();
            foreach ($newOffersList as $newOffer) {
                self::updateWithNewPosition($newPosition, $newOffer);
                $newPosition++;
            }
            self::clearCacheOfCategoryOffers($categoryId);
            return true;
        }
        return false;
    }

    public static function savePosition($offerIds, $categoryId)
    {
        if (!empty($categoryId)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->delete('KC\Entity\CategoriesOffers', 'spl')
                ->where('spl.categories ='.$categoryId)
                ->getQuery();
            $query->execute();
            $offerIds = explode(',', $offerIds);
            self::saveCategoryOfferData($offerIds, $categoryId);
        }
        self::clearCacheOfCategoryOffers($categoryId);
    }

    public static function saveCategoryOfferData($offerIds, $categoryId)
    {
        $i = 1;
        foreach ($offerIds as $offerId) {
            $entityManagerLocale  = \Zend_Registry::get('emLocale');
            $categoryOffer = new \KC\Entity\CategoriesOffers();
            $categoryOffer->offers = $entityManagerLocale->find('KC\Entity\Offer', $offerId);
            $categoryOffer->categories = $entityManagerLocale->find('KC\Entity\Category', $categoryId);
            $categoryOffer->position = $i;
            $categoryOffer->deleted = 0;
            $categoryOffer->created_at = new \DateTime('now');
            $categoryOffer->updated_at = new \DateTime('now');
            $entityManagerLocale->persist($categoryOffer);
            $entityManagerLocale->flush();
            $i++;
        }
        return true;
    }

    public static function deleteExpiredOffers()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p')
            ->from('\Core\Domain\Entity\CategoriesOffers', 'p')
            ->where('p.deleted = 0')
            ->orderBy('p.position ASC');
        $categoryOffersDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (!empty($categoryOffersDetails)) {
            foreach ($categoryOffersDetails as $categoryOfferDetail) {
                $expiredStatus = \KC\Repository\Offer::checkOfferExpired($categoryOfferDetail['offers']['id']);
                if ($expiredStatus) {
                    self::deleteCode(
                        $categoryOfferDetail['id'],
                        $categoryOfferDetail['position'],
                        $categoryOfferDetail['categories']['id']
                    );
                }
            }
        }
        return true;
    }

    public static function clearCacheOfCategoryOffers($id)
    {
        $key = 'home_category'.$id.'_offers';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesHome_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categories_count');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categories_list');
    }
}
