<?php
namespace KC\Entity;
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
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\User", inversedBy="refUserWebsite")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $websiteUsers;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Website", inversedBy="websiteUsers")
     * @ORM\JoinColumn(name="websiteId", referencedColumnName="id")
     */
    private $refUsersWebsite;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}