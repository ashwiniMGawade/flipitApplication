<?php
namespace Core\Domain\Entity;
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
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\Offer", inversedBy="offers")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    protected $refoffers;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\Page", inversedBy="pageoffers")
     * @ORM\JoinColumn(name="pageId", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    protected $offers;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}