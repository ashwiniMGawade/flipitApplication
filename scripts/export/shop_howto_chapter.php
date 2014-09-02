<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="shop_howto_chapter")
 */
class shop_howto_chapter
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $shopId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $chapterTitle;

    /**
     * @ORM\Column(type="unknown:@longblob", nullable=true)
     */
    private $chapterDescription;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=true)
     */
    private $updated_at;
}