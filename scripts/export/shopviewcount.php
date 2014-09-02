<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="shopviewcount", indexes={@ORM\Index(name="shopid_idx", columns={"shopid"})})
 */
class shopviewcount
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
    private $shopid;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $onclick;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $onload;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ip;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $deleted;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
     */
    private $updated_at;
}