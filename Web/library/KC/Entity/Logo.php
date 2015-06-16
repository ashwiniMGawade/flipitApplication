<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class Logo extends \KC\Entity\Image
{
    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Offer", mappedBy="logo")
     */
    protected $offer;

    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Page", mappedBy="logo")
     */
    protected $page;

    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\SeenIn", mappedBy="logo")
     */
    protected $seenin;

    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Shop", mappedBy="logo")
     */
    protected $shop;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Page", mappedBy="homepageimage")
     */
    protected $homepageimage;

    public function __get($property)
    {
        return parent::__get($property);
    }

    public function __set($property, $value)
    {
        parent::__set($property, $value);
    }
}