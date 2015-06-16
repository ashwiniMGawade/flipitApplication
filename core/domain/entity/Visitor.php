<?php
namespace core\domain\entity;
use Doctrine\ORM\Mapping AS ORM;

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
     * @ORM\OneToMany(targetEntity="KC\Entity\Conversions", mappedBy="visitor")
     */
    protected $conversions;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\VisitorKeyword", mappedBy="visitor")
     */
    protected $visitorKeyword;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\VisitorImage", inversedBy="visitor")
     * @ORM\JoinColumn(name="imageid", referencedColumnName="id")
     */
    protected $visitorimage;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Offer", mappedBy="visitors")
     */
    protected $offer;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Shop", mappedBy="visitors")
     */
    protected $favoriteshops;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\FavoriteOffer", mappedBy="visitor")
     */
    protected $favoriteOffer;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\FavoriteShop", mappedBy="visitor")
     */
    protected $favoritevisitorshops;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}