<?php
namespace KC\Entity\User;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ref_user_website")
 */
class refUserWebsite
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\User\User", inversedBy="refUserWebsite")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    protected $websiteUsers;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\User\Website", inversedBy="websiteUsers")
     * @ORM\JoinColumn(name="websiteId", referencedColumnName="id")
     */
    protected $refUsersWebsite;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}