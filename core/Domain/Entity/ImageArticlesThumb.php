<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class ImageArticlesThumb extends \Core\Domain\Entity\Image
{
    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Articles", mappedBy="thumbnail")
     */
    protected $articles;
    
    public function __get($property)
    {
        return parent::__get($property);
    }

    public function __set($property, $value)
    {
        parent::__set($property, $value);
    }
}