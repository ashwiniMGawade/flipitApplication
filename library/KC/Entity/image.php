<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="image", indexes={@ORM\Index(name="type_idx", columns={"type"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", length=10, type="string")
 * @ORM\DiscriminatorMap({""="KC\Entity\Logo"})
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $ext;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

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
     * @ORM\OneToOne(targetEntity="KC\Entity\SeenIn", mappedBy="seenin")
     */
    private $seeninlogo;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Articlecategory", mappedBy="artImage")
     */
    private $ArtCatIcon;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Articles", mappedBy="imagearticle")
     */
    private $articleImage;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Category", mappedBy="category")
     */
    private $categoryfeaturedimage;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Category", mappedBy="headerimagecategory")
     */
    private $categoryheaderimage;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Category", mappedBy="iconcategory")
     */
    private $categoryicon;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Media", mappedBy="media")
     */
    private $mediaimage;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Offer", mappedBy="logooffer")
     */
    private $logooffer;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Page", mappedBy="logo")
     */
    private $pageheaderimage;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Shop", mappedBy="shops")
     */
    private $howtousebigimage;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Shop", mappedBy="shopimage")
     */
    private $smallimage;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Shop", mappedBy="shoplogo")
     */
    private $logo;
}