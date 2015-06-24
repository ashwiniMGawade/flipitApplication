<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="widget_location")
 */
class WidgetLocation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $position;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $pagetype;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $location;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $relatedid;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $widgettype;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated_at;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}
