<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="couponcode", indexes={@ORM\Index(name="couponcode_idx", columns={"status"})})
 */
class CouponCode
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Offer", inversedBy="couponcode")
     * @ORM\JoinColumn(name="offerid", referencedColumnName="id")
     */
    private $offer;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}