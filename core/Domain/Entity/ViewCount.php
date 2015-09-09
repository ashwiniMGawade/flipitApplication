<?php
namespace Core\Domain\Entity;
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
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\Offer", inversedBy="offerviewcount")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id", onDelete="restrict")
     */
    protected $viewcount;

    /**
     * @return mixed
     */
    public function getIP()
    {
        return $this->IP;
    }

    /**
     * @param mixed $IP
     */
    public function setIP($IP)
    {
        $this->IP = $IP;
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
    public function getOnHover()
    {
        return $this->onHover;
    }

    /**
     * @param mixed $onHover
     */
    public function setOnHover($onHover)
    {
        $this->onHover = $onHover;
    }

    /**
     * @return mixed
     */
    public function getOnLoad()
    {
        return $this->onLoad;
    }

    /**
     * @param mixed $onLoad
     */
    public function setOnLoad($onLoad)
    {
        $this->onLoad = $onLoad;
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
    public function getCounted()
    {
        return $this->counted;
    }

    /**
     * @param mixed $counted
     */
    public function setCounted($counted)
    {
        $this->counted = $counted;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLoadTime()
    {
        return $this->loadTime;
    }

    /**
     * @param mixed $loadTime
     */
    public function setLoadTime($loadTime)
    {
        $this->loadTime = $loadTime;
    }

    /**
     * @return mixed
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * @param mixed $memberId
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;
    }

    /**
     * @return mixed
     */
    public function getOnClick()
    {
        return $this->onClick;
    }

    /**
     * @param mixed $onClick
     */
    public function setOnClick($onClick)
    {
        $this->onClick = $onClick;
    }

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}