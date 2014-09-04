<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="chain_item",
 *     indexes={
 *         @ORM\Index(name="ref_chain_items", columns={"chainid"}),
 *         @ORM\Index(name="ref_chain_website", columns={"websiteid"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="unique_shopname_website_idx", columns={"shopname","websiteid"})}
 * )
 */
class chain_item
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shopname;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $permalink;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $locale;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $shopId;

    /**
     * @ORM\ManyToOne(targetEntity="chain", inversedBy="chain")
     * @ORM\JoinColumn(name="chainid", referencedColumnName="id", onDelete="cascade")
     */
    private $chainItems;

    /**
     * @ORM\ManyToOne(targetEntity="website", inversedBy="website")
     * @ORM\JoinColumn(name="websiteid", referencedColumnName="id", onDelete="cascade")
     */
    private $chainItem;
}