<?php

namespace Core\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ImageHowToUseBigImage extends \Core\Domain\Entity\Image
{
    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Shop", mappedBy="howtousebigimage")
     */
    protected $shop;

    public function __get($property)
    {
        return parent::__get($property);
    }

    public function __set($property, $value)
    {
        parent::__set($property, $value);
    }
}
