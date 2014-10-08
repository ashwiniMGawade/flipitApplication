<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="disqus_comments",
 *     indexes={
 *         @ORM\Index(name="page_url_comments_idx", columns={"page_url"}),
 *         @ORM\Index(name="message_comments_idx", columns={"message"})
 *     }
 * )
 */
class DisqusComments
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $comment_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $page_title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $page_url;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author_profile_url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author_avtar;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}