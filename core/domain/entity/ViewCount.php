<?php
namespace core\domain\entity;
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
    protected $id;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $loadTime;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $onClick;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $onLoad;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $onHover;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $IP;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $memberId;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $counted = 0;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Offer", inversedBy="offerviewcount")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id", onDelete="restrict")
     */
    protected $viewcount;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}