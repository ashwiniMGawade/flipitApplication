<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="special_pages_offers")
 */
class SpecialPagesOffers
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Offer", inversedBy="specialPagesOffers")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
     */
    private $offers;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Page", inversedBy="specialPagesOffers")
     * @ORM\JoinColumn(name="pageId", referencedColumnName="id")
     */
    private $pages;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}