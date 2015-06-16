<?php
namespace core\domain\entity;
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
    protected $id;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    protected $position;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Offer", inversedBy="categoriesOffers")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
     */
    protected $offers;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Category", inversedBy="categoriesOffers")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id")
     */
    protected $categories;

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