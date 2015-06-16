<?php

namespace core\domain\entity;

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
    protected $category;
    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Category", mappedBy="categoryFeaturedImage")
     */
    protected $categoryfeatured;
    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Category", mappedBy="categoryHeaderImage")
     */
    protected $categoryheader;

    public function __get($property)
    {
        return parent::__get($property);
    }

    public function __set($property, $value)
    {
        parent::__set($property, $value);
    }
}
