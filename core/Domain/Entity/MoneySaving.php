<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="moneysaving")
 */
class MoneySaving
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    protected $deleted;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\Page", inversedBy="moneysaving")
     * @ORM\JoinColumn(name="pageid", referencedColumnName="id")
     */
    protected $page;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\ArticleCategory", inversedBy="moneysaving")
     * @ORM\JoinColumn(name="categoryid", referencedColumnName="id")
     */
    protected $articlecategory;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}