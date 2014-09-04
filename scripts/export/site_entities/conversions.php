<?php
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
class conversions
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
     * @ORM\ManyToOne(targetEntity="shop", inversedBy="conversions")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    private $shop;

    /**
     * @ORM\ManyToOne(targetEntity="visitor", inversedBy="conversions")
     * @ORM\JoinColumn(name="visitorId", referencedColumnName="id")
     */
    private $visitor;

    /**
     * @ORM\ManyToOne(targetEntity="offer", inversedBy="conversions")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
     */
    private $offer;
}