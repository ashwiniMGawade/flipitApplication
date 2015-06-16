<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="seen_in",
 *     indexes={@ORM\Index(name="logoid_idx", columns={"logoId"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="logoid", columns={"logoId"})}
 * )
 */
class SeenIn
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $toolltip;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $altText;

    /**
     * @ORM\OneToOne(targetEntity="KC\Entity\Logo", inversedBy="seenin")
     * @ORM\JoinColumn(name="logoId", referencedColumnName="id", unique=true)
     */
    protected $logo;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
    
    public function getId()
    {
        return $this->id;
    }
}