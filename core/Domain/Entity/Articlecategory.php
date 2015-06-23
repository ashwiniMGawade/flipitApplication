<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="articlecategory"
 *
 * )
 */
class Articlecategory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $permalink;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metatitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metadescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $categorytitlecolor;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\MoneySaving", mappedBy="articlecategory")
     */
    protected $moneysaving;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefArticleCategory", mappedBy="articlecategory")
     */
    protected $refArticleCategory;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\ImageArticleCategoryIcon", inversedBy="articlecategory")
     * @ORM\JoinColumn(name="categoryiconid", referencedColumnName="id")
     */
    protected $ArtCatIcon;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefArticlecategoryRelatedcategory", mappedBy="articlecategory")
     */
    protected $refArticlecategoryRelatedcategory;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Category", mappedBy="articlecategory")
     */
    protected $category;

    /**
    * @ORM\ManyToMany(targetEntity="KC\Entity\Articles", mappedBy="category")
    */
    protected $articles;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}