<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dashboard")
 */
class Dashboard
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $no_of_offers;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $no_of_shops;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $no_of_clickouts;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $no_of_subscribers;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $total_no_of_offers;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $total_no_of_shops;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $total_no_of_shops_online_code;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $total_no_of_shops_online_code_lastweek;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $total_no_members;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $total_no_of_shops_online_code_thisweek;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    
    public static function amountOfOffersCreatedLastWeek()
    {
        return Offer::getAmountOffersCreatedLastWeek();

    }

    public static function totalAmountOfOffers()
    {
        return Offer::getTotalAmountOfOffers();

    }

    public function amountOfShopsCreatedLastWeek()
    {
        return Shop::getAmountShopsCreatedLastWeek();

    }

    public function totalAmountOfShops()
    {
        return Shop::getTotalAmountOfShops();

    }

    public function amountOfClickoutsLastWeek()
    {
        return ViewCount::getAmountClickoutsLastWeek();

    }

    public function amountOfSubscribersLastWeek()
    {
        return Visitor::getAmountSubscribersLastWeek();

    }

    public function totalAmountOfSubscribers()
    {
        return Visitor::getTotalAmountSubscribers();

    }

    public function totalAmountOfShopsCodeOnline()
    {
        return Shop::getTotalAmountOfShopsCodeOnline();

    }

    public function totalAmountOfShopsCodeOnlineThisWeek()
    {
        return Shop::getTotalAmountOfShopsCodeOnlineThisWeek();

    }

    public function totalAmountOfShopsCodeOnlineLastWeek()
    {
        return Shop::getTotalAmountOfShopsCodeOnlineLastWeek();

    }

    public static function getDashboardToDisplay()
    {
        $getData = Doctrine_Query::create()->from('Dashboard')->fetchOne(null,Doctrine::HYDRATE_ARRAY);
        return $getData;

    }

    public static function getDashboardValueToDispaly($name)
    {
        try {
            $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $entityManagerLocale->select("ds.$name")
                ->from('KC\Entity\Dashboard', 'ds');
            $dashboardResult = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $dashboardResult[0][$name];
        } catch (Exception $e) {
            $dashboardResult = false;
        }
        return $dashboardResult;
    }

    public static function updateDashboard($noOfOffers, $noOfShops, $noOfClickouts, $noOfSubscribers, $totNoOfOffers, $totNoOfShops, $totNoOfshopsCodeOnline, $totNoOfshopsCodeOnlineThisWeek, $totNoOfshopsCodeOnlineLastWeek, $totNoOfSubscribers)
    {
        $checkData = Doctrine_Query::CREATE()->from('Dashboard')->fetchArray();
        if(empty($checkData)){
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
            ->execute();

        }

    }

    public static function count_format($n, $point='.', $sep=',')
    {
        if ($n < 0) {
            return 0;
        }

        if ($n < 1000) {
            return number_format($n, 0, $point, $sep);
        }

        $d = $n < 1000000 ? 1000 : 1000000;

        $f = round($n / $d, 1);

        return number_format($f, $f - intval($f) ? 1 : 0, $point, $sep) . ($d == 1000 ? '<sub>K</sub>' : 'M');
    }
}