<?php
namespace core\domain\entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="category",
 *     indexes={
 *         @ORM\Index(name="categoryiconid_idx", columns={"categoryiconid"}),
 *         @ORM\Index(name="name", columns={"name"}),
 *         @ORM\Index(name="name_2", columns={"name"}),
 *         @ORM\Index(name="name_3", columns={"name"}),
 *         @ORM\Index(name="name_4", columns={"name"}),
 *         @ORM\Index(name="name_5", columns={"name"}),
 *         @ORM\Index(name="name_6", columns={"name"}),
 *         @ORM\Index(name="name_7", columns={"name"}),
 *         @ORM\Index(name="name_8", columns={"name"}),
 *         @ORM\Index(name="name_9", columns={"name"}),
 *         @ORM\Index(name="name_10", columns={"name"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="categoryiconid", columns={"categoryiconid"})}
 * )
 */
class Category
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
    protected $permaLink;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metatitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $featured_category;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Interestingcategory", mappedBy="category")
     */
    protected $interestingcategory;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\PopularCategory", mappedBy="category")
     */
    protected $popularCategory;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefArticlecategoryRelatedcategory", mappedBy="category")
     */
    protected $refArticlecategoryRelatedcategory;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefOfferCategory", mappedBy="categories")
     */
    protected $categoryoffres;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefShopCategory", mappedBy="shop")
     */

    protected $shopcategory;
    
    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\CategoriesOffers", mappedBy="categories")
     */
    protected $categoriesOffers;
    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\ImageCategoryIcon", inversedBy="category")
     * @ORM\JoinColumn(name="categoryiconid", referencedColumnName="id")
     */
    protected $categoryicon;
    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\ImageCategoryIcon", inversedBy="categoryfeatured")
     * @ORM\JoinColumn(name="categoryFeaturedImageId", referencedColumnName="id")
     */
    protected $categoryFeaturedImage;
    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\ImageCategoryIcon", inversedBy="categoryheader")
     * @ORM\JoinColumn(name="categoryHeaderImageId", referencedColumnName="id")
     */
    protected $categoryHeaderImage;
    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Articlecategory", inversedBy="category")
     * @ORM\JoinTable(
     *     name="ref_articlecategory_relatedcategory",
     *     joinColumns={@ORM\JoinColumn(name="relatedcategoryid", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="articlecategoryid", referencedColumnName="id", nullable=false)}
     * )
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