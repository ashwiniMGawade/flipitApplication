<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="visitor",
 *     indexes={@ORM\Index(name="imageid_idx", columns={"imageId"}),@ORM\Index(name="createdby_idx", columns={"createdBy"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})}
 * )
 */
class Visitor
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
    private $lastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstName;

    /**
     * 
     */
    private $firstname;

    /**
     * 
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $pwd;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $imageId;

    /**
     * 
     */
    private $imageid;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $createdBy;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $codeAlert;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $travelNewsLetter;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $fashionNewsLetter;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $weeklyNewsLetter;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $postalCode;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateOfBirth;

    /**
     * 
     */
    private $dateofbirth;

    /**
     * 
     */
    private $postalcode;

    /**
     * 
     */
    private $weeklynewsletter;

    /**
     * 
     */
    private $fashionnewsletter;

    /**
     * 
     */
    private $travelnewsletter;

    /**
     * 
     */
    private $codealert;

    /**
     * 
     */
    private $createdby;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogIn;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $currentLogIn;

    /**
     * 
     */
    private $currentlogin;

    /**
     * 
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $interested;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profile_img;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $active_codeid;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $active;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $changepasswordrequest;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Conversions", mappedBy="visitor")
     */
    private $conversions;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\VisitorKeyword", mappedBy="visitor")
     */
    private $visitorKeyword;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\VisitorImage", inversedBy="visitor")
     * @ORM\JoinColumn(name="visitor_image_id", referencedColumnName="id")
     */
    private $visitorimage;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Offer", mappedBy="visitors")
     */
    private $offer;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\Shop", mappedBy="visitors")
     */
    private $favoriteshops;
}