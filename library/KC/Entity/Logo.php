<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class Logo extends \KC\Entity\Image
{
    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Shop", mappedBy="logo")
     */
    private $shop;

    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\SeenIn", mappedBy="logo")
     */
    private $seenin;

    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Page", mappedBy="logo")
     */
    private $page;

    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Offer", mappedBy="logo")
     */
    private $offer;
}