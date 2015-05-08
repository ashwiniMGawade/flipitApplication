<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="moneysaving_article",
 *     indexes={@ORM\Index(name="articleid_idx", columns={"articleId"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="articleid", columns={"articleId"})}
 * )
 */
class MoneysavingArticle
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
    private $type;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Articles", inversedBy="articles")
     * @ORM\JoinColumn(name="articleId", referencedColumnName="id", onDelete="restrict")
     */
    private $moneysaving;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}