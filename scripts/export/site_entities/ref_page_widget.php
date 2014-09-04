<?php
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ref_page_widget",
 *     indexes={@ORM\Index(name="pageid_idx", columns={"pageid"}),@ORM\Index(name="widgetid_idx", columns={"widgetid"})}
 * )
 */
class ref_page_widget
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $stauts;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="page", inversedBy="pagewidget")
     * @ORM\JoinColumn(name="pageid", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $widget;

    /**
     * @ORM\ManyToOne(targetEntity="widget", inversedBy="Widget")
     * @ORM\JoinColumn(name="widgetid", referencedColumnName="id", nullable=false, onDelete="restrict")
     */
    private $page;
}