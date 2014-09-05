<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="shop",
 *     indexes={
 *         @ORM\Index(name="affliatenetworkid_idx", columns={"affliatenetworkid"}),
 *         @ORM\Index(name="howtousepageid_idx", columns={"howtousepageid"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="logoid", columns={"logoid"}),
 *         @ORM\UniqueConstraint(name="howtousesmallimageid", columns={"howtousesmallimageid"}),
 *         @ORM\UniqueConstraint(name="howtousebigimageid", columns={"howtousebigimageid"})
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
    private $permalink;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metadescription;

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
    private $deeplink;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $deeplinkstatus;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $refurl;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $actualurl;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $affliateprogram;

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
    private $overritetitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $overritesubtitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $overritebrowsertitle;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $shoptext;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $views;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $howtouse;

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
    private $offlinesicne;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $accoutmanagerid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $accountManagerName;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $contentmanagerid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contentManagerName;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
     */
    private $screenshotid;

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
     * @ORM\Column(type="blob", nullable=true)
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
    private $showsignupoption;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $addtosearch;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $customheader;

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
    private $showchains;

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
    private $strictconfirmation;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $howToIntroductionText;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $brandingcss;

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
     * @ORM\OneToMany(targetEntity="KC\Entity\Signupfavoriteshop", mappedBy="signupfavoriteshop")
     */
    private $shop;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\AffliateNetwork", inversedBy="affliatenetwork")
     * @ORM\JoinColumn(name="affliatenetworkid", referencedColumnName="id", onDelete="restrict")
     */
    private $affliatenetwork;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Image", inversedBy="howtousebigimage")
     * @ORM\JoinColumn(name="howtousebigimageid", referencedColumnName="id", onDelete="restrict")
     */
    private $shops;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Page", inversedBy="pages")
     * @ORM\JoinColumn(name="howtousepageid", referencedColumnName="id", onDelete="restrict")
     */
    private $shopPage;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Image", inversedBy="smallimage")
     * @ORM\JoinColumn(name="howtousesmallimageid", referencedColumnName="id", onDelete="restrict")
     */
    private $shopimage;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Image", inversedBy="logo")
     * @ORM\JoinColumn(name="logoid", referencedColumnName="id", onDelete="restrict")
     */
    private $shoplogo;

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
}