<?php
namespace core\domain\entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="footer")
 */
class Footer
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
    protected $topFooter;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $middleColumn1;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $middleColumn2;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $middleColumn3;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $middleColumn4;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $bottomFooter;

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

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}