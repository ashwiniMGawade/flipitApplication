<?php
namespace Core\Domain\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="widget")
 */
class Widget
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $function_name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $status = 1;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $userDefined;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $showWithDefault;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $deleted = 0;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\RefPageWidget", mappedBy="page")
     */
    protected $Widget;

    /**
     * @ORM\OneToMany(targetEntity="Core\Domain\Entity\PageWidgets", mappedBy="widget")
     */
    protected $pageWidgets;

    /**
     * @return mixed
     */
    public function getWidget()
    {
        return $this->Widget;
    }

    /**
     * @param mixed $Widget
     */
    public function setWidget($Widget)
    {
        $this->Widget = $Widget;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getFunctionName()
    {
        return $this->function_name;
    }

    /**
     * @param mixed $function_name
     */
    public function setFunctionName($function_name)
    {
        $this->function_name = $function_name;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPageWidgets()
    {
        return $this->pageWidgets;
    }

    /**
     * @param mixed $pageWidgets
     */
    public function setPageWidgets($pageWidgets)
    {
        $this->pageWidgets = $pageWidgets;
    }

    /**
     * @return mixed
     */
    public function getShowWithDefault()
    {
        return $this->showWithDefault;
    }

    /**
     * @param mixed $showWithDefault
     */
    public function setShowWithDefault($showWithDefault)
    {
        $this->showWithDefault = $showWithDefault;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return mixed
     */
    public function getUserDefined()
    {
        return $this->userDefined;
    }

    /**
     * @param mixed $userDefined
     */
    public function setUserDefined($userDefined)
    {
        $this->userDefined = $userDefined;
    }

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $endDate;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}