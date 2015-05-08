<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="favorite_offer")
 */
class FavoriteOffer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Visitor", inversedBy="favoriteOffer")
     * @ORM\JoinColumn(name="visitorId", referencedColumnName="id")
     */
    private $visitor;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Offer", inversedBy="favoriteOffer")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
     */
    private $offer;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}