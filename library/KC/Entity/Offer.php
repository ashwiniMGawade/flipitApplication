<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="offer",
 *     indexes={
 *         @ORM\Index(name="shopid_idx", columns={"shopid"}),
 *         @ORM\Index(name="ind_offer_shenex", columns={"shopid","enddate","exclusivecode"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="offerlogoid", columns={"offerlogoid"})}
 * )
 */
class Offer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $visability;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discounttype;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     * @ORM\OneToMany(targetEntity="KC\Entity\CouponCode", mappedBy="offer")
     */
    private $couponcode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $refOfferUrl;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $refurl;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startdate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $enddate;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $exclusivecode;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $editorpicks;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $extendedoffer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extendedtitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extendedurl;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $extendedmetadescription;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $extendedfulldescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $discount;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $discountvalueType;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $authorId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $authorName;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $maxlimit;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $maxcode;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $userGenerated;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $approved;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $offline;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $tilesId;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $shopexist;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $totalviewcount;

    /**
     * @ORM\Column(type="decimal", length=16, nullable=true, scale=4)
     */
    private $popularitycount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $couponcodetype;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Conversions", mappedBy="offer")
     */
    private $conversions;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\OfferTiles", mappedBy="offer")
     */
    private $offerTiles;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\PopularCode", mappedBy="popularcode")
     */
    private $offer;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\PopularVouchercodes", mappedBy="offer")
     */
    private $popularVouchercodes;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefOfferCategory", mappedBy="category")
     */
    private $categoryoffres;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefOfferPage", mappedBy="refoffers")
     */
    private $offers;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\TermAndCondition", mappedBy="termandcondition")
     */
    private $offertermandcondition;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\ViewCount", mappedBy="viewcount")
     */
    private $offerviewcount;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Votes", mappedBy="offer")
     */
    private $votes;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Image", inversedBy="logooffer")
     * @ORM\JoinColumn(name="offerlogoid", referencedColumnName="id", onDelete="restrict")
     */
    private $logooffer;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Shop", inversedBy="offer")
     * @ORM\JoinColumn(name="shopid", referencedColumnName="id", onDelete="restrict")
     */
    private $shopOffers;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Visitor", inversedBy="offer")
     * @ORM\JoinTable(
     *     name="favorite_offer",
     *     joinColumns={@ORM\JoinColumn(name="offerId", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="visitorId", referencedColumnName="id", nullable=false)}
     * )
     */
    private $visitors;
}