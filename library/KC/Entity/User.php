<?php
 
namespace KC\Entity;
 
use Doctrine\ORM\Mapping as ORM;
 
/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User
{
    /**
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     */
    private $id;
 
    /**
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    private $firstname;
 
    /**
     * 
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}