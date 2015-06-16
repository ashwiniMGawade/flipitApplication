<?php
namespace core\domain\entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="offer_news")
 */
class OfferNews
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $offerId;

    /**
     * @ORM\Column(type="string", length=225, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=225, nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $linkstatus;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $startdate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $deleted;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Shop", inversedBy="offerNews")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    protected $shop;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}