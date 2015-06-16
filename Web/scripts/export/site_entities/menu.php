<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="menu")
 */
class menu
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
    private $name;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $parentId;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $root_id;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $lft;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $rgt;

    /**
     * @ORM\Column(type="integer", length=2, nullable=true)
     */
    private $level;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $iconId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $position;
}