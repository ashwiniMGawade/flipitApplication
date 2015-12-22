<?php
namespace Core\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $tilesId;
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
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $shopExist;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $totalViewcount;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $popularityCount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $couponCodeType = "GN";

    /**
     * @ORM\OneToOne(targetEntity="Core\Domain\Entity\Logo", inversedBy="offer")
     * @ORM\JoinColumn(name="offerlogoid", referencedColumnName="id", unique=true)
     */
    protected $logo;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Conversions", mappedBy="offer")
     */
    protected $conversions;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\CouponCode", mappedBy="offer")
     */
    protected $couponcode;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\OfferTiles", mappedBy="offer")
     */
    protected $offerTiles;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\PopularCode", mappedBy="popularcode")
     */
    protected $offer;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\PopularVouchercodes", mappedBy="offer")
     */
    protected $popularVouchercodes;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\RefOfferCategory", mappedBy="offers")
     */
    protected $categoryoffres;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\RefOfferPage", mappedBy="refoffers")
     */
    protected $offers;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\TermAndCondition", mappedBy="termandcondition")
     */
    protected $offertermandcondition;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\ViewCount", mappedBy="viewcount")
     */
    protected $offerviewcount;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Votes", mappedBy="offer")
     */
    protected $votes;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\Shop", inversedBy="offer")
     * @ORM\JoinColumn(name="shopid", referencedColumnName="id", onDelete="restrict")
     */
    protected $shopOffers;

    /**
     * @ORM\ManyToMany(targetEntity="Core\Domain\Entity\Visitor", inversedBy="offer")
     * @ORM\JoinTable(
     *     name="favorite_offer",
     *     joinColumns={@ORM\JoinColumn(name="offerId", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="visitorId", referencedColumnName="id", nullable=false)}
     * )
     */
    protected $visitors;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\FavoriteOffer", mappedBy="offer")
     */
    protected $favoriteOffer;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\SpecialPagesOffers", mappedBy="offers")
     */
    protected $specialPagesOffers;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\CategoriesOffers", mappedBy="offers")
     */
    protected $categoriesOffers;

    /**
     * @ORM\OneToOne(targetEntity="Core\Domain\Entity\NewsletterCampaignOffer", mappedBy="offer")
     */
    protected $campaignOffer;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $offer_position;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * @return mixed
     */
    public function getVisability()
    {
        return $this->Visability;
    }

    /**
     * @param mixed $Visability
     */
    public function setVisability($Visability)
    {
        $this->Visability = $Visability;
    }

    /**
     * @return mixed
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * @param mixed $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * @return mixed
     */
    public function getOfferAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @param mixed $authorId
     */
    public function setOfferAuthorId($authorId)
    {
        $this->authorId = $authorId;
    }

    /**
     * @return mixed
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @param mixed $authorName
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
    }

    /**
     * @return mixed
     */
    public function getCategoriesOffers()
    {
        return $this->categoriesOffers;
    }

    /**
     * @param mixed $categoriesOffers
     */
    public function setCategoriesOffers($categoriesOffers)
    {
        $this->categoriesOffers = $categoriesOffers;
    }

    /**
     * @return mixed
     */
    public function getCategoryoffres()
    {
        return $this->categoryoffres;
    }

    /**
     * @param mixed $categoryoffres
     */
    public function setCategoryoffres($categoryoffres)
    {
        $this->categoryoffres = $categoryoffres;
    }

    /**
     * @return mixed
     */
    public function getConversions()
    {
        return $this->conversions;
    }

    /**
     * @param mixed $conversions
     */
    public function setConversions($conversions)
    {
        $this->conversions = $conversions;
    }

    /**
     * @return mixed
     */
    public function getCouponCode()
    {
        return $this->couponCode;
    }

    /**
     * @param mixed $couponCode
     */
    public function setCouponCode($couponCode)
    {
        $this->couponCode = $couponCode;
    }

    /**
     * @return mixed
     */
    public function getCouponCodeType()
    {
        return $this->couponCodeType;
    }

    /**
     * @param mixed $couponCodeType
     */
    public function setCouponCodeType($couponCodeType)
    {
        $this->couponCodeType = $couponCodeType;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param mixed $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * @return mixed
     */
    public function getDiscountType()
    {
        return $this->discountType;
    }

    /**
     * @param mixed $discountType
     */
    public function setDiscountType($discountType)
    {
        $this->discountType = $discountType;
    }

    /**
     * @return mixed
     */
    public function getDiscountvalueType()
    {
        return $this->discountvalueType;
    }

    /**
     * @param mixed $discountvalueType
     */
    public function setDiscountvalueType($discountvalueType)
    {
        $this->discountvalueType = $discountvalueType;
    }

    /**
     * @return mixed
     */
    public function getEditorPicks()
    {
        return $this->editorPicks;
    }

    /**
     * @param mixed $editorPicks
     */
    public function setEditorPicks($editorPicks)
    {
        $this->editorPicks = $editorPicks;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getExclusiveCode()
    {
        return $this->exclusiveCode;
    }

    /**
     * @param mixed $exclusiveCode
     */
    public function setExclusiveCode($exclusiveCode)
    {
        $this->exclusiveCode = $exclusiveCode;
    }

    /**
     * @return mixed
     */
    public function getExtendedFullDescription()
    {
        return $this->extendedFullDescription;
    }

    /**
     * @param mixed $extendedFullDescription
     */
    public function setExtendedFullDescription($extendedFullDescription)
    {
        $this->extendedFullDescription = $extendedFullDescription;
    }

    /**
     * @return mixed
     */
    public function getExtendedMetaDescription()
    {
        return $this->extendedMetaDescription;
    }

    /**
     * @param mixed $extendedMetaDescription
     */
    public function setExtendedMetaDescription($extendedMetaDescription)
    {
        $this->extendedMetaDescription = $extendedMetaDescription;
    }

    /**
     * @return mixed
     */
    public function getExtendedOffer()
    {
        return $this->extendedOffer;
    }

    /**
     * @param mixed $extendedOffer
     */
    public function setExtendedOffer($extendedOffer)
    {
        $this->extendedOffer = $extendedOffer;
    }

    /**
     * @return mixed
     */
    public function getExtendedTitle()
    {
        return $this->extendedTitle;
    }

    /**
     * @param mixed $extendedTitle
     */
    public function setExtendedTitle($extendedTitle)
    {
        $this->extendedTitle = $extendedTitle;
    }

    /**
     * @return mixed
     */
    public function getOfferExtendedUrl()
    {
        return $this->extendedUrl;
    }

    /**
     * @param mixed $extendedUrl
     */
    public function setOfferExtendedUrl($extendedUrl)
    {
        $this->extendedUrl = $extendedUrl;
    }

    /**
     * @return mixed
     */
    public function getExtendedoffertitle()
    {
        return $this->extendedoffertitle;
    }

    /**
     * @param mixed $extendedoffertitle
     */
    public function setExtendedoffertitle($extendedoffertitle)
    {
        $this->extendedoffertitle = $extendedoffertitle;
    }

    /**
     * @return mixed
     */
    public function getFavoriteOffer()
    {
        return $this->favoriteOffer;
    }

    /**
     * @param mixed $favoriteOffer
     */
    public function setFavoriteOffer($favoriteOffer)
    {
        $this->favoriteOffer = $favoriteOffer;
    }

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return mixed
     */
    public function getMaxcode()
    {
        return $this->maxcode;
    }

    /**
     * @param mixed $maxcode
     */
    public function setMaxcode($maxcode)
    {
        $this->maxcode = $maxcode;
    }

    /**
     * @return mixed
     */
    public function getMaxlimit()
    {
        return $this->maxlimit;
    }

    /**
     * @param mixed $maxlimit
     */
    public function setMaxlimit($maxlimit)
    {
        $this->maxlimit = $maxlimit;
    }

    /**
     * @return mixed
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param mixed $nickname
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @return mixed
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * @param mixed $offer
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;
    }

    /**
     * @return mixed
     */
    public function getOfferTiles()
    {
        return $this->offerTiles;
    }

    /**
     * @param mixed $offerTiles
     */
    public function setOfferTiles($offerTiles)
    {
        $this->offerTiles = $offerTiles;
    }

    /**
     * @return mixed
     */
    public function getOfferUrl()
    {
        return $this->offerUrl;
    }

    /**
     * @param mixed $offerUrl
     */
    public function setOfferUrl($offerUrl)
    {
        $this->offerUrl = $offerUrl;
    }

    /**
     * @return mixed
     */
    public function getOfferPosition()
    {
        return $this->offer_position;
    }

    /**
     * @param mixed $offer_position
     */
    public function setOfferPosition($offer_position)
    {
        $this->offer_position = $offer_position;
    }

    /**
     * @return mixed
     */
    public function getRefOffers()
    {
        return $this->offers;
    }

    /**
     * @param mixed $offers
     */
    public function setRefOffers($offers)
    {
        $this->offers = $offers;
    }

    /**
     * @return mixed
     */
    public function getOffertermandcondition()
    {
        return $this->offertermandcondition;
    }

    /**
     * @param mixed $offertermandcondition
     */
    public function setOffertermandcondition($offertermandcondition)
    {
        $this->offertermandcondition = $offertermandcondition;
    }

    /**
     * @return mixed
     */
    public function getOfferviewcount()
    {
        return $this->offerviewcount;
    }

    /**
     * @param mixed $offerviewcount
     */
    public function setOfferviewcount($offerviewcount)
    {
        $this->offerviewcount = $offerviewcount;
    }

    /**
     * @return mixed
     */
    public function getOffline()
    {
        return $this->offline;
    }

    /**
     * @param mixed $offline
     */
    public function setOffline($offline)
    {
        $this->offline = $offline;
    }

    /**
     * @return mixed
     */
    public function getPopularVouchercodes()
    {
        return $this->popularVouchercodes;
    }

    /**
     * @param mixed $popularVouchercodes
     */
    public function setPopularVouchercodes($popularVouchercodes)
    {
        $this->popularVouchercodes = $popularVouchercodes;
    }

    /**
     * @return mixed
     */
    public function getPopularityCount()
    {
        return $this->popularityCount;
    }

    /**
     * @param mixed $popularityCount
     */
    public function setPopularityCount($popularityCount)
    {
        $this->popularityCount = $popularityCount;
    }

    /**
     * @return mixed
     */
    public function getRefOfferUrl()
    {
        return $this->refOfferUrl;
    }

    /**
     * @param mixed $refOfferUrl
     */
    public function setRefOfferUrl($refOfferUrl)
    {
        $this->refOfferUrl = $refOfferUrl;
    }

    /**
     * @return mixed
     */
    public function getRefURL()
    {
        return $this->refURL;
    }

    /**
     * @param mixed $refURL
     */
    public function setRefURL($refURL)
    {
        $this->refURL = $refURL;
    }

    /**
     * @return mixed
     */
    public function getShopExist()
    {
        return $this->shopExist;
    }

    /**
     * @param mixed $shopExist
     */
    public function setShopExist($shopExist)
    {
        $this->shopExist = $shopExist;
    }

    /**
     * @return mixed
     */
    public function getShopOffers()
    {
        return $this->shopOffers;
    }

    /**
     * @param mixed $shopOffers
     */
    public function setShopOffers($shopOffers)
    {
        $this->shopOffers = $shopOffers;
    }

    /**
     * @return mixed
     */
    public function getSpecialPagesOffers()
    {
        return $this->specialPagesOffers;
    }

    /**
     * @param mixed $specialPagesOffers
     */
    public function setSpecialPagesOffers($specialPagesOffers)
    {
        $this->specialPagesOffers = $specialPagesOffers;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getTilesId()
    {
        return $this->tilesId;
    }

    /**
     * @param mixed $tilesId
     */
    public function setTilesId($tilesId)
    {
        $this->tilesId = $tilesId;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTotalViewcount()
    {
        return $this->totalViewcount;
    }

    /**
     * @param mixed $totalViewcount
     */
    public function setTotalViewcount($totalViewcount)
    {
        $this->totalViewcount = $totalViewcount;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return mixed
     */
    public function getUserGenerated()
    {
        return $this->userGenerated;
    }

    /**
     * @param mixed $userGenerated
     */
    public function setUserGenerated($userGenerated)
    {
        $this->userGenerated = $userGenerated;
    }

    /**
     * @return mixed
     */
    public function getVisitors()
    {
        return $this->visitors;
    }

    /**
     * @param mixed $visitors
     */
    public function setVisitors($visitors)
    {
        $this->visitors = $visitors;
    }

    /**
     * @return mixed
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param mixed $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }
    
    public function getId()
    {
        return $this->id;
    }
}
