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
        return parent::__get($property);
    }

    public function __set($property, $value)
    {
        parent::__set($property, $value);
    }
}
