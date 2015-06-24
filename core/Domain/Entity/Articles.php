<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="articles", indexes={@ORM\Index(name="thumbnailid", columns={"thumbnailid"})})
 */
class Articles
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $publish;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $publishdate;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
     */
    protected $authorid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $authorname;

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
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $thumbnailsmallid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $plusTitle;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $featuredImageStatus;
    
    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\ArticleChapter", mappedBy="article")
     */
    protected $articleChapter;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\ArticleViewCount", mappedBy="articles")
     */
    protected $articleviewcount;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\MoneysavingArticle", mappedBy="moneysaving")
     */
    protected $articles;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\RefArticleStore", mappedBy="relatedstores")
     */
    protected $storearticles;
    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\ImageArticleFeaturedImage", inversedBy="articles")
     * @ORM\JoinColumn(name="featuredImage", referencedColumnName="id")
     */
    protected $featuredImage;
    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\ImageArticlesIcon", inversedBy="articles")
     * @ORM\JoinColumn(name="thumbnailid", referencedColumnName="id")
     */
    protected $articleImage;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\ImageArticlesThumb", inversedBy="articles")
     * @ORM\JoinColumn(name="thumbnailsmallid", referencedColumnName="id")
     */
    protected $thumbnail;

    /**
     * @ORM\ManyToMany(targetEntity="Core\Domain\Entity\ArticleCategory", inversedBy="articles")
     * @ORM\JoinTable(
     *     name="ref_article_category",
     *     joinColumns={@ORM\JoinColumn(name="articleid", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="relatedcategoryid", referencedColumnName="id", nullable=false)}
     * )
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\RefArticleCategory", mappedBy="articles")
     */
    protected $refArticleCategory;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\PopularArticles", mappedBy="articles")
     */
    protected $populararticles;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}