<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="route_redirect", indexes={@ORM\Index(name="orignalurl_idx", columns={"orignalurl"})})
 */
class route_redirect
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8, unsigned=true)
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
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $deleted;
}