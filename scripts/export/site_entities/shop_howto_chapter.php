<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="shop_howto_chapter")
 */
class shop_howto_chapter
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
    private $chapterTitle;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $chapterDescription;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="shop", inversedBy="howtochapter")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    private $shop;
}