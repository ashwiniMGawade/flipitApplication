<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="favorite_offer",
 *     indexes={@ORM\Index(name="offer_visitor_id_idx", columns={"offerId","visitorId"})}
 * )
 */
class favorite_offer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
     */
    private $offerId;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
     */
    private $visitorId;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\Column(type="unknown:@timestamp_f", nullable=false)
     */
    private $created_at;
}