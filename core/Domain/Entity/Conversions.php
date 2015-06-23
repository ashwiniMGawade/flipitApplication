<?php
namespace Core\Domain\Entity;
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
    protected $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $IP;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $subid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $utma;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $utmz;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $utmv;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $utmx;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $converted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Shop", inversedBy="conversions")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    protected $shop;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Visitor", inversedBy="conversions")
     * @ORM\JoinColumn(name="visitorId", referencedColumnName="id")
     */
    protected $visitor;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Offer", inversedBy="conversions")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
     */
    protected $offer;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}