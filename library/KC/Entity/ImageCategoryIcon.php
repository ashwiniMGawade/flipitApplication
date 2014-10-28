<?php

namespace KC\Entity;

use Doctrine\ORM\Mapping AS ORM;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class ImageCategoryIcon extends \KC\Entity\Image
{
    
    public function __construct()
    {
        $this->category = new ArrayCollection();
    }
    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Category", mappedBy="categoryicon")
     */
    private $category;
    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Category", mappedBy="categoryFeaturedImage")
     */
    private $categoryfeatured;
    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Category", mappedBy="categoryHeaderImage")
     */
    private $categoryheader;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
    
    public function test($value)
    {
        $this->type = $value;
    }

    public function testdel($value)
    {
        $this->deleted = $value;
    }

    public function testc($value)
    {
        $this->created_at = $value;
    }
    public function testcd($value)
    {
        $this->updated_at = $value;
    }

}