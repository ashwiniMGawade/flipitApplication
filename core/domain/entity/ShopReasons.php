<?php
namespace core\domain\entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="shopreasons")
 */
class ShopReasons
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $shopid;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $fieldname;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $fieldvalue;

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

    public function __set($property, $value = '')
    {
        $this->$property = $value;
    }

}