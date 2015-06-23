<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="disqus_thread")
 */
class DisqusThread
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $link;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $created;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\DisqusComments", mappedBy="disqusThread")
     */
    protected $disqusComments;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}