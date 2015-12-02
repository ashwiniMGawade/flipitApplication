<?php
namespace Core\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="newsletterCampaigns")
 */
class NewsletterCampaign
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $campaignName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $campaignSubject;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $senderName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $senderEmail;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $header;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $headerBanner;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $headerBannerURL;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $footer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $footerBanner;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $footerBannerURL;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $offerPartOneTitle;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $offerPartTwoTitle;
    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $scheduledStatus;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $scheduledTime;
    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $newsletterSentTime;
    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    protected $receipientCount;
    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $deleted;
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
    public function getCampaignName()
    {
        return $this->campaignName;
    }

    /**
     * @param mixed $campaignName
     */
    public function setCampaignName($campaignName)
    {
        $this->campaignName = $campaignName;
    }

    /**
     * @return mixed
     */
    public function getCampaignSubject()
    {
        return $this->campaignName;
    }

    /**
     * @param mixed $campaignName
     */
    public function setCampaignSubject($campaignName)
    {
        $this->campaignName = $campaignName;
    }

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
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @param mixed $footer
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;
    }

    /**
     * @return mixed
     */
    public function getFooterBanner()
    {
        return $this->footerBanner;
    }

    /**
     * @param mixed $footerBanner
     */
    public function setFooterBanner($footerBanner)
    {
        $this->footerBanner = $footerBanner;
    }

    /**
     * @return mixed
     */
    public function getFooterBannerURL()
    {
        return $this->footerBannerURL;
    }

    /**
     * @param mixed $footerBannerURL
     */
    public function setFooterBannerURL($footerBannerURL)
    {
        $this->footerBannerURL = $footerBannerURL;
    }

    /**
     * @return mixed
     */
    public function getHeaderBanner()
    {
        return $this->headerBanner;
    }

    /**
     * @param mixed $headerBanner
     */
    public function setHeaderBanner($headerBanner)
    {
        $this->headerBanner = $headerBanner;
    }

    /**
     * @return mixed
     */
    public function getHeaderBannerURL()
    {
        return $this->headerBannerURL;
    }

    /**
     * @param mixed $headerBannerURL
     */
    public function setHeaderBannerURL($headerBannerURL)
    {
        $this->headerBannerURL = $headerBannerURL;
    }

    /**
     * @return mixed
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param mixed $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
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
    public function getNewsletterSentTime()
    {
        return $this->newsletterSentTimet;
    }

    /**
     * @param mixed $newsletterSentTimet
     */
    public function setNewsletterSentTime($newsletterSentTimet)
    {
        $this->newsletterSentTimet = $newsletterSentTimet;
    }

    /**
     * @return mixed
     */
    public function getOfferPartOneTitle()
    {
        return $this->offerPartOneTitle;
    }

    /**
     * @param mixed $offerPartOneTitle
     */
    public function setOfferPartOneTitle($offerPartOneTitle)
    {
        $this->offerPartOneTitle = $offerPartOneTitle;
    }

    /**
     * @return mixed
     */
    public function getOfferPartTwoTitle()
    {
        return $this->offerPartTwoTitle;
    }

    /**
     * @param mixed $offerPartTwoTitle
     */
    public function setOfferPartTwoTitle($offerPartTwoTitle)
    {
        $this->offerPartTwoTitle = $offerPartTwoTitle;
    }

    /**
     * @return mixed
     */
    public function getReceipientCount()
    {
        return $this->receipientCount;
    }

    /**
     * @param mixed $receipientCount
     */
    public function setReceipientCount($receipientCount)
    {
        $this->receipientCount = $receipientCount;
    }

    /**
     * @return mixed
     */
    public function getScheduledStatus()
    {
        return $this->scheduledStatus;
    }

    /**
     * @param mixed $scheduledStatus
     */
    public function setScheduledStatus($scheduledStatus)
    {
        $this->scheduledStatus = $scheduledStatus;
    }

    /**
     * @return mixed
     */
    public function getdeleted()
    {
        return $this->scheduledStatus;
    }

    /**
     * @param mixed $scheduledStatus
     */
    public function setdeleted($scheduledStatus)
    {
        $this->scheduledStatus = $scheduledStatus;
    }

    /**
     * @return mixed
     */
    public function getScheduledTime()
    {
        return $this->scheduledTime;
    }

    /**
     * @param mixed $scheduledTime
     */
    public function setScheduledTime($scheduledTime)
    {
        $this->scheduledTime = $scheduledTime;
    }

    /**
     * @return mixed
     */
    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    /**
     * @param mixed $senderEmail
     */
    public function setSenderEmail($senderEmail)
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * @return mixed
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * @param mixed $senderName
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
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
