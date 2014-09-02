<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dashboard")
 */
class dashboard
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
    private $message;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $no_of_offers;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $no_of_shops;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $no_of_clickouts;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $no_of_subscribers;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $total_no_of_offers;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $total_no_of_shops;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $total_no_of_shops_online_code;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $total_no_of_shops_online_code_lastweek;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $total_no_members;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $deleted;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $total_no_of_shops_online_code_thisweek;
}