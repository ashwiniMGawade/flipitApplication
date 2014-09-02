<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="conversions",
 *     indexes={
 *         @ORM\Index(name="offer_conversion_idx", columns={"offerId","converted","IP"}),
 *         @ORM\Index(name="shop_conversion_idx", columns={"shopId","converted","IP"})
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
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $shopId;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $offerId;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $visitorId;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $converted;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
     */
    private $updated_at;
}