<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="popular_category")
 */
class PopularCategory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Category", inversedBy="popularCategory")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    private $total_offers;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    private $total_coupons;


    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}