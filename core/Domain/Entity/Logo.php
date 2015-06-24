<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class Logo extends \Core\Domain\Entity\Image
{
    /**
     * @ORM\OneToOne(targetEntity="Core\Domain\Entity\Offer", mappedBy="logo")
     */
    protected $offer;

    /**
     * @ORM\OneToOne(targetEntity="Core\Domain\Entity\Page", mappedBy="logo")
     */
    protected $page;

    /**
     * @ORM\OneToOne(targetEntity="Core\Domain\Entity\SeenIn", mappedBy="logo")
     */
    protected $seenin;

    /**
     * @ORM\OneToOne(targetEntity="Core\Domain\Entity\Shop", mappedBy="logo")
     */
    protected $shop;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Page", mappedBy="homepageimage")
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