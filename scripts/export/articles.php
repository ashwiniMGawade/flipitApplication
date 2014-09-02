<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="articles", indexes={@ORM\Index(name="thumbnailid", columns={"thumbnailid"})})
 */
class articles
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $permalink;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metatitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metadescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $publish;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $publishdate;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
     */
    private $authorid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $authorname;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $thumbnailsmallid;

    /**
     * @ORM\ManyToOne(targetEntity="image", inversedBy="test2")
     * @ORM\JoinColumn(name="thumbnailid", referencedColumnName="id", onDelete="restrict")
     */
    private $test;
}