<?php
namespace Core\Domain\Entity;
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
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\Articlecategory", inversedBy="refArticlecategoryRelatedcategory")
     * @ORM\JoinColumn(name="articlecategoryid", referencedColumnName="id")
     */
    protected $articlecategory;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\Category", inversedBy="refArticlecategoryRelatedcategory")
     * @ORM\JoinColumn(name="relatedcategoryid", referencedColumnName="id")
     */
    protected $category;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}