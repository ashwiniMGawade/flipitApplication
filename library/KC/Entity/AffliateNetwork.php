<?php
namespace KC\Entity;
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
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $status;

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
    private $subId;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\AffliateNetwork", mappedBy="affliate_networks")
     */
    private $affliate_network;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Shop", mappedBy="affliatenetwork")
     */
    private $affliatenetwork;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\AffliateNetwork", inversedBy="affliate_network")
     * @ORM\JoinColumn(name="replacewithId", referencedColumnName="id", onDelete="restrict")
     */
    private $affliate_networks;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}