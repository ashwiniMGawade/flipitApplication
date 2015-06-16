<?php
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
class ref_shop_relatedshop
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
     */
    private $relatedshopId;

    /**
     * @ORM\Column(type="integer", length=5, nullable=false)
     */
    private $position;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="shop", inversedBy="relatedshops")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    private $shop;
}