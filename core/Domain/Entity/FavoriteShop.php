<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="favorite_shop")})
 */
class FavoriteShop
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $code_alert_send_date;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Shop", inversedBy="favoriteshops")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    protected $shop;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Visitor", inversedBy="favoritevisitorshops")
     * @ORM\JoinColumn(name="visitorId", referencedColumnName="id")
     */
    protected $visitor;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}