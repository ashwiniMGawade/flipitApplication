<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="moneysaving")
 */
class moneysaving
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\ManyToOne(targetEntity="page", inversedBy="moneysaving")
     * @ORM\JoinColumn(name="pageid", referencedColumnName="id")
     */
    private $page;

    /**
     * @ORM\ManyToOne(targetEntity="articlecategory", inversedBy="moneysaving")
     * @ORM\JoinColumn(name="categoryid", referencedColumnName="id")
     */
    private $articlecategory;
}