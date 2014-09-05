<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="interestingcategory")
 */
class Interestingcategory
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
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Category", inversedBy="interestingcategory")
     * @ORM\JoinColumn(name="categoryid", referencedColumnName="id")
     */
    private $category;
}