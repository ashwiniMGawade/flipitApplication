<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="widget")
 */
class Widget
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $function_name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $status;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $userDefined;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $showWithDefault;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $deleted;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefPageWidget", mappedBy="page")
     */
    protected $Widget;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\PageWidgets", mappedBy="widget")
     */
    private $pageWidgets;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}