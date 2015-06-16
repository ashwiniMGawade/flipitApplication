<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="mainmenu")
 */
class Mainmenu
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $parentId;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $root_id;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $lft;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $rgt;

    /**
     * @ORM\Column(type="integer", length=2, nullable=true)
     */
    protected $level;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $iconId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    protected $position;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Image", inversedBy="mainmenu")
     * @ORM\JoinColumn(name="iconId", referencedColumnName="id")
     */
    protected $mainMenuIcon;
    
    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}