<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ref_offer_page",
 *     indexes={
 *         @ORM\Index(name="offerid_idx", columns={"offerId"}),
 *         @ORM\Index(name="pageid_idx", columns={"pageId"}),
 *         @ORM\Index(name="offer_page_id_idx", columns={"pageId","offerId"})
 *     }
 * )
 */
class RefOfferPage
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
     * @ORM\ManyToOne(targetEntity="KC\Entity\Offer", inversedBy="offers")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $refoffers;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Page", inversedBy="pageoffers")
     * @ORM\JoinColumn(name="pageId", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $offers;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}