<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="signupcodes", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})})
 */
class signupcodes
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $updated_at;
}