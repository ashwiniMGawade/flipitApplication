<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ref_articlecategory_relatedcategory")
 */
class RefArticlecategoryRelatedcategory
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
     * @ORM\ManyToOne(targetEntity="KC\Entity\Articlecategory", inversedBy="refArticlecategoryRelatedcategory")
     * @ORM\JoinColumn(name="articlecategoryid", referencedColumnName="id")
     */
    private $articlecategory;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Category", inversedBy="refArticlecategoryRelatedcategory")
     * @ORM\JoinColumn(name="relatedcategoryid", referencedColumnName="id")
     */
    private $category;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}