<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="signupfavoriteshop",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="store_id", columns={"store_id"})}
 * )
 */
class Signupfavoriteshop
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
     */
    private $entered_uid;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Shop", inversedBy="shop")
     * @ORM\JoinColumn(name="store_id", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $signupfavoriteshop;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}