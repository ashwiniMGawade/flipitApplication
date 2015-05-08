<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="offer",
 *     indexes={
 *         @ORM\Index(name="shopid_idx", columns={"shopId"}),
 *         @ORM\Index(name="ind_offer_shenex", columns={"shopId","endDate","exclusiveCode"})
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
    private $Visability;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discountType;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $couponCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $refOfferUrl;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $refURL;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $exclusiveCode;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $editorPicks;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $extendedOffer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extendedTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extendedoffertitle;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $offerUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extendedUrl;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $extendedMetaDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $extendedFullDescription;

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
    private $shopExist;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $totalViewcount;

    /**
     * @ORM\Column(type="decimal", length=16, nullable=true, scale=4)
     */
    private $popularityCount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $couponCodeType;

    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Logo", inversedBy="offer")
     * @ORM\JoinColumn(name="offerLogoId", referencedColumnName="id", unique=true)
     */
    private $logo;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Conversions", mappedBy="offer")
     */
    private $conversions;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\CouponCode", mappedBy="offer")
     */
    private $couponcode;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\OfferTiles", mappedBy="offer")
     * @ORM\JoinColumn(name="tilesId", referencedColumnName="id", onDelete="restrict")
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
     * @ORM\OneToMany(targetEntity="KC\Entity\RefOfferCategory", mappedBy="offers")
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

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\FavoriteOffer", mappedBy="offer")
     */
    private $favoriteOffer;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\SpecialPagesOffers", mappedBy="offers")
     */
    private $specialPagesOffers;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\CategoriesOffers", mappedBy="offers")
     */
    private $categoriesOffers;

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
