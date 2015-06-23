<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="page",
 *     indexes={
 *         @ORM\Index(name="pageattributeid_idx", columns={"pageattributeid"}),
 *         @ORM\Index(name="pageHeaderImageId_foreign_key", columns={"pageHeaderImageId"})
 *     }
 * )
 */
class page
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $pagetype;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pagetitle;

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
    private $metatitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metadescription;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $publish;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $pagelock;

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
    private $enabletimeconstraint;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $timenumberofdays;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $timetype;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $timemaxoffer;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $timeorder;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $enablewordconstraint;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $wordtitle;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $wordmaxoffer;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $publishdate;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $wordorder;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $awardconstratint;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $awardtype;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $awardmaxoffer;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $awardorder;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $enableclickconstraint;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $numberofclicks;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $clickmaxoffer;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $clickorder;

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
    private $couponregular;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $couponeditorpick;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $couponexclusive;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $saleregular;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $saleeditorpick;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $saleexclusive;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $printableregular;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $printableeditorpick;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $printableexclusive;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $showpage;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $logoid;

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
    private $customheader;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $showsitemap;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $offersCount;

    /**
     * @ORM\OneToMany(targetEntity="moneysaving", mappedBy="page")
     */
    private $moneysaving;

    /**
     * @ORM\OneToMany(targetEntity="ref_offer_page", mappedBy="offers")
     */
    private $pageoffers;

    /**
     * @ORM\OneToMany(targetEntity="ref_page_widget", mappedBy="widget")
     */
    private $pagewidget;

    /**
     * @ORM\OneToMany(targetEntity="shop", mappedBy="shopPage")
     */
    private $pages;

    /**
     * @ORM\OneToMany(targetEntity="special_list", mappedBy="page")
     */
    private $specialList;

    /**
     * @ORM\ManyToOne(targetEntity="image", inversedBy="pageheaderimage")
     * @ORM\JoinColumn(name="pageHeaderImageId", referencedColumnName="id", onDelete="cascade")
     */
    private $logo;

    /**
     * @ORM\ManyToOne(targetEntity="page_attribute", inversedBy="pageattribute")
     * @ORM\JoinColumn(name="pageattributeid", referencedColumnName="id", onDelete="restrict")
     */
    private $page;
}