<?php
 
namespace KC\Entity;
 
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User
{
    public function __construct()
    {
        $this->userwebsite = new ArrayCollection();
    }

    /**
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
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

    /**
     * Unidirectional - Many-To-One
     *
     * @ORM\ManyToOne(targetEntity="KC\Entity\Role", inversedBy="role")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="roleid", referencedColumnName="id")
     * })
     */
    private $role;

    /**
     * @ORM\ManyToMany(targetEntity="Website", inversedBy="refwebsite")
     * @ORM\JoinTable(name="ref_user_website")
     **/
    private $userwebsite;


    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
    private $tags;

    public function addWebsite(Website $tag)
    {
        $tag->addArticle($this); // synchronously updating inverse side
        $this->tags[] = $tag;
    }
}