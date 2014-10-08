<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class ArticlesIcon extends \KC\Entity\Image
{
    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Articles", mappedBy="articleImage")
     */
    private $articles;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}