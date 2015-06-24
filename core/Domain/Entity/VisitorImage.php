<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class VisitorImage extends \Core\Domain\Entity\Image
{
    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Visitor", mappedBy="visitorimage")
     */
    protected $visitor;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}