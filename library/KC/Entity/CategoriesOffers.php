<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories_offers")
 */
class CategoriesOffers
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
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
     * @ORM\ManyToOne(targetEntity="KC\Entity\Offer", inversedBy="categoriesOffers")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
     */
    private $offers;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Category", inversedBy="categoriesOffers")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id")
     */
    private $categories;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    public function getId()
    {
        return $this->id;
    }
}