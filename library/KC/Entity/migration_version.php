<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="migration_version")
 */
class migration_version
{
    /**
     * 
     * 
     * 
     
     
     */
    private $id;
    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $version;
}