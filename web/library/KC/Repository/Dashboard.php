<?php

namespace KC\Repository;

class Dashboard extends \Core\Domain\Entity\Dashboard
{

    public static function amountOfOffersCreatedLastWeek()
    {
        return \KC\Repository\Offer::getAmountOffersCreatedLastWeek();

    }

    public static function totalAmountOfOffers()
    {
        return \KC\Repository\Offer::getTotalAmountOfOffers();

    }

    public function amountOfShopsCreatedLastWeek()
    {
        return \KC\Repository\Shop::getAmountShopsCreatedLastWeek();

    }

    public function totalAmountOfShops()
    {
        return \KC\Repository\Shop::getTotalAmountOfShops();

    }

    public function amountOfClickoutsLastWeek()
    {
        return \KC\Repository\ViewCount::getAmountClickoutsLastWeek();

    }

    public function amountOfSubscribersLastWeek()
    {
        return \KC\Repository\Visitor::getAmountSubscribersLastWeek();

    }

    public function totalAmountOfSubscribers()
    {
        return \KC\Repository\Visitor::getTotalAmountSubscribers();

    }

    public function totalAmountOfShopsCodeOnline()
    {
        return \KC\Repository\Shop::getTotalAmountOfShopsCodeOnline();

    }

    public function totalAmountOfShopsCodeOnlineThisWeek()
    {
        return \KC\Repository\Shop::getTotalAmountOfShopsCodeOnlineThisWeek();

    }

    public function totalAmountOfShopsCodeOnlineLastWeek()
    {
        return \KC\Repository\Shop::getTotalAmountOfShopsCodeOnlineLastWeek();

    }

    public static function getDashboardToDisplay()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('d')
            ->from('\KC\Entity\Dashboard', 'd');
        $getData = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $getData;

    }

    public static function getMoneyShopRatio()
    {
        return Shop::moneyShopRatio();
    }

    public static function getDashboardValueToDispaly($name)
    {
        try {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->select('d.'.$name)
                ->from('\KC\Entity\Dashboard', 'd');
            $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $data[$name];

        } catch (Exception $e) {

            $data = false;

        }

        return $data;
    }

    public static function updateDashboard(
        $noOfOffers,
        $noOfShops,
        $noOfClickouts,
        $noOfSubscribers,
        $totNoOfOffers,
        $totNoOfShops,
        $totNoOfshopsCodeOnline,
        $totNoOfshopsCodeOnlineThisWeek,
        $totNoOfshopsCodeOnlineLastWeek,
        $totNoOfSubscribers,
        $moneyShopRatio
    ) {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('d')
            ->from('\KC\Entity\Dashboard', 'd');
        $checkData = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (empty($checkData)) {
            $dashboardAdd = new \KC\Entity\Dashboard();
            $dashboardAdd->id = 1;
            $dashboardAdd->no_of_offers = $noOfOffers;
            $dashboardAdd->no_of_shops = $noOfShops;
            $dashboardAdd->no_of_clickouts = $noOfClickouts;
            $dashboardAdd->no_of_subscribers = $noOfSubscribers;
            $dashboardAdd->total_no_of_offers = $totNoOfOffers;
            $dashboardAdd->total_no_of_shops = $totNoOfShops;
            $dashboardAdd->total_no_of_shops_online_code = $totNoOfshopsCodeOnline;
            $dashboardAdd->total_no_of_shops_online_code_lastweek = $totNoOfshopsCodeOnlineLastWeek;
            $dashboardAdd->total_no_of_shops_online_code_thisweek = $totNoOfshopsCodeOnlineThisWeek;
            $dashboardAdd->total_no_members = $totNoOfSubscribers;
            $dashboardAdd->updated_at = date('Y-m-d H:i:s');
            $dashboardAdd->money_shop_ratio = $moneyShopRatio;
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $entityManagerLocale->persist($dashboardAdd);
            $entityManagerLocale->flush();

        } else {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->update('\KC\Entity\Dashboard')
                ->set('no_of_offers', $noOfOffers)
                ->set('no_of_shops', $noOfShops)
                ->set('no_of_clickouts', $noOfClickouts)
                ->set('no_of_subscribers', $noOfSubscribers)
                ->set('total_no_of_offers', $totNoOfOffers)
                ->set('total_no_of_shops', $totNoOfShops)
                ->set('total_no_of_shops_online_code', $totNoOfshopsCodeOnline)
                ->set('total_no_of_shops_online_code_lastweek', $totNoOfshopsCodeOnlineLastWeek)
                ->set('total_no_of_shops_online_code_thisweek', $totNoOfshopsCodeOnlineThisWeek)
                ->set('total_no_members', $totNoOfSubscribers)
                ->set('updated_at', "'" . date('Y-m-d H:i:s') ."'")
                ->set('money_shop_ratio', $moneyShopRatio)
                ->getQuery();
            $query->execute();
        }
    }

    public static function count_format($n, $point = '.', $sep = ',')
    {
        if ($n < 0) {
            return 0;
        }
        if ($n < 1000) {
            return number_format($n, 0, $point, $sep);
        }
        $d = $n < 1000000 ? 1000 : 1000000;
        $f = round($n / $d, 1);
        if ($n > 100000) {
            return number_format($f) . ($d == 1000 ? '<sub>K</sub>' : 'M');
        }
        return number_format($f, $f - intval($f) ? 1 : 0, $point, $sep) . ($d == 1000 ? '<sub>K</sub>' : 'M');
    }
}
