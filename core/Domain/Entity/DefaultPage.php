<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class DefaultPage extends \KC\Entity\Page
{
    public function __get($property)
    {
        return parent::__get($property);
    }

    public function __set($property, $value)
    {
        parent::__set($property, $value);
    }
}