<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="favorite_shop",
 *     indexes={
 *         @ORM\Index(name="ind_fvshop_sid", columns={"shopId"}),
 *         @ORM\Index(name="fav_cascade", columns={"visitorId"}),
 *         @ORM\Index(name="shop_visitor_id_idx", columns={"shopId","visitorId"})
 *     }
 * )
 */
class favorite_shop
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
    private $shopId;

    /**
     * @ORM\Column(type="unknown:@timestamp_f", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $deleted;
}