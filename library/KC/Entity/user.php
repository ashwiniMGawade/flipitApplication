<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="user",
 *     indexes={
 *         @ORM\Index(name="roleid_idx", columns={"roleid"}),
 *         @ORM\Index(name="profileimageid_idx", columns={"profileimageid"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})}
 * )
 */
class user
{
    public function __construct()
    {
        $this->userWebsite = new ArrayCollection();
        $this->user = new ArrayCollection();
    }
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $google;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $twitter;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $pinterest;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $likes;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $dislike;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $mainText;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $createdby;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $currentlogin;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $lastlogin;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $showInAboutListing;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $addtosearch;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $popularkortingscode;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordchangetime;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $countryLocale;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $editorText;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\profile_image", inversedBy="profileimage")
     * @ORM\JoinColumn(name="profileimageid", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $user;

    /**
     * 
     * 
     */
    private $userxyz;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\role", inversedBy="role")
     * @ORM\JoinColumn(name="roleid", referencedColumnName="id", onDelete="restrict")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\website", mappedBy="user")
     */
    private $website;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}