<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;
use \Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="user",
 *     indexes={
 *         @ORM\Index(name="roleid_idx", columns={"roleId"}),
 *         @ORM\Index(name="profileimageid_idx", columns={"profileImageId"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})}
 * )
 */
class User
{
    public function __construct()
    {
        $this->refUserWebsite = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $password;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $google;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $twitter;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $pinterest;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $likes;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $dislike;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $mainText;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $createdBy;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $currentLogIn;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $lastLogIn;

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
    protected $showInAboutListing;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $slug;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $addtosearch;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $popularKortingscode;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $passwordChangeTime;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $countryLocale;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $editorText;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\UserSession", mappedBy="usersession")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\refUserWebsite", mappedBy="websiteUsers", cascade={"persist", "remove"})
     */
    protected $refUserWebsite;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\ProfileImage", inversedBy="user")
     * @ORM\JoinColumn(name="profileImageId", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    protected $profileimage;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Role", inversedBy="roleid")
     * @ORM\JoinColumn(name="roleId", referencedColumnName="id", onDelete="restrict")
     */
    protected $users;

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
        if ($this->password == md5($passwordToBeVerified)) {
            return true;
        }
        return false;
    }

    public function isPasswordDifferent($newPassword)
    {

        if ($this->password === md5($newPassword)) {
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
    //it will be reome after testing
    /*public function setPassword($password)
    {
        $this->_set('password', md5($password));
        $this->_set('passwordChangeTime', date("Y-m-d H:i:s"));
    }*/

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

    public function getPermissions()
    {
        if (intval($this->id) > 0) {
            $perm = $genralPermission =  array();
            
            $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
            $query = $queryBuilder->select('u, r')
                ->from('KC\Entity\User', 'u')
                ->leftJoin('u.users', 'r')
                ->where($queryBuilder->expr()->eq('u.id', $this->id));
            $role = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
          
            $perm['roles'] = $role[0]['users'];

            unset($perm['roles']['created_at']);
            unset($perm['roles']['updated_at']);

            $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
            $query = $queryBuilder->select('u, r, rt')
                ->from('KC\Entity\User', 'u')
                ->leftJoin('u.users', 'r')
                ->leftJoin('r.rights', 'rt')
                ->where($queryBuilder->expr()->eq('u.id', $this->id));
            $rights = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            $perm['rights'] = $rights[0]['users']['rights'];

            for ($i=0; $i < count($perm['rights']); $i++) {
                unset($perm['rights'][$i]['created_at']);
                unset($perm['rights'][$i]['updated_at']);
                unset($perm['rights'][$i]['id']);
                unset($perm['rights'][$i]['roleId']);
                $perm['rights'][$perm['rights'][$i]['name']]= $perm['rights'][$i];
                unset($perm['rights'][$i]);
            }
            
            $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
            $query = $queryBuilder
                ->select('w.id as websiteid, u.id, w.name, w.created_at, w.updated_at, w.url')
                ->from('KC\Entity\User', 'u')
                ->leftJoin('u.refUserWebsite', 'rf')
                ->leftJoin('rf.refUsersWebsite', 'w')
                ->where($queryBuilder->expr()->eq('u.id', $this->id));
            $websites = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            $perm['webaccess'] = $websites;

            for ($i=0; $i < count($perm['webaccess']); $i++) {
                //unset($perm['webaccess'][$i]['id']);
                unset($perm['webaccess'][$i]['id']);
                unset($perm['webaccess'][$i]['created_at']);
                unset($perm['webaccess'][$i]['updated_at']);
                
                $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
                $query = $queryBuilder->select('w.name')
                    ->from('KC\Entity\Website', 'w')
                    ->where($queryBuilder->expr()->eq('w.id', $perm['webaccess'][$i]['websiteid']))
                    ->andWhere($queryBuilder->expr()->eq('w.status', $queryBuilder->expr()->literal('online')))
                    ->orderBy('w.name', 'ASC');
                $q = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                
                $perm['webaccess'][$i]['websitename'] = $q['0']['name'];
            }
             # rearange websites based on website name and keep kortingscode at same place
             $data = $perm['webaccess'];
             $data = \BackEnd_Helper_viewHelper::msort($data, array('websitename'), "kortingscode.nl");
             $perm['webaccess'] = $data;
             return $perm;
        }
        return null ;
    }
}