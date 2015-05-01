<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
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