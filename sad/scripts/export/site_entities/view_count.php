<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="view_count",
 *     indexes={
 *         @ORM\Index(name="offerid_idx", columns={"offerid"}),
 *         @ORM\Index(name="offer_click_count_idx", columns={"offerid","onclick","counted"}),
 *         @ORM\Index(name="memberid_idx", columns={"memberid"})
 *     }
 * )
 */
class view_count
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
    private $loadtime;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $onclick;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $onload;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $onhover;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ip;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $memberid;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $counted;

    /**
     * @ORM\ManyToOne(targetEntity="offer", inversedBy="offerviewcount")
     * @ORM\JoinColumn(name="offerid", referencedColumnName="id", onDelete="restrict")
     */
    private $viewcount;
}