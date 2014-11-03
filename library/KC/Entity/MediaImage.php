<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class MediaImage extends \KC\Entity\Image
{
    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Media", mappedBy="mediaimage")
     */
    private $media;

    public function __get($property)
    {
        return parent::__get($property);
    }

    public function __set($property, $value)
    {
        parent::__set($property, $value);
    }
}