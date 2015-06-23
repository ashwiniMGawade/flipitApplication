<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ref_page_widget",
 *     indexes={@ORM\Index(name="pageid_idx", columns={"pageId"}),@ORM\Index(name="widgetid_idx", columns={"widgetid"})}
 * )
 */
class RefPageWidget
{
    /**
    * @ORM\Id
     * @ORM\Column(type="integer", length=8, nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $stauts;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    protected $position;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Page", inversedBy="pagewidget")
     * @ORM\JoinColumn(name="pageId", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    protected $widget;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Widget", inversedBy="Widget")
     * @ORM\JoinColumn(name="widgetid", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    protected $page;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}