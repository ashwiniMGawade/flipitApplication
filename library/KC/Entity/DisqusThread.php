<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class DisqusThread
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $created;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\DisqusComments", mappedBy="disqusThread")
     */
    private $disqusComments;
}