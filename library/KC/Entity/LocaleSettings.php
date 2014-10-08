<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="locale_settings")
 */
class LocaleSettings
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $locale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $timezone;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}