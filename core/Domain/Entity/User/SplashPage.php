<?php
namespace Core\Domain\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="splashPage")
 */
class SplashPage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $content;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $image;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $popularShops;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    protected $updatedBy;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updatedAt;

    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getPopularShops()
    {
        return $this->popularShops;
    }

    /**
     * @param mixed $polularShops
     */
    public function setPopularShops($popularShops)
    {
        $this->popularShops = $popularShops;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param mixed $updatedBy
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}
