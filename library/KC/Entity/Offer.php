<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="offer",
 *     indexes={
 *         @ORM\Index(name="shopid_idx", columns={"shopId"}),
 *         @ORM\Index(name="ind_offer_shenex", columns={"shopId","endDate","exclusiveCode"}),
 *         @ORM\Index(name="tilesId", columns={"tilesId"})
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
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $Visability;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $discountType;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    protected $couponCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $refOfferUrl;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $refURL;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $endDate;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $exclusiveCode;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $editorPicks;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $extendedOffer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $extendedTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $extendedoffertitle;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $offerUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $nickname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $extendedUrl;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $extendedMetaDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $extendedFullDescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $discount;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $discountvalueType;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $authorId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $authorName;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $maxlimit;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    protected $maxcode;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $userGenerated;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $approved;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    protected $offline;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $tilesId;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $shopExist;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $totalViewcount;

    /**
     * @ORM\Column(type="decimal", length=16, nullable=true, scale=4)
     */
    protected $popularityCount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $couponCodeType = "GN";

    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Logo", inversedBy="offer")
     * @ORM\JoinColumn(name="offerLogoId", referencedColumnName="id", unique=true)
     */
    protected $logo;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Conversions", mappedBy="offer")
     */
    protected $conversions;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\CouponCode", mappedBy="offer")
     */
    protected $couponcode;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\OfferTiles", mappedBy="offer")
     * @ORM\JoinColumn(name="tilesId", referencedColumnName="id", onDelete="restrict")
     */
    protected $offerTiles;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\PopularCode", mappedBy="popularcode")
     */
    protected $offer;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\PopularVouchercodes", mappedBy="offer")
     */
    protected $popularVouchercodes;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefOfferCategory", mappedBy="offers")
     */
    protected $categoryoffres;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefOfferPage", mappedBy="refoffers")
     */
    protected $offers;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\TermAndCondition", mappedBy="termandcondition")
     */
    protected $offertermandcondition;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\ViewCount", mappedBy="viewcount")
     */
    protected $offerviewcount;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Votes", mappedBy="offer")
     */
    protected $votes;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Shop", inversedBy="offer")
     * @ORM\JoinColumn(name="shopid", referencedColumnName="id", onDelete="restrict")
     */
    protected $shopOffers;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Visitor", inversedBy="offer")
     * @ORM\JoinTable(
     *     name="favorite_offers",
     *     joinColumns={@ORM\JoinColumn(name="offerId", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="visitorId", referencedColumnName="id", nullable=false)}
     * )
     */
    protected $visitors;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\FavoriteOffer", mappedBy="offer")
     */
    protected $favoriteOffer;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\SpecialPagesOffers", mappedBy="offers")
     */
    protected $specialPagesOffers;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\CategoriesOffers", mappedBy="offers")
     */
    protected $categoriesOffers;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
    
    public function getId()
    {
        return $this->id;
    }
}
