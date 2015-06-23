<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ref_excludedkeyword_shop")
 */
class RefExcludedkeywordShop
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    protected $keywordname;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
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
     * @ORM\ManyToOne(targetEntity="KC\Entity\ExcludedKeyword", inversedBy="keywords")
     * @ORM\JoinColumn(name="keywordid", referencedColumnName="id")
     */
    protected $shops;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Shop", inversedBy="shopsofKeyword")
     * @ORM\JoinColumn(name="shopid", referencedColumnName="id")
     */
    protected $keywords;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}