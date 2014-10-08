<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class ArticleCategoryIcon extends \KC\Entity\Image
{
    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Category", mappedBy="articlecategoryicon")
     */
    private $category;
}