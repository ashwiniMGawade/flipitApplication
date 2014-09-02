<?php
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
class shop
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $permalink;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metadescription;

    /**
     * @ORM\Column(type="enum", nullable=true)
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
     * @ORM\Column(type="unknown:@longblob", nullable=true)
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
     * @ORM\Column(type="enum", nullable=true)
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
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
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
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
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
     * @ORM\Column(type="unknown:@longblob", nullable=true)
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
     * @ORM\Column(type="unknown:@longblob", nullable=true)
     */
    private $howToIntroductionText;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $brandingcss;
}