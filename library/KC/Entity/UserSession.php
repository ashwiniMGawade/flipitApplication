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
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sessionid;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\User", inversedBy="user")
     * @ORM\JoinColumn(name="userid", referencedColumnName="id")
     */
    private $usersession;
}