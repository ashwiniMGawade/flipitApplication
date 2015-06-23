<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="media", indexes={@ORM\Index(name="mediaimageid_idx", columns={"mediaimageid"})})
 */
class media
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alternatetext;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $caption;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileurl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $authorName;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $authorId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="image", inversedBy="mediaimage")
     * @ORM\JoinColumn(name="mediaimageid", referencedColumnName="id", onDelete="restrict")
     */
    private $media;
}