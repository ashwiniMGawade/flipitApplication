<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="chain", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 */
class Chain
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Website", inversedBy="chain")
     * @ORM\JoinTable(
     *     name="chain_item",
     *     joinColumns={@ORM\JoinColumn(name="chainId", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="websiteId", referencedColumnName="id", nullable=false)}
     * )
     */
    private $website;
}