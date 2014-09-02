<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ref_offer_category",
 *     indexes={
 *         @ORM\Index(name="offerid_idx", columns={"offerid"}),
 *         @ORM\Index(name="categoryid_idx", columns={"categoryid"}),
 *         @ORM\Index(name="offer_category_id_idx", columns={"categoryid","offerid"})
 *     }
 * )
 */
class ref_offer_category
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $updated_at;
}