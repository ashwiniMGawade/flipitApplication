<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="articles", indexes={@ORM\Index(name="thumbnailid", columns={"thumbnailid"})})
 */
class articles
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
     * @ORM\OneToMany(targetEntity="KC\Entity\article_chapter", mappedBy="article")
     */
    private $articleChapter;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\articleviewcount", mappedBy="articles")
     */
    private $articleviewcount;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\moneysaving_article", mappedBy="moneysaving")
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\ref_article_store", mappedBy="relatedstores")
     */
    private $storearticles;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\image", inversedBy="articleImage")
     * @ORM\JoinColumn(name="thumbnailid", referencedColumnName="id", onDelete="restrict")
     */
    private $imagearticle;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\category", inversedBy="articles")
     * @ORM\JoinTable(
     *     name="ref_article_category",
     *     joinColumns={@ORM\JoinColumn(name="articlesid", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="relatedcategoryid", referencedColumnName="id", nullable=false)}
     * )
     */
    private $category;
}