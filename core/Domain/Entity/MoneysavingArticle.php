<?php
namespace Core\Domain\Entity;
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
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $position;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Articles", inversedBy="articles")
     * @ORM\JoinColumn(name="articleId", referencedColumnName="id", onDelete="restrict")
     */
    protected $moneysaving;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}