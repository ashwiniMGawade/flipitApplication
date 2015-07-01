<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="shopExcelInformation")
 */
class ShopExcelInformation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    protected $totalShopsCount;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    protected $passCount;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    protected $failCount;

    /**
     * @ORM\Column(type="boolean", length=1, nullable=true)
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
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    protected $userId;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value = '')
    {
        $this->$property = $value;
    }
}