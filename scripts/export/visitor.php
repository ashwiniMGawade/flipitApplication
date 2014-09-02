<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="visitor",
 *     indexes={@ORM\Index(name="imageid_idx", columns={"imageid"}),@ORM\Index(name="createdby_idx", columns={"createdby"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})}
 * )
 */
class visitor
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
    private $imageid;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $gender;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateofbirth;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $postalcode;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $weeklynewsletter;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $fashionnewsletter;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $travelnewsletter;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $codealert;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $createdby;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $currentlogin;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $lastlogin;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
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
}