<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="media", indexes={@ORM\Index(name="mediaimageid_idx", columns={"mediaimageid"})})
 */
class Media
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $alternatetext;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $caption;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $fileurl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $authorName;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    protected $authorId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\MediaImage", inversedBy="media")
     * @ORM\JoinColumn(name="mediaimageid", referencedColumnName="id")
     */
    protected $mediaimage;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}