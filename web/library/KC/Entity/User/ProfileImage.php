<?php
namespace KC\Entity\User;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="profile_image")
 */
class ProfileImage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    protected $ext;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\User\User", mappedBy="profileimage")
     */
    protected $user;

    public function getId()
    {
        return $this->id;
    }

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}