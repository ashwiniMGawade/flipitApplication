<?php
namespace Core\Domain\Entity\User;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="website")
 */
class Website
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     */
    protected $chain;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\User\ChainItem", mappedBy="website")
     */
    protected $chainItem;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\User\refUserWebsite", mappedBy="refUsersWebsite")
     */
    protected $websiteUsers;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}