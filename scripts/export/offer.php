<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="offer",
 *     indexes={
 *         @ORM\Index(name="shopid_idx", columns={"shopid"}),
 *         @ORM\Index(
 *             name="ind_offer_exdist",
 *             columns={"exclusivecode","discounttype","startdate","title","enddate","visability","approved"}
 *         ),
 *         @ORM\Index(name="ind_offer_shenex", columns={"shopid","enddate","exclusivecode"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="offerlogoid", columns={"offerlogoid"})}
 * )
 */
class offer
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $visability;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discounttype;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $couponcode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $refOfferUrl;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $refurl;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
     */
    private $startdate;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
     */
    private $enddate;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $exclusivecode;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $editorpicks;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $extendedoffer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extendedtitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extendedurl;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $extendedmetadescription;

    /**
     * @ORM\Column(type="unknown:@longblob", nullable=true)
     */
    private $extendedfulldescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $discount;

    /**
     * @ORM\Column(type="enum", nullable=true)
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
     * @ORM\Column(type="enum", nullable=true)
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
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $userGenerated;

    /**
     * @ORM\Column(type="enum", nullable=false)
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
    private $shopexist;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $totalviewcount;

    /**
     * @ORM\Column(type="decimal", length=16, nullable=true, scale=4)
     */
    private $popularitycount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $couponcodetype;
}