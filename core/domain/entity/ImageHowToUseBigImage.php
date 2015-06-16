<?php

namespace core\domain\entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ImageHowToUseBigImage extends \KC\Entity\Image
{
    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Shop", mappedBy="howtousebigimage")
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
