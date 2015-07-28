<?php
namespace Core\Domain\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="api_keys")
 */
class ApiKey
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    protected $api_key;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\User\User", inversedBy="apiKeys", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user_id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $deleted;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * @param $value
     */
    public function setApiKey($value)
    {
        $this->api_key = $value;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param $value
     */
    public function setUserId($value)
    {
        $this->user_id = $value;
    }

    /**
     * @return \datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param $value
     */
    public function setCreatedAt($value)
    {
        $this->created_at = $value;
    }

    /**
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param $value
     */
    public function setDeleted($value)
    {
        $this->deleted = $value;
    }
}
