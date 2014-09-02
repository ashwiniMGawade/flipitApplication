<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ref_offer_page",
 *     indexes={
 *         @ORM\Index(name="offerid_idx", columns={"offerid"}),
 *         @ORM\Index(name="pageid_idx", columns={"pageid"}),
 *         @ORM\Index(name="offer_page_id_idx", columns={"pageid","offerid"})
 *     }
 * )
 */
class ref_offer_page
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