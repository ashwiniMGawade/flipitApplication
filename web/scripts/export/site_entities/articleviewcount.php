<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="articleviewcount")
 */
class articleviewcount
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
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
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\ManyToOne(targetEntity="articles", inversedBy="articleviewcount")
     * @ORM\JoinColumn(name="articleid", referencedColumnName="id")
     */
    private $articles;
}