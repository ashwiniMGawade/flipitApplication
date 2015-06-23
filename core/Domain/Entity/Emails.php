<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="emails")
 */
class Emails
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
    protected $type;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $header;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $body;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $footer;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $schedule;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $test;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $send_date;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $send_counter;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}