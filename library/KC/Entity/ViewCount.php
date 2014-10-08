<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="view_count",
 *     indexes={
 *         @ORM\Index(name="offerid_idx", columns={"offerId"}),
 *         @ORM\Index(name="offer_click_count_idx", columns={"offerId","onClick","counted"}),
 *         @ORM\Index(name="memberid_idx", columns={"memberId"})
 *     }
 * )
 */
class ViewCount
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $loadTime;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $onClick;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $onLoad;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $onHover;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $IP;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $memberId;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $counted;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Offer", inversedBy="offerviewcount")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id", onDelete="restrict")
     */
    private $viewcount;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}