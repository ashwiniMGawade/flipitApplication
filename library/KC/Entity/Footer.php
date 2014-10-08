<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="footer")
 */
class Footer
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
    private $bottomFooter;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $middleColumn4;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $middleColumn3;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $middleColumn2;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $middleColumn1;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $topFooter;

    /**
     * 
     */
    private $topfooter;

    /**
     * 
     */
    private $middlecolumn1;

    /**
     * 
     */
    private $middlecolumn2;

    /**
     * 
     */
    private $middlecolumn3;

    /**
     * 
     */
    private $middlecolumn4;

    /**
     * 
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