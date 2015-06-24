<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="articleviewcount")
 */
class ArticleViewCount
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
     */
    protected $onclick;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $onload;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $ip;

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
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\Articles", inversedBy="articleviewcount")
     * @ORM\JoinColumn(name="articleid", referencedColumnName="id")
     */
    protected $articles;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}