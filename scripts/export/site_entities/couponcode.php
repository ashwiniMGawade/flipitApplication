<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="couponcode", indexes={@ORM\Index(name="couponcode_idx", columns={"status"})})
 */
class couponcode
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
     * @ORM\ManyToOne(targetEntity="offer", inversedBy="couponcode")
     * @ORM\JoinColumn(name="offerid", referencedColumnName="id")
     */
    private $offer;
}