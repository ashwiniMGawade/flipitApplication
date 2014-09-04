<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="image", indexes={@ORM\Index(name="type_idx", columns={"type"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", length=10, type="string")
 * @ORM\DiscriminatorMap({""="logo"})
 */
class image
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
     * @ORM\OneToOne(targetEntity="seen_in", mappedBy="seenin")
     */
    private $seeninlogo;

    /**
     * @ORM\OneToMany(targetEntity="articlecategory", mappedBy="artImage")
     */
    private $ArtCatIcon;

    /**
     * @ORM\OneToMany(targetEntity="articles", mappedBy="imagearticle")
     */
    private $articleImage;

    /**
     * @ORM\OneToMany(targetEntity="category", mappedBy="category")
     */
    private $categoryfeaturedimage;

    /**
     * @ORM\OneToMany(targetEntity="category", mappedBy="headerimagecategory")
     */
    private $categoryheaderimage;

    /**
     * @ORM\OneToMany(targetEntity="category", mappedBy="iconcategory")
     */
    private $categoryicon;

    /**
     * @ORM\OneToMany(targetEntity="media", mappedBy="media")
     */
    private $mediaimage;

    /**
     * @ORM\OneToMany(targetEntity="offer", mappedBy="logooffer")
     */
    private $logooffer;

    /**
     * @ORM\OneToMany(targetEntity="page", mappedBy="logo")
     */
    private $pageheaderimage;

    /**
     * @ORM\OneToMany(targetEntity="shop", mappedBy="shops")
     */
    private $howtousebigimage;

    /**
     * @ORM\OneToMany(targetEntity="shop", mappedBy="shopimage")
     */
    private $smallimage;

    /**
     * @ORM\OneToMany(targetEntity="shop", mappedBy="shoplogo")
     */
    private $logo;
}