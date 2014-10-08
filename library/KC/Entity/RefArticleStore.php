<?php
namespace KC\Entity;
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
class RefArticleStore
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
     * @ORM\ManyToOne(targetEntity="KC\Entity\Articles", inversedBy="storearticles")
     * @ORM\JoinColumn(name="articleid", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $relatedstores;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Shop", inversedBy="articlestore")
     * @ORM\JoinColumn(name="storeid", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $articleshops;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}