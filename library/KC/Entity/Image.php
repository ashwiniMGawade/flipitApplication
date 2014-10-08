<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="image", indexes={@ORM\Index(name="type_idx", columns={"type"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", length=10, type="string")
 * @ORM\DiscriminatorMap(
 *     {
 *     "LG"="KC\Entity\Logo",
 *     "HTUS"="KC\Entity\HowToUseSmallImage",
 *     "HTUB"="KC\Entity\HowToUseBigImage",
 *     "MI"="KC\Entity\MediaImage",
 *     "CATICON"="KC\Entity\CategoryIcon",
 *     "VISITORPIC"="KC\Entity\VisitorImage",
 *     "ARTCATICON"="KC\Entity\ArticleCategoryIcon",
 *     "ARTICON"="KC\Entity\ArticlesIcon",
 *     "ARTTHUMB"="KC\Entity\ArticlesThumb",
 *     "SCREENSHOT"="KC\Entity\WebsiteScrenshot"
 * }
 * )
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
     * 
     */
    private $seeninlogo;

    /**
     * 
     */
    private $ArtCatIcon;

    /**
     * 
     */
    private $articleImage;

    /**
     * 
     */
    private $categoryfeaturedimage;

    /**
     * 
     */
    private $categoryheaderimage;

    /**
     * 
     */
    private $categoryicon;

    /**
     * 
     */
    private $mediaimage;

    /**
     * 
     */
    private $logooffer;

    /**
     * 
     */
    private $pageheaderimage;

    /**
     * 
     */
    private $howtousebigimage;

    /**
     * 
     */
    private $smallimage;

    /**
     * 
     */
    private $logo;
}