<?php
 
namespace KC\Entity;
 
use Doctrine\ORM\Mapping as ORM;
 
/**
 * Role
 *
 * @ORM\Table(name="role")
 * @ORM\Entity
 */
class Role
{
    /**
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
 
    /**
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;
 
    /**
     * 
     * @ORM\Column(name="deleted", type="string", length=255, nullable=true)
     */
    private $deleted;
    
    /** 
     * @ORM\OneToMany(targetEntity="KC\Entity\User", mappedBy="role")
     */
    private $role;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}