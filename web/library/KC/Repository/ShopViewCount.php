<?php
namespace KC\Repository;

class ShopViewCount extends \Core\Domain\Entity\ShopViewCount
{
    ##########################################
    ########### REFACTORED CODE ##############
    ##########################################
    public static function getShopClick($shopId, $clientIp)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shopClick = $queryBuilder
            ->select('count(sv) as ShopViewCountExists')
            ->from('\Core\Domain\Entity\ShopViewCount', 'sv')
            ->where('sv.deleted=0')
            ->andWhere('sv.onclick!=0')
            ->andWhere('sv.shop='.$shopId)
            ->andWhere('sv.ip='.$clientIp)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopClick[0]['ShopViewCountExists'];
    }

    public static function getSaveShopClick($shopId, $clientIp)
    {
        $shopClick  = new \Core\Domain\Entity\ShopViewCount();
        $shopClick->shop = \Zend_Registry::get('emLocale')
            ->getRepository('\Core\Domain\Entity\Shop')
            ->find($shopId);
        $shopClick->onclick = 1;
        $shopClick->onload = 0;
        $shopClick->ip = $clientIp;
        $shopClick->deleted = 0;
        $shopClick->created_at = new \DateTime('now');
        $shopClick->updated_at = new \DateTime('now');
        \Zend_Registry::get('emLocale')->persist($shopClick);
        \Zend_Registry::get('emLocale')->flush();
        return true;
    }

    public static function getShopOnload($shopId, $clientIp)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shopOnload = $queryBuilder
            ->select('count(sv) as ShopViewCountExists')
            ->from('\Core\Domain\Entity\ShopViewCount', 'sv')
            ->where('sv.deleted=0')
            ->andWhere('sv.onload!=0')
            ->andWhere('sv.shop='.$shopId)
            ->andWhere('sv.ip='.$clientIp)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopOnload[0]['ShopViewCountExists'];
    }

    public static function getSaveShopOnload($shopId, $clientIp)
    {
        $shopOnLoad  = new \Core\Domain\Entity\ShopViewCount();
        $shopOnLoad->shop = \Zend_Registry::get('emLocale')
            ->getRepository('\Core\Domain\Entity\Shop')
            ->find($shopId);
        $shopOnLoad->onload = 1;
        $shopOnLoad->onclick = 0;
        $shopOnLoad->ip = $clientIp;
        $shopOnLoad->deleted = 0;
        $shopOnLoad->created_at = new \DateTime('now');
        $shopOnLoad->updated_at = new \DateTime('now');
        \Zend_Registry::get('emLocale')->persist($shopOnLoad);
        \Zend_Registry::get('emLocale')->flush();
        return true;
    }

    ##########################################
    ########### END REFACTORED CODE ##########
    ##########################################
    public static function getAmountClickoutOfShop($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $format = 'Y-m-j H:i:s';
        $date = date($format);

        $offers = \KC\Repository\Offer::getTotalAmountOfOffersByShopId($shopId);
        $lastWeekOfferClicks = 0;
        // - 7 days from today
        $past7Days = date($format, strtotime('-7 day' . $date));

        if (!empty($offers)) {
            $dataOffer = $queryBuilder
                ->select("count(v) as amountclicks")
                ->from('\Core\Domain\Entity\ViewCount', 'v')
                ->where($queryBuilder->expr()->in('v.viewcount', $offers[0]))
                ->andWhere($queryBuilder->expr()->eq('v.onClick', 1))
                ->andWhere(
                    $queryBuilder->expr()->between(
                        'v.created_at',
                        $queryBuilder->expr()->literal($past7Days),
                        $queryBuilder->expr()->literal($date)
                    )
                )
                ->getQuery()
                ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            if (!empty($dataOffer)) {
                $lastWeekOfferClicks = $dataOffer['amountclicks'];
            } else {
                $lastWeekOfferClicks = 0;
            }
        }

        $data = \Zend_Registry::get('emLocale')->createQueryBuilder()
            ->select("count(s) as amountclicks")
            ->from('\Core\Domain\Entity\ShopViewCount', 's')
            ->where('s.deleted = 0')
            ->where('s.shop = '.$shopId)
            ->andWhere(
                $queryBuilder->expr()->between(
                    's.created_at',
                    $queryBuilder->expr()->literal($past7Days),
                    $queryBuilder->expr()->literal($date)
                )
            )
            ->getQuery()
            ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if (!empty($data)) {
            $lastWeekShopClicks = $data['amountclicks'];
        } else {
            $lastWeekShopClicks = 0;
        }

        $lastWeekClicks = $lastWeekOfferClicks + $lastWeekShopClicks;
        return $lastWeekClicks;
    }

    public static function getTotalAmountClicksOfShop($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $offers = Offer::getTotalAmountOfOffersByShopId($shopId);

        $totalOfferClicks = 0;
        if (!empty($offers)) {
            $dataOffer = $queryBuilder
                ->select("count(v) as amountclicks")
                ->from("\Core\Domain\Entity\ViewCount", "v")
                ->where($queryBuilder->expr()->in('v.viewcount', $offers))
                ->andWhere($queryBuilder->expr()->eq('v.onClick', 1))
                ->getQuery()
                ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            if (!empty($dataOffer)) {

                $totalOfferClicks = $dataOffer['amountclicks'];
            } else {
                $totalOfferClicks = 0;
            }
        }
        
        $data = \Zend_Registry::get('emLocale')->createQueryBuilder()
            ->select("count(s) as counts")
            ->from('\Core\Domain\Entity\ShopViewCount', 's')
            ->where('s.deleted = 0')
            ->andWhere('s.shop = '.$shopId)
            ->getQuery()
            ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $totalShopClicks = $data['counts'];
        $totalClicks = $totalOfferClicks + $totalShopClicks;
        return $totalClicks;
    }

    public static function getTotalViewCountOfShopAndOffer($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $allOffers = $queryBuilder
            ->select('o.id,o.totalViewcount as clicks')
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->where('o.deleted = 0')
            ->andWhere($queryBuilder->expr()->eq('o.shopOffers', $queryBuilder->expr()->literal($shopId)))
            ->andWhere($queryBuilder->expr()->gt('o.endDate', $queryBuilder->expr()->literal($date)))
            ->andWhere($queryBuilder->expr()->lte('o.startDate', $queryBuilder->expr()->literal($date)))
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $totalOfferClicks = self::tranverseAllOffer($allOffers);
        $totalShopClicks = self::getShopClicks($shopId);
        $totalClicks =  $totalOfferClicks + $totalShopClicks;
        return $totalClicks;
    }

    public static function tranverseAllOffer($allOffers)
    {
        $totalOfferClicks = 0;
        if (!empty($allOffers)) :
            foreach ($allOffers as $arr) :
                $totalOfferClicks = $totalOfferClicks + $arr['clicks'];
            endforeach;
        endif;
        return $totalOfferClicks;
    }

    public static function getShopClicks($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder
           ->select('s.totalviewcount as clicks')
           ->from('\Core\Domain\Entity\Shop', 's')
           ->where('s.deleted = 0')
           ->andWhere('s.id = '.$shopId)
           ->getQuery()
           ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $totalShopClicks = $data['clicks'];
        return $totalShopClicks;
    }
}
