<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="page",
 *     indexes={
 *         @ORM\Index(name="pageattributeid_idx", columns={"pageAttributeId"}),
 *         @ORM\Index(name="pageHeaderImageId_foreign_key", columns={"pageHeaderImageId"})
 *     }
 * )
 * @ORM\DiscriminatorMap({"offer"="KC\Entity\OfferListPage","default"="KC\Entity\DefaultPage"})
 * @ORM\DiscriminatorColumn(name="pageType", length=10, type="string")
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class Page
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
    protected $pageTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $permalink;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaTitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $publish;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $pageLock;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
     */
    protected $contentManagerId;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    protected $contentManagerName;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $enableTimeConstraint;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $timenumberOfDays;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $timeType;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $timeMaxOffer;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $timeOrder;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $enableWordConstraint;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $wordTitle;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $wordMaxOffer;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $publishDate;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $wordOrder;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $awardConstratint;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    protected $awardType;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $awardMaxOffer;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $awardOrder;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $enableClickConstraint;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $numberOfClicks;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $clickMaxOffer;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $clickOrder;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    protected $maxOffers;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    protected $oderOffers;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $couponRegular;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $couponEditorPick;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $couponExclusive;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $saleRegular;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $saleEditorPick;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $saleExclusive;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $printableRegular;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $printableEditorPick;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $printableExclusive;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $showPage;


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
    protected $customHeader;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $showsitemap;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $subtitle;
    
    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Logo", inversedBy="page")
     * @ORM\JoinColumn(name="pageHeaderImageId", referencedColumnName="id", unique=true)
     */
    protected $pageHeaderImageId;
    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $offersCount;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $showinmobilemenu;

    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Logo", inversedBy="page")
     * @ORM\JoinColumn(name="logoid", referencedColumnName="id", unique=true)
     */
    protected $logo;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\MoneySaving", mappedBy="page")
     */
    protected $moneysaving;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefOfferPage", mappedBy="offers")
     */
    protected $pageoffers;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefPageWidget", mappedBy="widget")
     */
    protected $pagewidget;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Shop", mappedBy="shopPage")
     */
    protected $pages;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\SpecialList", mappedBy="page")
     */
    protected $specialList;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\PageAttribute", inversedBy="pageattribute")
     * @ORM\JoinColumn(name="pageAttributeId", referencedColumnName="id", onDelete="restrict")
     */
    protected $page;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\SpecialPagesOffers", mappedBy="pages")
     */
    protected $specialPagesOffers;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Logo", inversedBy="homepageimage")
     * @ORM\JoinColumn(name="pageHomeImageId", referencedColumnName="id")
     */
    protected $homepageimage;

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
