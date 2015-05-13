<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class ImageArticleCategoryIcon extends \KC\Entity\Image
{
    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Articlecategory", mappedBy="ArtCatIcon")
     */
    private $articlecategory;
    
    public function __get($property)
    {
        return parent::__get($property);
    }

    public function __set($property, $value)
    {
        parent::__set($property, $value);
    }
}