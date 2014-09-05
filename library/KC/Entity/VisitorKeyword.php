<?php
namespace KC\Entity;
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
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $keyword;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Visitor", inversedBy="visitorKeyword")
     * @ORM\JoinColumn(name="visitorId", referencedColumnName="id")
     */
    private $visitor;
}