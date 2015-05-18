<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="favorite_shop", indexes={@ORM\Index(name="fav_cascade", columns={"shop"})})
 */
class FavoriteShop
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $code_alert_send_date;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Shop", inversedBy="favoriteshops")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    private $shop;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Visitor", inversedBy="favoritevisitorshops")
     * @ORM\JoinColumn(name="visitorId", referencedColumnName="id")
     */
    private $visitor;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}