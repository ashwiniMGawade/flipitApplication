<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="route_redirect", indexes={@ORM\Index(name="orignalurl_idx", columns={"orignalurl"})})
 */
class RouteRedirect
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=12)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $orignalurl;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $redirectto;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $deleted;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}