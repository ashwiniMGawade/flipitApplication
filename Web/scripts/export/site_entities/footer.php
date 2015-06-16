<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="footer")
 */
class footer
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
    private $topfooter;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $middlecolumn1;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $middlecolumn2;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $middlecolumn3;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $middlecolumn4;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $bottomfooter;

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
}