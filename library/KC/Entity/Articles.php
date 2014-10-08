<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="articles", indexes={@ORM\Index(name="thumbnailid", columns={})})
 */
class Articles
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
    private $title;

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
     * @ORM\Column(type="string", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $publish;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $publishdate;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
     */
    private $authorid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $authorname;

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
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $thumbnailsmallid;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\ArticleChapter", mappedBy="article")
     */
    private $articleChapter;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\ArticleViewCount", mappedBy="articles")
     */
    private $articleviewcount;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\MoneysavingArticle", mappedBy="moneysaving")
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefArticleStore", mappedBy="relatedstores")
     */
    private $storearticles;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\ArticlesIcon", inversedBy="articles")
     * @ORM\JoinColumn(name="articles_icon_id", referencedColumnName="id")
     */
    private $articleImage;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\ArticlesThumb", inversedBy="articles")
     * @ORM\JoinColumn(name="thumbnailsmallid2", referencedColumnName="id")
     */
    private $thumbnail;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Category", inversedBy="articles")
     * @ORM\JoinTable(
     *     name="ref_article_category",
     *     joinColumns={@ORM\JoinColumn(name="articlesid", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="relatedcategoryid", referencedColumnName="id", nullable=false)}
     * )
     */
    private $category;
}