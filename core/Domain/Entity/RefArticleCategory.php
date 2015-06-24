<?php
namespace Core\Domain\Entity;
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
     * 
     * 
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\Articles", inversedBy="refArticleCategory")
     * @ORM\JoinColumn(name="articleid", referencedColumnName="id")
     */
    protected $articles;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\Articlecategory", inversedBy="refArticleCategory")
     * @ORM\JoinColumn(name="relatedcategoryid", referencedColumnName="id")
     */
    protected $articlecategory;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}