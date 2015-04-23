<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ref_shop_category",
 *     indexes={
 *         @ORM\Index(name="shopid_idx", columns={"shopId"}),
 *         @ORM\Index(name="categoryid_idx", columns={"categoryId"}),
 *         @ORM\Index(name="shop_category_id_idx", columns={"shopId","categoryId"})
 *     }
 * )
 */
class RefShopCategory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $shopId;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $categoryId;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Category", inversedBy="shopcategory")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id", onDelete="restrict")
     */
    private $shop;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Shop", inversedBy="categoryshops")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id", onDelete="restrict")
     */
    private $category;
    
    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

}