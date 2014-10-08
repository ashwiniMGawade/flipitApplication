<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="term_and_condition", indexes={@ORM\Index(name="offerid_idx", columns={"offerId"})})
 */
class TermAndCondition
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Offer", inversedBy="offertermandcondition")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id", onDelete="restrict")
     */
    private $termandcondition;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}