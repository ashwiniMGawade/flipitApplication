<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="migration_version")
 */
class migration_version
{
    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $version;
}