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
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", unique=true, length=255, nullable=true)
     */
    private $permaLink;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metaDescription;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $usergenratedcontent;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $notes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deepLink;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $deepLinkStatus;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $refUrl;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $actualUrl;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $affliateProgram;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $overriteTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $overriteSubtitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $overriteBrowserTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $shopText;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $views;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $howToUse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Deliverytime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $returnPolicy;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $freeDelivery;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deliveryCost;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $offlineSicne;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $accoutManagerId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $accountManagerName;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $contentManagerId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contentManagerName;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $screenshotId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $keywordlink;

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
     * @ORM\Column(type="string", nullable=true)
     */
    private $howtoTitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $howtoSubtitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $howtoMetaTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $howtoMetaDescription;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $ideal;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $qShops;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $freeReturns;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $pickupPoints;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $mobileShop;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $service;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $serviceNumber;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $discussions;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $displayExtraProperties;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $showSignupOption;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $addtosearch;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $customHeader;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $totalviewcount;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $showSimliarShops;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $showChains;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $chainItemId;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $chainId;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $strictConfirmation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $howToIntroductionText;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $brandingcss;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lightboxsecondtext;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lightboxfirsttext;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $howtoguideslug;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $code_alert_send_date;
    
    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Logo", inversedBy="shop")
     * @ORM\JoinColumn(name="logoId", referencedColumnName="id", unique=true)
     */
    private $logo;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Adminfavoriteshp", mappedBy="shops")
     */
    private $adminfevoriteshops;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Conversions", mappedBy="shop")
     */
    private $conversions;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Offer", mappedBy="shopOffers")
     */
    private $offer;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\OfferNews", mappedBy="shop")
     */
    private $offerNews;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\PopularShop", mappedBy="popularshops")
     */
    private $popularshop;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefArticleStore", mappedBy="articleshops")
     */
    private $articlestore;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefExcludedkeywordShop", mappedBy="keywords")
     */
    private $shopsofKeyword;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefShopCategory", mappedBy="category")
     */
    private $categoryshops;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefShopRelatedshop", mappedBy="shop")
     */
    private $relatedshops;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\ShopHowToChapter", mappedBy="shop")
     */
    private $howtochapter;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\ShopViewCount", mappedBy="shop")
     */
    private $viewcount;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\EditorBallonText", mappedBy="shop")
     */
    private $ballontext;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Signupfavoriteshop", mappedBy="signupfavoriteshop")
     */
    private $shop;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\AffliateNetwork", inversedBy="affliatenetwork")
     * @ORM\JoinColumn(name="affliateNetworkId", referencedColumnName="id", onDelete="restrict")
     */
    private $affliatenetwork;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Page", inversedBy="pages")
     * @ORM\JoinColumn(name="howtoUsepageId", referencedColumnName="id", onDelete="restrict")
     */
    private $shopPage;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\ImageHowToUseSmallImage", inversedBy="shop")
     * @ORM\JoinColumn(name="howtoUseSmallImageId", referencedColumnName="id")
     */
    private $howtousesmallimage;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\ImageHowToUseBigImage", inversedBy="shop")
     * @ORM\JoinColumn(name="howtoUseBigImageId", referencedColumnName="id")
     */
    private $howtousebigimage;

 

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Visitor", inversedBy="favoriteshops")
     * @ORM\JoinTable(
     *     name="favorite_shop",
     *     joinColumns={@ORM\JoinColumn(name="shopId", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="visitorId", referencedColumnName="id", nullable=false)}
     * )
     */
    private $visitors;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\ExcludedKeyword", mappedBy="shops")
     */
    private $keywords;
    
    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\FavoriteShop", mappedBy="shop")
     */
    private $favoriteshops;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $moretextforshop;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $howtoSubSubTitle;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $shopsViewedIds;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    private $shopAndOfferClickouts;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    private $lastSevendayClickouts;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    private $customtextposition;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showcustomtext;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $customtext;
 

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value = '')
    {
        $this->$property = $value;
    }
}