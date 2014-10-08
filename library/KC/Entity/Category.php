<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="category",
 *     indexes={
 *         @ORM\Index(name="categoryiconid_idx", columns={}),
 *         @ORM\Index(name="name", columns={"name"}),
 *         @ORM\Index(name="name_2", columns={"name"}),
 *         @ORM\Index(name="name_3", columns={"name"}),
 *         @ORM\Index(name="name_4", columns={"name"}),
 *         @ORM\Index(name="name_5", columns={"name"}),
 *         @ORM\Index(name="name_6", columns={"name"}),
 *         @ORM\Index(name="name_7", columns={"name"}),
 *         @ORM\Index(name="name_8", columns={"name"}),
 *         @ORM\Index(name="name_9", columns={"name"}),
 *         @ORM\Index(name="name_10", columns={"name"}),
 *         @ORM\Index(name="categoryFeaturedImageId_foreign_key", columns={}),
 *         @ORM\Index(name="categoryHeaderImageId_foreign_key", columns={})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="categoryiconid", columns={})}
 * )
 */
class Category
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
    private $permaLink;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metatitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metaDescription;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $featured_category;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Interestingcategory", mappedBy="category")
     */
    private $interestingcategory;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\PopularCategory", mappedBy="category")
     */
    private $popularCategory;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefOfferCategory", mappedBy="offer")
     */
    private $categoryoffres;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefShopCategory", mappedBy="shop")
     */
    private $shopcategory;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\CategoryIcon", inversedBy="category")
     * @ORM\JoinColumn(name="categoryIconId", referencedColumnName="id")
     */
    private $categoryicon;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\ArticleCategoryIcon", inversedBy="category")
     * @ORM\JoinColumn(name="categoryIconId2", referencedColumnName="id")
     */
    private $articlecategoryicon;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Articlecategory", inversedBy="category")
     * @ORM\JoinTable(
     *     name="ref_articlecategory_relatedcategory",
     *     joinColumns={@ORM\JoinColumn(name="relatedcategoryid", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="articlecategoryid", referencedColumnName="id", nullable=false)}
     * )
     */
    private $articlecategory;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Articles", mappedBy="category")
     */
    private $articles;
}