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
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="offer", inversedBy="offers")
     * @ORM\JoinColumn(name="offerid", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $refoffers;

    /**
     * @ORM\ManyToOne(targetEntity="page", inversedBy="pageoffers")
     * @ORM\JoinColumn(name="pageid", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $offers;
}