<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="votes")
 */
class Votes
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    protected $ipAddress;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $date;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    protected $vote;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    protected $moneySaved;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $product;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    protected $status;

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
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $visitorId;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\Offer", inversedBy="votes")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
     */
    protected $offer;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}