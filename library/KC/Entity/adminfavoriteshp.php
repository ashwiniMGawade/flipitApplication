<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="adminfavoriteshp")
 */
class Adminfavoriteshp
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
     * @ORM\ManyToOne(targetEntity="KC\Entity\Shop", inversedBy="adminfevoriteshops")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    private $shops;
}