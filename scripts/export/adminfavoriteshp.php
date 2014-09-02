<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="adminfavoriteshp", indexes={@ORM\Index(name="shopId_idx", columns={"shopId"})})
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
    private $shopId;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $userId;
}