<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="offer_tiles")
 */
class OfferTiles
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $label;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $ext;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $position;

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
     * @ORM\ManyToOne(targetEntity="KC\Entity\Offer", inversedBy="offerTiles")
     */
    protected $offer;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $offerId;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}