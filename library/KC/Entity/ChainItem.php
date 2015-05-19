<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="chain_item",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="unique_shopname_website_idx", columns={"shopName"})}
 * )
 */
class ChainItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $shopName;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", unique=true, length=255, nullable=true)
     */
    private $permalink;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $locale;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $shopId;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Chain", inversedBy="chainItem")
     * @ORM\JoinColumn(name="chainId", referencedColumnName="id")
     */
    private $chainItem;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Website", inversedBy="chainItem")
     * @ORM\JoinColumn(name="websiteId", referencedColumnName="id")
     */
    private $website;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
    
    public function getId()
    {
        return $this->id;
    }
}