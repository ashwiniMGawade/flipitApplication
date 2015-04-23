<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ref_article_store",
 *     indexes={
 *         @ORM\Index(name="articleid", columns={"articleid"}),
 *         @ORM\Index(name="storeid", columns={"storeid"}),
 *         @ORM\Index(name="article_shop_id_idx", columns={"articleid","storeid"})
 *     }
 * )
 */
class ref_article_store
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
     * @ORM\ManyToOne(targetEntity="articles", inversedBy="storearticles")
     * @ORM\JoinColumn(name="articleid", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $relatedstores;

    /**
     * @ORM\ManyToOne(targetEntity="shop", inversedBy="articlestore")
     * @ORM\JoinColumn(name="storeid", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $articleshops;
}