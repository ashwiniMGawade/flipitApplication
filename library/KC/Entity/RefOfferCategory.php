<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ref_offer_category",
 *     indexes={
 *         @ORM\Index(name="offerid_idx", columns={"offerId"}),
 *         @ORM\Index(name="categoryid_idx", columns={"categoryId"}),
 *         @ORM\Index(name="offer_category_id_idx", columns={"categoryId","offerId"})
 *     }
 * )
 */
class RefOfferCategory
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
     * @ORM\ManyToOne(targetEntity="KC\Entity\Category", inversedBy="categoryoffres")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $categories;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Offer", inversedBy="categoryoffres")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id", nullable=false, onDelete="restrict")
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