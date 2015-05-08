<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="offer_news")
 */
class offer_news
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
    private $offerId;

    /**
     * @ORM\Column(type="string", length=225, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=225, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkstatus;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startdate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $deleted;

    /**
     * @ORM\ManyToOne(targetEntity="shop", inversedBy="offerNews")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    private $shop;
}