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
 *     "HTUS"="KC\Entity\ImageHowToUseSmallImage",
 *     "HTUB"="KC\Entity\ImageHowToUseBigImage",
 *     "MI"="KC\Entity\MediaImage",
 *     "CATICON"="KC\Entity\ImageCategoryIcon",
 *     "VISITORPIC"="KC\Entity\VisitorImage",
 *     "ARTCATICON"="KC\Entity\ImageArticleCategoryIcon",
 *     "ARTICON"="KC\Entity\ImageArticlesIcon",
 *     "ARTTHUMB"="KC\Entity\ImageArticlesThumb",
 *     "SCREENSHOT"="KC\Entity\WebsiteScrenshot",
 *     "ARTFEATIMG"="KC\Entity\ImageArticleFeaturedImage"
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
     * @ORM\Column(type="integer", length=1, nullable=true)
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
     * @ORM\OneToMany(targetEntity="KC\Entity\Menu", mappedBy="menuIcon")
     */
    private $menu;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Mainmenu", mappedBy="mainMenuIcon")
     */
    private $mainmenu;

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
