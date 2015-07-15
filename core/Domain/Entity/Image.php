<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="image", indexes={@ORM\Index(name="type_idx", columns={"type"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", length=10, type="string")
 * @ORM\DiscriminatorMap(
 *     {
 *     "LG"="Core\Domain\Entity\Logo",
 *     "HTUS"="Core\Domain\Entity\ImageHowToUseSmallImage",
 *     "HTUB"="Core\Domain\Entity\ImageHowToUseBigImage",
 *     "MI"="Core\Domain\Entity\MediaImage",
 *     "CATICON"="Core\Domain\Entity\ImageCategoryIcon",
 *     "VISITORPIC"="Core\Domain\Entity\VisitorImage",
 *     "ARTCATICON"="Core\Domain\Entity\ImageArticleCategoryIcon",
 *     "ARTICON"="Core\Domain\Entity\ImageArticlesIcon",
 *     "ARTTHUMB"="Core\Domain\Entity\ImageArticlesThumb",
 *     "SCREENSHOT"="Core\Domain\Entity\WebsiteScrenshot",
 *     "ARTFEATIMG"="Core\Domain\Entity\ImageArticleFeaturedImage"
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
    protected $id;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    protected $ext;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
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
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    protected $width;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    protected $height;


    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Menu", mappedBy="menuIcon")
     */
    protected $menu;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Mainmenu", mappedBy="mainMenuIcon")
     */
    protected $mainmenu;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
    
    public function getId()
    {
        return $this->id;
    }
}
