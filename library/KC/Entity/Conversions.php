<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="conversions",
 *     indexes={
 *         @ORM\Index(name="offer_conversion_idx", columns={"converted","IP"}),
 *         @ORM\Index(name="shop_conversion_idx", columns={"converted","IP"})
 *     }
 * )
 */
class Conversions
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $IP;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $subid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $utma;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $utmz;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $utmv;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $utmx;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $converted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Shop", inversedBy="conversions")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    private $shop;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Visitor", inversedBy="conversions")
     * @ORM\JoinColumn(name="visitorId", referencedColumnName="id")
     */
    private $visitor;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Offer", inversedBy="conversions")
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