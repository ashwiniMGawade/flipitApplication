<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="widget")
 */
class widget
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $status;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $userdefined;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $showwithdefault;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\OneToMany(targetEntity="ref_page_widget", mappedBy="page")
     */
    private $Widget;
}