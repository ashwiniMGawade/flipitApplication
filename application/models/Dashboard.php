<?php
/**
 * Dashboard
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##RAMAN## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class Dashboard extends BaseDashboard
{
    /**
     * Get the number of offers created in last 7 days
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public static function amountOfOffersCreatedLastWeek()
    {
        return Offer::getAmountOffersCreatedLastWeek();

    }

    /**
     * Get the Total number of offers
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public static function totalAmountOfOffers()
    {
        return Offer::getTotalAmountOfOffers();

    }

    /**
     * Get the number of shops created in last 7 days
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public function amountOfShopsCreatedLastWeek()
    {
        return Shop::getAmountShopsCreatedLastWeek();

    }

    /**
     * Get the total number of shops
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public function totalAmountOfShops()
    {
        return Shop::getTotalAmountOfShops();

    }


    /**
     * Get the number of clickouts in last 7 days
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public function amountOfClickoutsLastWeek()
    {
        return ViewCount::getAmountClickoutsLastWeek();

    }

    /**
     * Get the number of Subscribers in last 7 days
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public function amountOfSubscribersLastWeek()
    {
        return Visitor::getAmountSubscribersLastWeek();

    }

    /**
     * Get the total number of Subscribers
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public function totalAmountOfSubscribers()
    {
        return Visitor::getTotalAmountSubscribers();

    }

    /**
     * Get the total number of shops with atleast one code online
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public function totalAmountOfShopsCodeOnline()
    {
        return Shop::getTotalAmountOfShopsCodeOnline();

    }

    /**
     * Get the total number of shops with atleast one code online for This week
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public function totalAmountOfShopsCodeOnlineThisWeek()
    {
        return Shop::getTotalAmountOfShopsCodeOnlineThisWeek();

    }

    /**
     * Get the total number of shops with atleast one code online for last week
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public function totalAmountOfShopsCodeOnlineLastWeek()
    {
        return Shop::getTotalAmountOfShopsCodeOnlineLastWeek();

    }

    /**
     * Get all data from dashboard table
     * @author Cbhopal
     * @return array
     * @version 1.0
     */

    public static function getDashboardToDisplay()
    {
        $getData = Doctrine_Query::create()->from('Dashboard')->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        return $getData;

    }

    public static function getMoneyShopRatio()
    {
        return Shop::moneyShopRatio();
    }


    /**
     * to get a particular dasbboard value by its name
     * @param string settings name
     * @return mixed setting value or false
     *
     * @example getDashboardValueToDispaly("total_no_of_offers") will return total amount of offers
     */

    public static function getDashboardValueToDispaly($name)
    {

        try {
            $data = Doctrine_Query::create()->select($name)->from('Dashboard')->fetchOne(null, Doctrine::HYDRATE_ARRAY);
            return $data[$name];

        } catch (Exception $e) {

            $data = false;

        }

        return $data;
    }



    /**
     * Update the dashboard table with latest data
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public static function updateDashboard($noOfOffers, $noOfShops, $noOfClickouts,
        $noOfSubscribers, $totNoOfOffers, $totNoOfShops, $totNoOfshopsCodeOnline,
        $totNoOfshopsCodeOnlineThisWeek, $totNoOfshopsCodeOnlineLastWeek,
        $totNoOfSubscribers, $moneyShopRatio
    )
    {
        $checkData = Doctrine_Query::CREATE()->from('Dashboard')->fetchArray();
        if (empty($checkData)) {
            $dashboardAdd = new Dashboard();
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
            $dashboardAdd->save();

        } else {
            $dashboardUpdate = Doctrine_Query::create()
            ->update('Dashboard')
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
            ->execute();

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
