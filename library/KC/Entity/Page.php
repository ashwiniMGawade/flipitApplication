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
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pageTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $permalink;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metaTitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metaDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $publish;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $pageLock;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
     */
    private $contentManagerId;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $contentManagerName;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $enableTimeConstraint;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $timenumberOfDays;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $timeType;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $timeMaxOffer;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $timeOrder;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $enableWordConstraint;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $wordTitle;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $wordMaxOffer;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $publishDate;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $wordOrder;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $awardConstratint;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $awardType;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $awardMaxOffer;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $awardOrder;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $enableClickConstraint;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $numberOfClicks;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $clickMaxOffer;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $clickOrder;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $maxOffers;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $oderOffers;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $couponRegular;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $couponEditorPick;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $couponExclusive;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $saleRegular;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $saleEditorPick;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $saleExclusive;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $printableRegular;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $printableEditorPick;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $printableExclusive;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $showPage;


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
    private $customHeader;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $showsitemap;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subtitle;
    
    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Logo", inversedBy="page")
     * @ORM\JoinColumn(name="pageHeaderImageId", referencedColumnName="id", unique=true)
     */
    private $pageHeaderImageId;
    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $offersCount;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showinmobilemenu;

    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Logo", inversedBy="page")
     * @ORM\JoinColumn(name="logoid", referencedColumnName="id", unique=true)
     */
    private $logo;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\MoneySaving", mappedBy="page")
     */
    private $moneysaving;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefOfferPage", mappedBy="offers")
     */
    private $pageoffers;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefPageWidget", mappedBy="widget")
     */
    private $pagewidget;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Shop", mappedBy="shopPage")
     */
    private $pages;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\SpecialList", mappedBy="page")
     */
    private $specialList;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\PageAttribute", inversedBy="pageattribute")
     * @ORM\JoinColumn(name="pageAttributeId", referencedColumnName="id", onDelete="restrict")
     */
    private $page;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\SpecialPagesOffers", mappedBy="pages")
     */
    private $specialPagesOffers;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Logo", inversedBy="homepageimage")
     * @ORM\JoinColumn(name="pageHomeImageId", referencedColumnName="id")
     */
    private $homepageimage;

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
