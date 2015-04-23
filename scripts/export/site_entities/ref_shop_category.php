<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ref_shop_category",
 *     indexes={
 *         @ORM\Index(name="shopid_idx", columns={"shopid"}),
 *         @ORM\Index(name="categoryid_idx", columns={"categoryid"}),
 *         @ORM\Index(name="shop_category_id_idx", columns={"shopid","categoryid"})
 *     }
 * )
 */
class ref_shop_category
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="category", inversedBy="shopcategory")
     * @ORM\JoinColumn(name="categoryid", referencedColumnName="id", onDelete="restrict")
     */
    private $shop;

    /**
     * @ORM\ManyToOne(targetEntity="shop", inversedBy="categoryshops")
     * @ORM\JoinColumn(name="shopid", referencedColumnName="id", onDelete="restrict")
     */
    private $category;
}