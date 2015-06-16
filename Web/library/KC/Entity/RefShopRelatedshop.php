<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ref_shop_relatedshop",
 *     indexes={
 *         @ORM\Index(name="shop_relatedshop_id_idx", columns={"relatedshopId"}),
 *         @ORM\Index(name="shop_relatedshop_idx", columns={"relatedshopId"})
 *     }
 * )
 */
class RefShopRelatedshop
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
     */
    protected $relatedshopId;

    /**
     * @ORM\Column(type="integer", length=5, nullable=false)
     */
    protected $position;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Shop", inversedBy="relatedshops")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    protected $shop;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}