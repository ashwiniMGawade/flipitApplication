<?php
namespace Core\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="newsletterCampaignOffers"
 * )
 */
class NewsletterCampaignOffer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $campaignId;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $offerId;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Domain\Entity\NewsletterCampaign", inversedBy="newsletterCampaignOffers")
     * @ORM\JoinColumn(name="campaignId", referencedColumnName="id")
     */
    protected $newsletterCampaign;

    /**
     * @ORM\OneToOne(targetEntity="Core\Domain\Entity\Offer", inversedBy="campaignOffer")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
     */
    protected $offer;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
     */
    protected $position;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $section;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $deleted = 0;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updatedAt;

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getNewsletterCampaign()
    {
        return $this->newsletterCampaign;
    }

    /**
     * @param mixed $newsletterCampaign
     */
    public function setNewsletterCampaign($newsletterCampaign)
    {
        $this->newsletterCampaign = $newsletterCampaign;
    }


    /**
     * @return mixed
     */
    public function getCampaignId()
    {
        return $this->campaignId;
    }

    /**
     * @param mixed $campaignId
     */
    public function setCampaignId($campaignId)
    {
        $this->campaignId = $campaignId;
    }

    /**
     * @return mixed
     */
    public function getOfferId()
    {
        return $this->offerId;
    }

    /**
     * @param mixed $offerId
     */
    public function setOfferId($offerId)
    {
        $this->offerId = $offerId;
    }

    /**
     * @return mixed
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * @param mixed $offer
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
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
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param mixed $section
     */
    public function setSection($section)
    {
        $this->section = $section;
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
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}
