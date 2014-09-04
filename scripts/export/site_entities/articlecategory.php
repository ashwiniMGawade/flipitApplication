<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="articlecategory",
 *     indexes={@ORM\Index(name="categoryiconid_idx", columns={"categoryiconid"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="categoryiconid", columns={"categoryiconid"})}
 * )
 */
class articlecategory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="blob", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
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
     * @ORM\OneToMany(targetEntity="moneysaving", mappedBy="articlecategory")
     */
    private $moneysaving;

    /**
     * @ORM\ManyToOne(targetEntity="image", inversedBy="ArtCatIcon")
     * @ORM\JoinColumn(name="categoryiconid", referencedColumnName="id", onDelete="restrict")
     */
    private $artImage;

    /**
     * @ORM\ManyToMany(targetEntity="category", mappedBy="articlecategory")
     */
    private $category;
}