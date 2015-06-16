<?php
namespace domain\entity\user;
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
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $shopName;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="string", unique=true, length=255, nullable=true)
     */
    protected $permalink;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $locale;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $shopId;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\User\Chain", inversedBy="chainItem")
     * @ORM\JoinColumn(name="chainId", referencedColumnName="id")
     */
    protected $chainItem;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\User\Website", inversedBy="chainItem")
     * @ORM\JoinColumn(name="websiteId", referencedColumnName="id")
     */
    protected $website;

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