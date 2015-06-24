<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class MediaImage extends \Core\Domain\Entity\Image
{
    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Media", mappedBy="mediaimage")
     */
    protected $media;

    public function __get($property)
    {
        return parent::__get($property);
    }

    public function __set($property, $value)
    {
        parent::__set($property, $value);
    }
}