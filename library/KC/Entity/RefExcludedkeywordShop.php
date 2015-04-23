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
    private $id;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $keywordname;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
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
     * @ORM\ManyToOne(targetEntity="KC\Entity\ExcludedKeyword", inversedBy="keywords")
     * @ORM\JoinColumn(name="keywordid", referencedColumnName="id")
     */
    private $shops;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Shop", inversedBy="shopsofKeyword")
     * @ORM\JoinColumn(name="shopid", referencedColumnName="id")
     */
    private $keywords;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}