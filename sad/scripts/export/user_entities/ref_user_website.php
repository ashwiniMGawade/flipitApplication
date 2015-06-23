<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ref_user_website",
 *     indexes={@ORM\Index(name="userid_idx", columns={"userid"}),@ORM\Index(name="websiteid_idx", columns={"websiteid"})}
 * )
 */
class ref_user_website
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
     * @ORM\ManyToOne(targetEntity="user", inversedBy="userWebsite")
     * @ORM\JoinColumn(name="userid", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $website;

    /**
     * @ORM\ManyToOne(targetEntity="website", inversedBy="WebsiteRef")
     * @ORM\JoinColumn(name="websiteid", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $refUserWebsite;
}