<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="couponcode",
 *     indexes={
 *         @ORM\Index(name="offerid_idx", columns={"offerid"}),
 *         @ORM\Index(name="couponcode_idx", columns={"offerid","status"})
 *     }
 * )
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
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $offerid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $status;
}