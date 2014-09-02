<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="article_chapter")
 */
class article_chapter
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
    private $articleId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="unknown:@longblob", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
     */
    private $updated_at;
}