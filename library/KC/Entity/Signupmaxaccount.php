<?php
namespace KC\Entity;
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
    private $id;

    /**
     * @ORM\Column(type="integer", length=20, nullable=false)
     */
    private $entered_uid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $no_of_acc;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    private $status;

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
    private $email_confirmation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $email_header;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $email_footer;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $max_account;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $emailperlocale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sendername;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $emailsubject;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $testemail;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $testimonial1;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $testimonial2;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $testimonial3;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $showtestimonial;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $homepagebanner_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $homepagebanner_path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $homepage_widget_banner_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $homepage_widget_banner_path;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $newletter_is_scheduled;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $newletter_scheduled_time;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $newsletter_sent_time;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}