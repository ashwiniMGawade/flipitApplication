<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="category",
 *     indexes={
 *         @ORM\Index(name="categoryiconid_idx", columns={"categoryiconid"}),
 *         @ORM\Index(name="name", columns={"name"}),
 *         @ORM\Index(name="name_2", columns={"name"}),
 *         @ORM\Index(name="name_3", columns={"name"}),
 *         @ORM\Index(name="name_4", columns={"name"}),
 *         @ORM\Index(name="name_5", columns={"name"}),
 *         @ORM\Index(name="name_6", columns={"name"}),
 *         @ORM\Index(name="name_7", columns={"name"}),
 *         @ORM\Index(name="name_8", columns={"name"}),
 *         @ORM\Index(name="name_9", columns={"name"}),
 *         @ORM\Index(name="name_10", columns={"name"}),
 *         @ORM\Index(name="categoryFeaturedImageId_foreign_key", columns={"categoryFeaturedImageId"}),
 *         @ORM\Index(name="categoryHeaderImageId_foreign_key", columns={"categoryHeaderImageId"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="categoryiconid", columns={"categoryiconid"})}
 * )
 */
class category
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $permalink;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metatitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metadescription;

    /**
     * @ORM\Column(type="unknown:@longblob", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="unknown:@datetime_f", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $featured_category;
}