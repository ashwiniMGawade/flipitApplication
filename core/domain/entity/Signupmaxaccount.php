<?php
namespace core\domain\entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="signupmaxaccount")
 */
class Signupmaxaccount
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=20, nullable=false)
     */
    protected $entered_uid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $no_of_acc;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    protected $status;

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
    protected $email_confirmation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $email_header;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $email_footer;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $max_account;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $emailperlocale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $sendername;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $emailsubject;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $testemail;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $testimonial1;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $testimonial2;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $testimonial3;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $showtestimonial;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $homepagebanner_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $homepagebanner_path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $homepage_widget_banner_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $homepage_widget_banner_path;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $newletter_is_scheduled;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $newletter_scheduled_time;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $newsletter_sent_time;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}