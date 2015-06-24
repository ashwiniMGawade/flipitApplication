<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="adminfavoriteshp")
 */
class Adminfavoriteshp
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    protected $userId;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\Shop", inversedBy="adminfevoriteshops")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    protected $shops;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}