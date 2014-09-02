<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="votes", indexes={@ORM\Index(name="offer_id_idx", columns={"offerId"})})
 */
class votes
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $offerId;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $ipaddress;

    /**
     * @ORM\Column(type="unknown:@timestamp_f", nullable=false)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $vote;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private $moneySaved;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $product;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $status;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $visitorid;
}