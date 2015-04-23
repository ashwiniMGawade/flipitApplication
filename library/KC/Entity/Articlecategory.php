<?php
namespace KC\Entity;
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
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $permalink;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metatitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metadescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

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
     * @ORM\Column(type="string", nullable=false)
     */
    private $categorytitlecolor;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\MoneySaving", mappedBy="articlecategory")
     */
    private $moneysaving;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefArticleCategory", mappedBy="articlecategory")
     */
    private $refArticleCategory;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\ImageArticleCategoryIcon", inversedBy="articlecategory")
     * @ORM\JoinColumn(name="categoryiconid", referencedColumnName="id")
     */
    private $ArtCatIcon;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefArticlecategoryRelatedcategory", mappedBy="articlecategory")
     */
    private $refArticlecategoryRelatedcategory;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Category", mappedBy="articlecategory")
     */
    private $category;

    /**
    * @ORM\ManyToMany(targetEntity="KC\Entity\Articles", mappedBy="category")
    */
    private $articles;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}