<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

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
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastName;

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
    private $currentLogIn;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $lastLogIn;

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
    private $popularKortingscode;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordChangeTime;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $countryLocale;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $editorText;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\UserSession", mappedBy="usersession")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\ProfileImage", inversedBy="user")
     * @ORM\JoinColumn(name="profileimageid", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $profileimage;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Role", inversedBy="roleid")
     * @ORM\JoinColumn(name="roleid", referencedColumnName="id", onDelete="restrict")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Website", mappedBy="user")
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


    public function getId()
    {
        return $this->id;
    }
    
    public function validatePassword($passwordToBeVerified)
    {
        if ($this->password == md5($passwordToBeVerified))
        {
           return true;
        }
        return false;
    }

    public function isPasswordDifferent($newPassword)
    {

        if($this->password === md5($newPassword)) {
            return false;
        }
        return true ;
    }
   
    public function validateEmail($emailToBeVerified)
    {
        if ($this->email == ($emailToBeVerified)) {
            return true;
        }
        return false;
    }

    public function setPassword($password)
    {
            $this->_set('password', md5($password));
            $this->_set('passwordChangeTime', date("Y-m-d H:i:s"));
    }

    public function updateLoginTime($id)
    {
        $entityManagerUser = \Zend_Registry::get('emUser');
        $user = $entityManagerUser->find('KC\Entity\User', $id);
        if ($user->currentLogIn == '0000-00-00 00:00:00') {
            $user->currentLogIn = new \DateTime('now');
        }
        $user->lastLogIn = $user->currentLogIn;
        $user->currentLogIn = new \DateTime('now');
        $entityManagerUser->persist($user);
        $entityManagerUser->flush();
    }
}
