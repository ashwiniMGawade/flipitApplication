<?php
namespace Core\Domain\Entity;
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
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $message;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $no_of_offers;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $no_of_shops;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $no_of_clickouts;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $no_of_subscribers;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $total_no_of_offers;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $total_no_of_shops;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $total_no_of_shops_online_code;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $total_no_of_shops_online_code_lastweek;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $total_no_members;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    protected $total_no_of_shops_online_code_thisweek;

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getDashboardMoneyShopRatio()
    {
        return $this->money_shop_ratio;
    }

    /**
     * @param mixed $money_shop_ratio
     */
    public function setDashboardMoneyShopRatio($money_shop_ratio)
    {
        $this->money_shop_ratio = $money_shop_ratio;
    }

    /**
     * @return mixed
     */
    public function getNoOfClickouts()
    {
        return $this->no_of_clickouts;
    }

    /**
     * @param mixed $no_of_clickouts
     */
    public function setNoOfClickouts($no_of_clickouts)
    {
        $this->no_of_clickouts = $no_of_clickouts;
    }

    /**
     * @return mixed
     */
    public function getNoOfOffers()
    {
        return $this->no_of_offers;
    }

    /**
     * @param mixed $no_of_offers
     */
    public function setNoOfOffers($no_of_offers)
    {
        $this->no_of_offers = $no_of_offers;
    }

    /**
     * @return mixed
     */
    public function getNoOfShops()
    {
        return $this->no_of_shops;
    }

    /**
     * @param mixed $no_of_shops
     */
    public function setNoOfShops($no_of_shops)
    {
        $this->no_of_shops = $no_of_shops;
    }

    /**
     * @return mixed
     */
    public function getNoOfSubscribers()
    {
        return $this->no_of_subscribers;
    }

    /**
     * @param mixed $no_of_subscribers
     */
    public function setNoOfSubscribers($no_of_subscribers)
    {
        $this->no_of_subscribers = $no_of_subscribers;
    }

    /**
     * @return mixed
     */
    public function getTotalNoMembers()
    {
        return $this->total_no_members;
    }

    /**
     * @param mixed $total_no_members
     */
    public function setTotalNoMembers($total_no_members)
    {
        $this->total_no_members = $total_no_members;
    }

    /**
     * @return mixed
     */
    public function getTotalNoOfOffers()
    {
        return $this->total_no_of_offers;
    }

    /**
     * @param mixed $total_no_of_offers
     */
    public function setTotalNoOfOffers($total_no_of_offers)
    {
        $this->total_no_of_offers = $total_no_of_offers;
    }

    /**
     * @return mixed
     */
    public function getTotalNoOfShops()
    {
        return $this->total_no_of_shops;
    }

    /**
     * @param mixed $total_no_of_shops
     */
    public function setTotalNoOfShops($total_no_of_shops)
    {
        $this->total_no_of_shops = $total_no_of_shops;
    }

    /**
     * @return mixed
     */
    public function getTotalNoOfShopsOnlineCode()
    {
        return $this->total_no_of_shops_online_code;
    }

    /**
     * @param mixed $total_no_of_shops_online_code
     */
    public function setTotalNoOfShopsOnlineCode($total_no_of_shops_online_code)
    {
        $this->total_no_of_shops_online_code = $total_no_of_shops_online_code;
    }

    /**
     * @return mixed
     */
    public function getTotalNoOfShopsOnlineCodeLastweek()
    {
        return $this->total_no_of_shops_online_code_lastweek;
    }

    /**
     * @param mixed $total_no_of_shops_online_code_lastweek
     */
    public function setTotalNoOfShopsOnlineCodeLastweek($total_no_of_shops_online_code_lastweek)
    {
        $this->total_no_of_shops_online_code_lastweek = $total_no_of_shops_online_code_lastweek;
    }

    /**
     * @return mixed
     */
    public function getTotalNoOfShopsOnlineCodeThisweek()
    {
        return $this->total_no_of_shops_online_code_thisweek;
    }

    /**
     * @param mixed $total_no_of_shops_online_code_thisweek
     */
    public function setTotalNoOfShopsOnlineCodeThisweek($total_no_of_shops_online_code_thisweek)
    {
        $this->total_no_of_shops_online_code_thisweek = $total_no_of_shops_online_code_thisweek;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }
    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $money_shop_ratio;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}