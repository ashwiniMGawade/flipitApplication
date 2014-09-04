<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="excluded_keyword")
 */
class excluded_keyword
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
    private $keyword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $action;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\ManyToMany(targetEntity="shop", inversedBy="keywords")
     * @ORM\JoinTable(
     *     name="ref_excludedkeyword_shop",
     *     joinColumns={@ORM\JoinColumn(name="keywordid", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="shopid", referencedColumnName="id", nullable=false)}
     * )
     */
    private $shops;
}