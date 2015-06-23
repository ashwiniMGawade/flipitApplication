<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="seen_in",
 *     indexes={@ORM\Index(name="logoid_idx", columns={"logoid"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="logoid", columns={"logoid"})}
 * )
 */
class seen_in
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $toolltip;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $alttext;

    /**
     * @ORM\OneToOne(targetEntity="image", inversedBy="seeninlogo")
     * @ORM\JoinColumn(name="logoid", referencedColumnName="id", unique=true, onDelete="restrict")
     */
    private $seenin;
}