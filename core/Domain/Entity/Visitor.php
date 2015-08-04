<?php
namespace Core\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="visitor",
 *     indexes={@ORM\Index(name="imageid_idx", columns={"imageId"}),@ORM\Index(name="createdby_idx", columns={"createdBy"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="visitoremail", columns={"email"})}
 * )
 */
class Visitor
{
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
    protected $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $pwd;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $gender;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $dateOfBirth;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $postalCode;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $weeklyNewsLetter;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $fashionNewsLetter;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $travelNewsLetter;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $codeAlert;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $createdBy;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $currentLogIn;

    /**
     * @ORM\Column(type="datetime", nullable=true)
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $interested;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $profile_img;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $active_codeid;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $active;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $changepasswordrequest;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $code_alert_send_date;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\Conversions", mappedBy="visitor")
     */
    protected $conversions;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\VisitorKeyword", mappedBy="visitor")
     */
    protected $visitorKeyword;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\VisitorImage", inversedBy="visitor")
     * @ORM\JoinColumn(name="imageid", referencedColumnName="id")
     */
    protected $visitorimage;

    /**
     * @ORM\ManyToMany(targetEntity="Core\Domain\Entity\Offer", mappedBy="visitors")
     */
    protected $offer;

    /**
     * @ORM\ManyToMany(targetEntity="Core\Domain\Entity\Shop", mappedBy="visitors")
     */
    protected $favoriteshops;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\FavoriteOffer", mappedBy="visitor")
     */
    protected $favoriteOffer;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\FavoriteShop", mappedBy="visitor")
     */
    protected $favoritevisitorshops;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    protected $mailClickCount;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    protected $mailOpenCount;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    protected $mailHardBounceCount;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    protected $mailSoftBounceCount;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $inactiveStatusReason;

    /**
     * @return mixed
     */
    public function getInactiveStatusReason()
    {
        return $this->inactiveStatusReason;
    }

    /**
     * @param mixed $inactiveStatusReason
     */
    public function setInactiveStatusReason($inactiveStatusReason)
    {
        $this->inactiveStatusReason = $inactiveStatusReason;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getActiveCodeid()
    {
        return $this->active_codeid;
    }

    /**
     * @param mixed $active_codeid
     */
    public function setActiveCodeid($active_codeid)
    {
        $this->active_codeid = $active_codeid;
    }

    /**
     * @return mixed
     */
    public function getChangepasswordrequest()
    {
        return $this->changepasswordrequest;
    }

    /**
     * @param mixed $changepasswordrequest
     */
    public function setChangepasswordrequest($changepasswordrequest)
    {
        $this->changepasswordrequest = $changepasswordrequest;
    }

    /**
     * @return mixed
     */
    public function getCodeAlert()
    {
        return $this->codeAlert;
    }

    /**
     * @param mixed $codeAlert
     */
    public function setCodeAlert($codeAlert)
    {
        $this->codeAlert = $codeAlert;
    }

    /**
     * @return mixed
     */
    public function getCodeAlertSendDate()
    {
        return $this->code_alert_send_date;
    }

    /**
     * @param mixed $code_alert_send_date
     */
    public function setCodeAlertSendDate($code_alert_send_date)
    {
        $this->code_alert_send_date = $code_alert_send_date;
    }

    /**
     * @return mixed
     */
    public function getConversions()
    {
        return $this->conversions;
    }

    /**
     * @param mixed $conversions
     */
    public function setConversions($conversions)
    {
        $this->conversions = $conversions;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getCurrentLogIn()
    {
        return $this->currentLogIn;
    }

    /**
     * @param mixed $currentLogIn
     */
    public function setCurrentLogIn($currentLogIn)
    {
        $this->currentLogIn = $currentLogIn;
    }

    /**
     * @return mixed
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @param mixed $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getFashionNewsLetter()
    {
        return $this->fashionNewsLetter;
    }

    /**
     * @param mixed $fashionNewsLetter
     */
    public function setFashionNewsLetter($fashionNewsLetter)
    {
        $this->fashionNewsLetter = $fashionNewsLetter;
    }

    /**
     * @return mixed
     */
    public function getFavoriteOffer()
    {
        return $this->favoriteOffer;
    }

    /**
     * @param mixed $favoriteOffer
     */
    public function setFavoriteOffer($favoriteOffer)
    {
        $this->favoriteOffer = $favoriteOffer;
    }

    /**
     * @return mixed
     */
    public function getFavoriteshops()
    {
        return $this->favoriteshops;
    }

    /**
     * @param mixed $favoriteshops
     */
    public function setFavoriteshops($favoriteshops)
    {
        $this->favoriteshops = $favoriteshops;
    }

    /**
     * @return mixed
     */
    public function getFavoritevisitorshops()
    {
        return $this->favoritevisitorshops;
    }

    /**
     * @param mixed $favoritevisitorshops
     */
    public function setFavoritevisitorshops($favoritevisitorshops)
    {
        $this->favoritevisitorshops = $favoritevisitorshops;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getInterested()
    {
        return $this->interested;
    }

    /**
     * @param mixed $interested
     */
    public function setInterested($interested)
    {
        $this->interested = $interested;
    }

    /**
     * @return mixed
     */
    public function getLastLogIn()
    {
        return $this->lastLogIn;
    }

    /**
     * @param mixed $lastLogIn
     */
    public function setLastLogIn($lastLogIn)
    {
        $this->lastLogIn = $lastLogIn;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getMailClickCount()
    {
        return $this->mailClickCount;
    }

    /**
     * @param mixed $mailClickCount
     */
    public function setMailClickCount($mailClickCount)
    {
        $this->mailClickCount = $mailClickCount;
    }

    /**
     * @return mixed
     */
    public function getMailHardBounceCount()
    {
        return $this->mailHardBounceCount;
    }

    /**
     * @param mixed $mailHardBounceCount
     */
    public function setMailHardBounceCount($mailHardBounceCount)
    {
        $this->mailHardBounceCount = $mailHardBounceCount;
    }

    /**
     * @return mixed
     */
    public function getMailOpenCount()
    {
        return $this->mailOpenCount;
    }

    /**
     * @param mixed $mailOpenCount
     */
    public function setMailOpenCount($mailOpenCount)
    {
        $this->mailOpenCount = $mailOpenCount;
    }

    /**
     * @return mixed
     */
    public function getMailSoftBounceCount()
    {
        return $this->mailSoftBounceCount;
    }

    /**
     * @param mixed $mailSoftBounceCount
     */
    public function setMailSoftBounceCount($mailSoftBounceCount)
    {
        $this->mailSoftBounceCount = $mailSoftBounceCount;
    }

    /**
     * @return mixed
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * @param mixed $offer
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param mixed $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return mixed
     */
    public function getProfileImg()
    {
        return $this->profile_img;
    }

    /**
     * @param mixed $profile_img
     */
    public function setProfileImg($profile_img)
    {
        $this->profile_img = $profile_img;
    }

    /**
     * @return mixed
     */
    public function getPwd()
    {
        return $this->pwd;
    }

    /**
     * @param mixed $pwd
     */
    public function setPwd($pwd)
    {
        $this->pwd = $pwd;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getTravelNewsLetter()
    {
        return $this->travelNewsLetter;
    }

    /**
     * @param mixed $travelNewsLetter
     */
    public function setTravelNewsLetter($travelNewsLetter)
    {
        $this->travelNewsLetter = $travelNewsLetter;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getVisitorKeyword()
    {
        return $this->visitorKeyword;
    }

    /**
     * @param mixed $visitorKeyword
     */
    public function setVisitorKeyword($visitorKeyword)
    {
        $this->visitorKeyword = $visitorKeyword;
    }

    /**
     * @return mixed
     */
    public function getVisitorimage()
    {
        return $this->visitorimage;
    }

    /**
     * @param mixed $visitorimage
     */
    public function setVisitorimage($visitorimage)
    {
        $this->visitorimage = $visitorimage;
    }

    /**
     * @return mixed
     */
    public function getWeeklyNewsLetter()
    {
        return $this->weeklyNewsLetter;
    }

    /**
     * @param mixed $weeklyNewsLetter
     */
    public function setWeeklyNewsLetter($weeklyNewsLetter)
    {
        $this->weeklyNewsLetter = $weeklyNewsLetter;
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
