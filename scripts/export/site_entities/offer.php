<?php
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
class offer
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
     * @ORM\OneToMany(targetEntity="couponcode", mappedBy="offer")
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
     * @ORM\OneToMany(targetEntity="conversions", mappedBy="offer")
     */
    private $conversions;

    /**
     * @ORM\OneToMany(targetEntity="offer_tiles", mappedBy="offer")
     */
    private $offerTiles;

    /**
     * @ORM\OneToMany(targetEntity="popular_code", mappedBy="popularcode")
     */
    private $offer;

    /**
     * @ORM\OneToMany(targetEntity="popular_vouchercodes", mappedBy="offer")
     */
    private $popularVouchercodes;

    /**
     * @ORM\OneToMany(targetEntity="ref_offer_category", mappedBy="category")
     */
    private $categoryoffres;

    /**
     * @ORM\OneToMany(targetEntity="ref_offer_page", mappedBy="refoffers")
     */
    private $offers;

    /**
     * @ORM\OneToMany(targetEntity="term_and_condition", mappedBy="termandcondition")
     */
    private $offertermandcondition;

    /**
     * @ORM\OneToMany(targetEntity="view_count", mappedBy="viewcount")
     */
    private $offerviewcount;

    /**
     * @ORM\OneToMany(targetEntity="votes", mappedBy="offer")
     */
    private $votes;

    /**
     * @ORM\ManyToOne(targetEntity="image", inversedBy="logooffer")
     * @ORM\JoinColumn(name="offerlogoid", referencedColumnName="id", onDelete="restrict")
     */
    private $logooffer;

    /**
     * @ORM\ManyToOne(targetEntity="shop", inversedBy="offer")
     * @ORM\JoinColumn(name="shopid", referencedColumnName="id", onDelete="restrict")
     */
    private $shopOffers;

    /**
     * @ORM\ManyToMany(targetEntity="visitor", inversedBy="offer")
     * @ORM\JoinTable(
     *     name="favorite_offer",
     *     joinColumns={@ORM\JoinColumn(name="offerId", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="visitorId", referencedColumnName="id", nullable=false)}
     * )
     */
    private $visitors;
}