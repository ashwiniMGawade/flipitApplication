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
}