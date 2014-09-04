<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="affliate_network", indexes={@ORM\Index(name="replacewithid_idx", columns={"replacewithid"})})
 */
class affliate_network
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
     * @ORM\OneToMany(targetEntity="affliate_network", mappedBy="affliate_networks")
     */
    private $affliate_network;

    /**
     * @ORM\OneToMany(targetEntity="shop", mappedBy="affliatenetwork")
     */
    private $affliatenetwork;

    /**
     * @ORM\ManyToOne(targetEntity="affliate_network", inversedBy="affliate_network")
     * @ORM\JoinColumn(name="replacewithid", referencedColumnName="id", onDelete="restrict")
     */
    private $affliate_networks;
}