<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_session")
 */
class UserSession
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $sessionId;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\User", inversedBy="user")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    protected $usersession;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}