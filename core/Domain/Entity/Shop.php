<?php
namespace Core\Domain\Entity;
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
     * @ORM\OneToOne(targetEntity="Core\Domain\Entity\Logo", inversedBy="shop")
     * @ORM\JoinColumn(name="logoId", referencedColumnName="id", unique=true)
     */
    protected $logo;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Adminfavoriteshp", mappedBy="shops")
     */
    protected $adminfevoriteshops;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Conversions", mappedBy="shop")
     */
    protected $conversions;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Offer", mappedBy="shopOffers")
     */
    protected $offer;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\OfferNews", mappedBy="shop")
     */
    protected $offerNews;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\PopularShop", mappedBy="popularshops")
     */
    protected $popularshop;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\RefArticleStore", mappedBy="articleshops")
     */
    protected $articlestore;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\RefExcludedkeywordShop", mappedBy="keywords")
     */
    protected $shopsofKeyword;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\RefShopCategory", mappedBy="category")
     */
    protected $categoryshops;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\RefShopRelatedshop", mappedBy="shop")
     */
    protected $relatedshops;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\ShopHowToChapter", mappedBy="shop")
     */
    protected $howtochapter;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\ShopViewCount", mappedBy="shop")
     */
    protected $viewcount;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Signupfavoriteshop", mappedBy="signupfavoriteshop")
     */
    protected $shop;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\AffliateNetwork", inversedBy="affliatenetwork")
     * @ORM\JoinColumn(name="affliateNetworkId", referencedColumnName="id", onDelete="restrict")
     */
    protected $affliatenetwork;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\Page", inversedBy="pages")
     * @ORM\JoinColumn(name="howtoUsepageId", referencedColumnName="id", onDelete="restrict")
     */
    protected $shopPage;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\ImageHowToUseSmallImage", inversedBy="shop")
     * @ORM\JoinColumn(name="howtoUseSmallImageId", referencedColumnName="id")
     */
    protected $howtousesmallimage;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\ImageHowToUseBigImage", inversedBy="shop")
     * @ORM\JoinColumn(name="howtoUseBigImageId", referencedColumnName="id")
     */
    protected $howtousebigimage;

 

    /**
     * @ORM\ManyToMany(targetEntity="Core\Domain\Entity\Visitor", inversedBy="favoriteshops")
     * @ORM\JoinTable(
     *     name="favorite_shop",
     *     joinColumns={@ORM\JoinColumn(name="shopId", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="visitorId", referencedColumnName="id", nullable=false)}
     * )
     */
    protected $visitors;

    /**
     * @ORM\ManyToMany(targetEntity="Core\Domain\Entity\ExcludedKeyword", mappedBy="shops")
     */
    protected $keywords;
    
    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\FavoriteShop", mappedBy="shop")
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

    /**
     * @return mixed
     */
    public function getAccountManagerName()
    {
        return $this->accountManagerName;
    }

    /**
     * @param mixed $accountManagerName
     */
    public function setAccountManagerName($accountManagerName)
    {
        $this->accountManagerName = $accountManagerName;
    }

    /**
     * @return mixed
     */
    public function getDeliverytime()
    {
        return $this->Deliverytime;
    }

    /**
     * @param mixed $Deliverytime
     */
    public function setDeliverytime($Deliverytime)
    {
        $this->Deliverytime = $Deliverytime;
    }

    /**
     * @return mixed
     */
    public function getAccoutManagerId()
    {
        return $this->accoutManagerId;
    }

    /**
     * @param mixed $accoutManagerId
     */
    public function setAccoutManagerId($accoutManagerId)
    {
        $this->accoutManagerId = $accoutManagerId;
    }

    /**
     * @return mixed
     */
    public function getActualUrl()
    {
        return $this->actualUrl;
    }

    /**
     * @param mixed $actualUrl
     */
    public function setActualUrl($actualUrl)
    {
        $this->actualUrl = $actualUrl;
    }

    /**
     * @return mixed
     */
    public function getAddtosearch()
    {
        return $this->addtosearch;
    }

    /**
     * @param mixed $addtosearch
     */
    public function setAddtosearch($addtosearch)
    {
        $this->addtosearch = $addtosearch;
    }

    /**
     * @return mixed
     */
    public function getAdminfevoriteshops()
    {
        return $this->adminfevoriteshops;
    }

    /**
     * @param mixed $adminfevoriteshops
     */
    public function setAdminfevoriteshops($adminfevoriteshops)
    {
        $this->adminfevoriteshops = $adminfevoriteshops;
    }

    /**
     * @return mixed
     */
    public function getAffliateProgram()
    {
        return $this->affliateProgram;
    }

    /**
     * @param mixed $affliateProgram
     */
    public function setAffliateProgram($affliateProgram)
    {
        $this->affliateProgram = $affliateProgram;
    }

    /**
     * @return mixed
     */
    public function getAffliatenetwork()
    {
        return $this->affliatenetwork;
    }

    /**
     * @param mixed $affliatenetwork
     */
    public function setAffliatenetwork($affliatenetwork)
    {
        $this->affliatenetwork = $affliatenetwork;
    }

    /**
     * @return mixed
     */
    public function getArticlestore()
    {
        return $this->articlestore;
    }

    /**
     * @param mixed $articlestore
     */
    public function setArticlestore($articlestore)
    {
        $this->articlestore = $articlestore;
    }

    /**
     * @return mixed
     */
    public function getBrandingcss()
    {
        return $this->brandingcss;
    }

    /**
     * @param mixed $brandingcss
     */
    public function setBrandingcss($brandingcss)
    {
        $this->brandingcss = $brandingcss;
    }

    /**
     * @return mixed
     */
    public function getCategoryshops()
    {
        return $this->categoryshops;
    }

    /**
     * @param mixed $categoryshops
     */
    public function setCategoryshops($categoryshops)
    {
        $this->categoryshops = $categoryshops;
    }

    /**
     * @return mixed
     */
    public function getChainId()
    {
        return $this->chainId;
    }

    /**
     * @param mixed $chainId
     */
    public function setChainId($chainId)
    {
        $this->chainId = $chainId;
    }

    /**
     * @return mixed
     */
    public function getChainItemId()
    {
        return $this->chainItemId;
    }

    /**
     * @param mixed $chainItemId
     */
    public function setChainItemId($chainItemId)
    {
        $this->chainItemId = $chainItemId;
    }

    /**
     * @return mixed
     */
    public function getCodeAlertSendDate()
    {
        return $this->code_alert_send_date;
    }

    /**
     * @param mixed $code_alert_send_date
     */
    public function setCodeAlertSendDate($code_alert_send_date)
    {
        $this->code_alert_send_date = $code_alert_send_date;
    }

    /**
     * @return mixed
     */
    public function getContentManagerId()
    {
        return $this->contentManagerId;
    }

    /**
     * @param mixed $contentManagerId
     */
    public function setContentManagerId($contentManagerId)
    {
        $this->contentManagerId = $contentManagerId;
    }

    /**
     * @return mixed
     */
    public function getContentManagerName()
    {
        return $this->contentManagerName;
    }

    /**
     * @param mixed $contentManagerName
     */
    public function setContentManagerName($contentManagerName)
    {
        $this->contentManagerName = $contentManagerName;
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
    public function getCustomHeader()
    {
        return $this->customHeader;
    }

    /**
     * @param mixed $customHeader
     */
    public function setCustomHeader($customHeader)
    {
        $this->customHeader = $customHeader;
    }

    /**
     * @return mixed
     */
    public function getCustomtext()
    {
        return $this->customtext;
    }

    /**
     * @param mixed $customtext
     */
    public function setCustomtext($customtext)
    {
        $this->customtext = $customtext;
    }

    /**
     * @return mixed
     */
    public function getCustomtextposition()
    {
        return $this->customtextposition;
    }

    /**
     * @param mixed $customtextposition
     */
    public function setCustomtextposition($customtextposition)
    {
        $this->customtextposition = $customtextposition;
    }

    /**
     * @return mixed
     */
    public function getDeepLink()
    {
        return $this->deepLink;
    }

    /**
     * @param mixed $deepLink
     */
    public function setDeepLink($deepLink)
    {
        $this->deepLink = $deepLink;
    }

    /**
     * @return mixed
     */
    public function getDeepLinkStatus()
    {
        return $this->deepLinkStatus;
    }

    /**
     * @param mixed $deepLinkStatus
     */
    public function setDeepLinkStatus($deepLinkStatus)
    {
        $this->deepLinkStatus = $deepLinkStatus;
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
    public function getDeliveryCost()
    {
        return $this->deliveryCost;
    }

    /**
     * @param mixed $deliveryCost
     */
    public function setDeliveryCost($deliveryCost)
    {
        $this->deliveryCost = $deliveryCost;
    }

    /**
     * @return mixed
     */
    public function getDiscussions()
    {
        return $this->discussions;
    }

    /**
     * @param mixed $discussions
     */
    public function setDiscussions($discussions)
    {
        $this->discussions = $discussions;
    }

    /**
     * @return mixed
     */
    public function getDisplayExtraProperties()
    {
        return $this->displayExtraProperties;
    }

    /**
     * @param mixed $displayExtraProperties
     */
    public function setDisplayExtraProperties($displayExtraProperties)
    {
        $this->displayExtraProperties = $displayExtraProperties;
    }

    /**
     * @return mixed
     */
    public function getFavoriteshops()
    {
        return $this->favoriteshops;
    }

    /**
     * @param mixed $favoriteshops
     */
    public function setFavoriteshops($favoriteshops)
    {
        $this->favoriteshops = $favoriteshops;
    }

    /**
     * @return mixed
     */
    public function getFeaturedtext()
    {
        return $this->featuredtext;
    }

    /**
     * @param mixed $featuredtext
     */
    public function setFeaturedtext($featuredtext)
    {
        $this->featuredtext = $featuredtext;
    }

    /**
     * @return mixed
     */
    public function getFeaturedtextdate()
    {
        return $this->featuredtextdate;
    }

    /**
     * @param mixed $featuredtextdate
     */
    public function setFeaturedtextdate($featuredtextdate)
    {
        $this->featuredtextdate = $featuredtextdate;
    }

    /**
     * @return mixed
     */
    public function getFreeDelivery()
    {
        return $this->freeDelivery;
    }

    /**
     * @param mixed $freeDelivery
     */
    public function setFreeDelivery($freeDelivery)
    {
        $this->freeDelivery = $freeDelivery;
    }

    /**
     * @return mixed
     */
    public function getFreeReturns()
    {
        return $this->freeReturns;
    }

    /**
     * @param mixed $freeReturns
     */
    public function setFreeReturns($freeReturns)
    {
        $this->freeReturns = $freeReturns;
    }

    /**
     * @return mixed
     */
    public function getFuturecode()
    {
        return $this->futurecode;
    }

    /**
     * @param mixed $futurecode
     */
    public function setFuturecode($futurecode)
    {
        $this->futurecode = $futurecode;
    }

    /**
     * @return mixed
     */
    public function getHowToIntroductionText()
    {
        return $this->howToIntroductionText;
    }

    /**
     * @param mixed $howToIntroductionText
     */
    public function setHowToIntroductionText($howToIntroductionText)
    {
        $this->howToIntroductionText = $howToIntroductionText;
    }

    /**
     * @return mixed
     */
    public function getHowToUse()
    {
        return $this->howToUse;
    }

    /**
     * @param mixed $howToUse
     */
    public function setHowToUse($howToUse)
    {
        $this->howToUse = $howToUse;
    }

    /**
     * @return mixed
     */
    public function getHowtoMetaDescription()
    {
        return $this->howtoMetaDescription;
    }

    /**
     * @param mixed $howtoMetaDescription
     */
    public function setHowtoMetaDescription($howtoMetaDescription)
    {
        $this->howtoMetaDescription = $howtoMetaDescription;
    }

    /**
     * @return mixed
     */
    public function getHowtoMetaTitle()
    {
        return $this->howtoMetaTitle;
    }

    /**
     * @param mixed $howtoMetaTitle
     */
    public function setHowtoMetaTitle($howtoMetaTitle)
    {
        $this->howtoMetaTitle = $howtoMetaTitle;
    }

    /**
     * @return mixed
     */
    public function getHowtoSubSubTitle()
    {
        return $this->howtoSubSubTitle;
    }

    /**
     * @param mixed $howtoSubSubTitle
     */
    public function setHowtoSubSubTitle($howtoSubSubTitle)
    {
        $this->howtoSubSubTitle = $howtoSubSubTitle;
    }

    /**
     * @return mixed
     */
    public function getHowtoSubtitle()
    {
        return $this->howtoSubtitle;
    }

    /**
     * @param mixed $howtoSubtitle
     */
    public function setHowtoSubtitle($howtoSubtitle)
    {
        $this->howtoSubtitle = $howtoSubtitle;
    }

    /**
     * @return mixed
     */
    public function getHowtoTitle()
    {
        return $this->howtoTitle;
    }

    /**
     * @param mixed $howtoTitle
     */
    public function setHowtoTitle($howtoTitle)
    {
        $this->howtoTitle = $howtoTitle;
    }

    /**
     * @return mixed
     */
    public function getHowtochapter()
    {
        return $this->howtochapter;
    }

    /**
     * @param mixed $howtochapter
     */
    public function setHowtochapter($howtochapter)
    {
        $this->howtochapter = $howtochapter;
    }

    /**
     * @return mixed
     */
    public function getHowtoguideslug()
    {
        return $this->howtoguideslug;
    }

    /**
     * @param mixed $howtoguideslug
     */
    public function setHowtoguideslug($howtoguideslug)
    {
        $this->howtoguideslug = $howtoguideslug;
    }

    /**
     * @return mixed
     */
    public function getHowtousebigimage()
    {
        return $this->howtousebigimage;
    }

    /**
     * @param mixed $howtousebigimage
     */
    public function setHowtousebigimage($howtousebigimage)
    {
        $this->howtousebigimage = $howtousebigimage;
    }

    /**
     * @return mixed
     */
    public function getHowtousesmallimage()
    {
        return $this->howtousesmallimage;
    }

    /**
     * @param mixed $howtousesmallimage
     */
    public function setHowtousesmallimage($howtousesmallimage)
    {
        $this->howtousesmallimage = $howtousesmallimage;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

     /**
     * @return mixed
     */
    public function getIdeal()
    {
        return $this->ideal;
    }

    /**
     * @param mixed $ideal
     */
    public function setIdeal($ideal)
    {
        $this->ideal = $ideal;
    }

    /**
     * @return mixed
     */
    public function getKeywordlink()
    {
        return $this->keywordlink;
    }

    /**
     * @param mixed $keywordlink
     */
    public function setKeywordlink($keywordlink)
    {
        $this->keywordlink = $keywordlink;
    }

    /**
     * @return mixed
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param mixed $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return mixed
     */
    public function getLastSevendayClickouts()
    {
        return $this->lastSevendayClickouts;
    }

    /**
     * @param mixed $lastSevendayClickouts
     */
    public function setLastSevendayClickouts($lastSevendayClickouts)
    {
        $this->lastSevendayClickouts = $lastSevendayClickouts;
    }

    /**
     * @return mixed
     */
    public function getLightboxfirsttext()
    {
        return $this->lightboxfirsttext;
    }

    /**
     * @param mixed $lightboxfirsttext
     */
    public function setLightboxfirsttext($lightboxfirsttext)
    {
        $this->lightboxfirsttext = $lightboxfirsttext;
    }

    /**
     * @return mixed
     */
    public function getLightboxsecondtext()
    {
        return $this->lightboxsecondtext;
    }

    /**
     * @param mixed $lightboxsecondtext
     */
    public function setLightboxsecondtext($lightboxsecondtext)
    {
        $this->lightboxsecondtext = $lightboxsecondtext;
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
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param mixed $metaDescription
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return mixed
     */
    public function getMobileShop()
    {
        return $this->mobileShop;
    }

    /**
     * @param mixed $mobileShop
     */
    public function setMobileShop($mobileShop)
    {
        $this->mobileShop = $mobileShop;
    }

    /**
     * @return mixed
     */
    public function getMoretextforshop()
    {
        return $this->moretextforshop;
    }

    /**
     * @param mixed $moretextforshop
     */
    public function setMoretextforshop($moretextforshop)
    {
        $this->moretextforshop = $moretextforshop;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
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
    public function getOfferNews()
    {
        return $this->offerNews;
    }

    /**
     * @param mixed $offerNews
     */
    public function setOfferNews($offerNews)
    {
        $this->offerNews = $offerNews;
    }

    /**
     * @return mixed
     */
    public function getOfflineSicne()
    {
        return $this->offlineSicne;
    }

    /**
     * @param mixed $offlineSicne
     */
    public function setOfflineSicne($offlineSicne)
    {
        $this->offlineSicne = $offlineSicne;
    }

    /**
     * @return mixed
     */
    public function getOverriteBrowserTitle()
    {
        return $this->overriteBrowserTitle;
    }

    /**
     * @param mixed $overriteBrowserTitle
     */
    public function setOverriteBrowserTitle($overriteBrowserTitle)
    {
        $this->overriteBrowserTitle = $overriteBrowserTitle;
    }

    /**
     * @return mixed
     */
    public function getOverriteSubtitle()
    {
        return $this->overriteSubtitle;
    }

    /**
     * @param mixed $overriteSubtitle
     */
    public function setOverriteSubtitle($overriteSubtitle)
    {
        $this->overriteSubtitle = $overriteSubtitle;
    }

    /**
     * @return mixed
     */
    public function getOverriteTitle()
    {
        return $this->overriteTitle;
    }

    /**
     * @param mixed $overriteTitle
     */
    public function setOverriteTitle($overriteTitle)
    {
        $this->overriteTitle = $overriteTitle;
    }

    /**
     * @return mixed
     */
    public function getPermaLink()
    {
        return $this->permaLink;
    }

    /**
     * @param mixed $permaLink
     */
    public function setPermaLink($permaLink)
    {
        $this->permaLink = $permaLink;
    }

    /**
     * @return mixed
     */
    public function getPickupPoints()
    {
        return $this->pickupPoints;
    }

    /**
     * @param mixed $pickupPoints
     */
    public function setPickupPoints($pickupPoints)
    {
        $this->pickupPoints = $pickupPoints;
    }

    /**
     * @return mixed
     */
    public function getPopularshop()
    {
        return $this->popularshop;
    }

    /**
     * @param mixed $popularshop
     */
    public function setPopularshop($popularshop)
    {
        $this->popularshop = $popularshop;
    }

    /**
     * @return mixed
     */
    public function getQShops()
    {
        return $this->qShops;
    }

    /**
     * @param mixed $qShops
     */
    public function setQShops($qShops)
    {
        $this->qShops = $qShops;
    }

    /**
     * @return mixed
     */
    public function getRefUrl()
    {
        return $this->refUrl;
    }

    /**
     * @param mixed $refUrl
     */
    public function setRefUrl($refUrl)
    {
        $this->refUrl = $refUrl;
    }

    /**
     * @return mixed
     */
    public function getRelatedshops()
    {
        return $this->relatedshops;
    }

    /**
     * @param mixed $relatedshops
     */
    public function setRelatedshops($relatedshops)
    {
        $this->relatedshops = $relatedshops;
    }

    /**
     * @return mixed
     */
    public function getReturnPolicy()
    {
        return $this->returnPolicy;
    }

    /**
     * @param mixed $returnPolicy
     */
    public function setReturnPolicy($returnPolicy)
    {
        $this->returnPolicy = $returnPolicy;
    }

    /**
     * @return mixed
     */
    public function getScreenshotId()
    {
        return $this->screenshotId;
    }

    /**
     * @param mixed $screenshotId
     */
    public function setScreenshotId($screenshotId)
    {
        $this->screenshotId = $screenshotId;
    }

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function getServiceNumber()
    {
        return $this->serviceNumber;
    }

    /**
     * @param mixed $serviceNumber
     */
    public function setServiceNumber($serviceNumber)
    {
        $this->serviceNumber = $serviceNumber;
    }

    /**
     * @return mixed
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * @param mixed $shop
     */
    public function setShop($shop)
    {
        $this->shop = $shop;
    }

    /**
     * @return mixed
     */
    public function getShopAndOfferClickouts()
    {
        return $this->shopAndOfferClickouts;
    }

    /**
     * @param mixed $shopAndOfferClickouts
     */
    public function setShopAndOfferClickouts($shopAndOfferClickouts)
    {
        $this->shopAndOfferClickouts = $shopAndOfferClickouts;
    }

    /**
     * @return mixed
     */
    public function getShopPage()
    {
        return $this->shopPage;
    }

    /**
     * @param mixed $shopPage
     */
    public function setShopPage($shopPage)
    {
        $this->shopPage = $shopPage;
    }

    /**
     * @return mixed
     */
    public function getShopText()
    {
        return $this->shopText;
    }

    /**
     * @param mixed $shopText
     */
    public function setShopText($shopText)
    {
        $this->shopText = $shopText;
    }

    /**
     * @return mixed
     */
    public function getShopsViewedIds()
    {
        return $this->shopsViewedIds;
    }

    /**
     * @param mixed $shopsViewedIds
     */
    public function setShopsViewedIds($shopsViewedIds)
    {
        $this->shopsViewedIds = $shopsViewedIds;
    }

    /**
     * @return mixed
     */
    public function getShopsofKeyword()
    {
        return $this->shopsofKeyword;
    }

    /**
     * @param mixed $shopsofKeyword
     */
    public function setShopsofKeyword($shopsofKeyword)
    {
        $this->shopsofKeyword = $shopsofKeyword;
    }

    /**
     * @return mixed
     */
    public function getShowChains()
    {
        return $this->showChains;
    }

    /**
     * @param mixed $showChains
     */
    public function setShowChains($showChains)
    {
        $this->showChains = $showChains;
    }

    /**
     * @return mixed
     */
    public function getShowSignupOption()
    {
        return $this->showSignupOption;
    }

    /**
     * @param mixed $showSignupOption
     */
    public function setShowSignupOption($showSignupOption)
    {
        $this->showSignupOption = $showSignupOption;
    }

    /**
     * @return mixed
     */
    public function getShowSimliarShops()
    {
        return $this->showSimliarShops;
    }

    /**
     * @param mixed $showSimliarShops
     */
    public function setShowSimliarShops($showSimliarShops)
    {
        $this->showSimliarShops = $showSimliarShops;
    }

    /**
     * @return mixed
     */
    public function getShowcustomtext()
    {
        return $this->showcustomtext;
    }

    /**
     * @param mixed $showcustomtext
     */
    public function setShowcustomtext($showcustomtext)
    {
        $this->showcustomtext = $showcustomtext;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStrictConfirmation()
    {
        return $this->strictConfirmation;
    }

    /**
     * @param mixed $strictConfirmation
     */
    public function setStrictConfirmation($strictConfirmation)
    {
        $this->strictConfirmation = $strictConfirmation;
    }

    /**
     * @return mixed
     */
    public function getSubTitle()
    {
        return $this->subTitle;
    }

    /**
     * @param mixed $subTitle
     */
    public function setSubTitle($subTitle)
    {
        $this->subTitle = $subTitle;
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
    public function getTotalviewcount()
    {
        return $this->totalviewcount;
    }

    /**
     * @param mixed $totalviewcount
     */
    public function setTotalviewcount($totalviewcount)
    {
        $this->totalviewcount = $totalviewcount;
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
    public function getUsergenratedcontent()
    {
        return $this->usergenratedcontent;
    }

    /**
     * @param mixed $usergenratedcontent
     */
    public function setUsergenratedcontent($usergenratedcontent)
    {
        $this->usergenratedcontent = $usergenratedcontent;
    }

    /**
     * @return mixed
     */
    public function getViewcount()
    {
        return $this->viewcount;
    }

    /**
     * @param mixed $viewcount
     */
    public function setViewcount($viewcount)
    {
        $this->viewcount = $viewcount;
    }

    /**
     * @return mixed
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param mixed $views
     */
    public function setViews($views)
    {
        $this->views = $views;
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
}