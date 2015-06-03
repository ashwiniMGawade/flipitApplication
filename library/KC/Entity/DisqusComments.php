<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="disqus_comments",
 *     indexes={
 *         @ORM\Index(name="page_url_comments_idx", columns={"comment"}),
 *         @ORM\Index(name="message_comments_idx", columns={"thread_id"})
 *     }
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
     * 
     */
    protected $comment_id;

    /**
     * 
     */
    protected $message;

    /**
     * 
     */
    protected $page_title;

    /**
     * 
     */
    protected $page_url;

    /**
     * 
     */
    protected $created_at;

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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * 
     */
    protected $author_profile_url;

    /**
     * 
     */
    protected $author_avtar;
    
    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}