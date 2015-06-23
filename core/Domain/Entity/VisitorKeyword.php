<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="visitor_keyword")
 */
class VisitorKeyword
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
    protected $keyword;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Visitor", inversedBy="visitorKeyword")
     * @ORM\JoinColumn(name="visitorId", referencedColumnName="id")
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