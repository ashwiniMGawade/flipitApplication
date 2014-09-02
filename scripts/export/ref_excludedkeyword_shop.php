<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ref_excludedkeyword_shop",
 *     indexes={@ORM\Index(name="keyword_shop_id_idx", columns={"keywordid","shopid"})}
 * )
 */
class ref_excludedkeyword_shop
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
    private $keywordid;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $keywordname;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $shopid;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\Column(type="unknown:@timestamp_f", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@timestamp_f", nullable=false)
     */
    private $updated_at;
}