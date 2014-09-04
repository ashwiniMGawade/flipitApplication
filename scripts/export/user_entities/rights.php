<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rights", indexes={@ORM\Index(name="roleid_idx", columns={"roleid"})})
 */
class rights
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
    private $name;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     * @ORM\ManyToOne(targetEntity="role", inversedBy="roleRights")
     * @ORM\JoinColumn(name="roleid", referencedColumnName="id", onDelete="restrict")
     */
    private $rights;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;
}