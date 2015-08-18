<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="affliate_network", indexes={@ORM\Index(name="replacewithid_idx", columns={"replacewithId"})})
 */
class AffliateNetwork
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $status;

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
    protected $subId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $extendedSubid;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\AffliateNetwork", mappedBy="affliate_networks")
     */
    protected $affliate_network;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Shop", mappedBy="affliatenetwork")
     */
    protected $affliatenetwork;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\AffliateNetwork", inversedBy="affliate_network")
     * @ORM\JoinColumn(name="replacewithId", referencedColumnName="id", onDelete="restrict")
     */
    protected $affliate_networks;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * @return mixed
     */
    public function getAffliateNetwork()
    {
        return $this->affliate_network;
    }

    /**
     * @param mixed $affliate_network
     */
    public function setAffliateNetwork($affliate_network)
    {
        $this->affliate_network = $affliate_network;
    }

    /**
     * @return mixed
     */
    public function getAffliateNetworks()
    {
        return $this->affliate_networks;
    }

    /**
     * @param mixed $affliate_networks
     */
    public function setAffliateNetworks($affliate_networks)
    {
        $this->affliate_networks = $affliate_networks;
    }

    /**
     * @return mixed
     */
    public function getAffliateNetworkShop()
    {
        return $this->affliatenetwork;
    }

    /**
     * @param mixed $affliatenetwork
     */
    public function setAffliateNetworkShop($affliatenetwork)
    {
        $this->affliatenetwork = $affliatenetwork;
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
    public function getExtendedSubid()
    {
        return $this->extendedSubid;
    }

    /**
     * @param mixed $extendedSubid
     */
    public function setExtendedSubid($extendedSubid)
    {
        $this->extendedSubid = $extendedSubid;
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
    public function getSubId()
    {
        return $this->subId;
    }

    /**
     * @param mixed $subId
     */
    public function setSubId($subId)
    {
        $this->subId = $subId;
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
}