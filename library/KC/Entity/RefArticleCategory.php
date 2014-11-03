<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ref_article_category")
 */
class RefArticleCategory
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
     * 
     * 
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Articles", inversedBy="refArticleCategory")
     * @ORM\JoinColumn(name="articleid", referencedColumnName="id")
     */
    private $articles;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Articlecategory", inversedBy="refArticleCategory")
     * @ORM\JoinColumn(name="relatedcategoryid", referencedColumnName="id")
     */
    private $articlecategory;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}