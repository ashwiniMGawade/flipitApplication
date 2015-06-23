<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="disqus_comments"
 * )
 */
class DisqusComments
{
    /**
     * 
     * @ORM\Column(type="integer", length=11, nullable=true)
     * @ORM\Id
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $author_name;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="KC\Entity\DisqusThread", inversedBy="disqusComments")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id")
     */
    protected $disqusThread;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $created;

    /**
     * @ORM\Column(type="text", length=512, nullable=true)
     */
    protected $comment;
    
    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}