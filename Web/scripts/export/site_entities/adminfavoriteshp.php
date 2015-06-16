<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="adminfavoriteshp")
 */
class adminfavoriteshp
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="shop", inversedBy="adminfevoriteshops")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    private $shops;
}