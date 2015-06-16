<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="shop",
 *     indexes={
 *         @ORM\Index(name="affliatenetworkid_idx", columns={"affliateNetworkId"}),
 *         @ORM\Index(name="howtousepageid_idx", columns={"howtoUsepageId"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="logoid", columns={"logoId"}),
 *         @ORM\UniqueConstraint(name="howtousesmallimageid", columns={"howtoUseSmallImageId"}),
 *         @ORM\UniqueConstraint(name="howtousebigimageid", columns={"howtoUseBigImageId"})
 *     }
 * )
 */
class Shop
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
    protected $name;

    /**
     * @ORM\Column(type="string", unique=true, length=255, nullable=true)
     */
    protected $permaLink;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaDescription;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $usergenratedcontent;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $notes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $deepLink;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $deepLinkStatus;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $refUrl;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $actualUrl;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $affliateProgram;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $subTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $overriteTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $overriteSubtitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $overriteBrowserTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $shopText;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $views;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $howToUse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $Deliverytime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $returnPolicy;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $freeDelivery;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $deliveryCost;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $offlineSicne;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $accoutManagerId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $accountManagerName;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $contentManagerId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $contentManagerName;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $screenshotId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $keywordlink;

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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $howtoTitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $howtoSubtitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $howtoMetaTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $howtoMetaDescription;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $ideal;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $qShops;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $freeReturns;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $pickupPoints;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $mobileShop;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $service;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $serviceNumber;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $discussions;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $displayExtraProperties;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $showSignupOption;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $addtosearch;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $customHeader;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $totalviewcount;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $showSimliarShops;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $showChains;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $chainItemId;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $chainId;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $strictConfirmation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $howToIntroductionText;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $brandingcss;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lightboxsecondtext;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lightboxfirsttext;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $howtoguideslug;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $code_alert_send_date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $featuredtext;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $featuredtextdate;

    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Logo", inversedBy="shop")
     * @ORM\JoinColumn(name="logoId", referencedColumnName="id", unique=true)
     */
    protected $logo;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Adminfavoriteshp", mappedBy="shops")
     */
    protected $adminfevoriteshops;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Conversions", mappedBy="shop")
     */
    protected $conversions;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Offer", mappedBy="shopOffers")
     */
    protected $offer;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\OfferNews", mappedBy="shop")
     */
    protected $offerNews;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\PopularShop", mappedBy="popularshops")
     */
    protected $popularshop;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefArticleStore", mappedBy="articleshops")
     */
    protected $articlestore;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefExcludedkeywordShop", mappedBy="keywords")
     */
    protected $shopsofKeyword;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefShopCategory", mappedBy="category")
     */
    protected $categoryshops;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefShopRelatedshop", mappedBy="shop")
     */
    protected $relatedshops;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\ShopHowToChapter", mappedBy="shop")
     */
    protected $howtochapter;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\ShopViewCount", mappedBy="shop")
     */
    protected $viewcount;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Signupfavoriteshop", mappedBy="signupfavoriteshop")
     */
    protected $shop;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\AffliateNetwork", inversedBy="affliatenetwork")
     * @ORM\JoinColumn(name="affliateNetworkId", referencedColumnName="id", onDelete="restrict")
     */
    protected $affliatenetwork;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Page", inversedBy="pages")
     * @ORM\JoinColumn(name="howtoUsepageId", referencedColumnName="id", onDelete="restrict")
     */
    protected $shopPage;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\ImageHowToUseSmallImage", inversedBy="shop")
     * @ORM\JoinColumn(name="howtoUseSmallImageId", referencedColumnName="id")
     */
    protected $howtousesmallimage;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\ImageHowToUseBigImage", inversedBy="shop")
     * @ORM\JoinColumn(name="howtoUseBigImageId", referencedColumnName="id")
     */
    protected $howtousebigimage;

 

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Visitor", inversedBy="favoriteshops")
     * @ORM\JoinTable(
     *     name="favorite_shop",
     *     joinColumns={@ORM\JoinColumn(name="shopId", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="visitorId", referencedColumnName="id", nullable=false)}
     * )
     */
    protected $visitors;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\ExcludedKeyword", mappedBy="shops")
     */
    protected $keywords;
    
    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\FavoriteShop", mappedBy="shop")
     */
    protected $favoriteshops;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $moretextforshop;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $howtoSubSubTitle;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $shopsViewedIds;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    protected $shopAndOfferClickouts;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    protected $lastSevendayClickouts;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    protected $customtextposition;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $showcustomtext;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $customtext;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $futurecode;
 
    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value = '')
    {
        $this->$property = $value;
    }
}