<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="newsletterbanners")
 */
class NewsLetterBanners
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $imagetype;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $headerurl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $footerurl;

    /**
     * @ORM\Column(type="boolean", nullable=true)
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

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}