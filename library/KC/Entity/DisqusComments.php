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
     * 
     */
    private $id;

    /**
     * 
     */
    private $comment_id;

    /**
     * 
     */
    private $message;

    /**
     * 
     */
    private $page_title;

    /**
     * 
     */
    private $page_url;

    /**
     * 
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author_name;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="KC\Entity\DisqusThread", inversedBy="disqusComments")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id")
     */
    private $disqusThread;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * 
     */
    private $author_profile_url;

    /**
     * 
     */
    private $author_avtar;
}