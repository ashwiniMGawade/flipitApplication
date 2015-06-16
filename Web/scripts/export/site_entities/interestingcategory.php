<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="interestingcategory")
 */
class interestingcategory
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
     * @ORM\ManyToOne(targetEntity="category", inversedBy="interestingcategory")
     * @ORM\JoinColumn(name="categoryid", referencedColumnName="id")
     */
    private $category;
}