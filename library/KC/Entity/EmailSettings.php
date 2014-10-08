<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="emailsettings")
 */
class EmailSettings
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
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $locale;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $timezone;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}