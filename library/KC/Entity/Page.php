<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="page",
 *     indexes={
 *         @ORM\Index(name="pageattributeid_idx", columns={"pageattributeid"}),
 *         @ORM\Index(name="pageHeaderImageId_foreign_key", columns={"pageHeaderImageId"})
 *     }
 * )
 */
class Page
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=8)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $pagetype;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pagetitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

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
     * @ORM\Column(type="blob", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $publish;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $pagelock;

    /**
     * @ORM\Column(type="integer", length=8, nullable=false)
     */
    private $contentManagerId;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $contentManagerName;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $enabletimeconstraint;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $timenumberofdays;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $timetype;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $timemaxoffer;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $timeorder;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $enablewordconstraint;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $wordtitle;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $wordmaxoffer;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $publishdate;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $wordorder;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $awardconstratint;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $awardtype;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $awardmaxoffer;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $awardorder;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $enableclickconstraint;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $numberofclicks;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $clickmaxoffer;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $clickorder;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $maxOffers;

    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $oderOffers;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $couponregular;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $couponeditorpick;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $couponexclusive;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $saleregular;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $saleeditorpick;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $saleexclusive;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $printableregular;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $printableeditorpick;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $printableexclusive;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $showpage;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $logoid;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $customheader;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $showsitemap;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true)
     */
    private $offersCount;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\MoneySaving", mappedBy="page")
     */
    private $moneysaving;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefOfferPage", mappedBy="offers")
     */
    private $pageoffers;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\RefPageWidget", mappedBy="widget")
     */
    private $pagewidget;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\Shop", mappedBy="shopPage")
     */
    private $pages;

    /**
     * @ORM\OneToMany(targetEntity="KC\Entity\SpecialList", mappedBy="page")
     */
    private $specialList;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\Image", inversedBy="pageheaderimage")
     * @ORM\JoinColumn(name="pageHeaderImageId", referencedColumnName="id", onDelete="cascade")
     */
    private $logo;

    /**
     * @ORM\ManyToOne(targetEntity="KC\Entity\PageAttribute", inversedBy="pageattribute")
     * @ORM\JoinColumn(name="pageattributeid", referencedColumnName="id", onDelete="restrict")
     */
    private $page;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    public static function getPageDetailsInError($permalink)
    {
        $currentDate = date('Y-m-d 00:00:00');
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page')
            ->from('KC\Entity\Page', 'page')
            ->leftJoin('page.pagewidget', 'pagewidget')
            ->setParameter(1, $permalink)
            ->where('page.permalink = ?1')
            ->setParameter(2, $currentDate)
            ->andWhere('page.publishdate <= ?2')
            ->setParameter(3, 0)
            ->andWhere('page.deleted = ?3');
        $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetails;
    }

    public static function getPageDetailsFromUrl($pagePermalink)
    {
        $pageDetails = self::getPageDetailsByPermalink($pagePermalink);
        if (!empty($pageDetails)) {
            return $pageDetails;
        } else {
            throw new Zend_Controller_Action_Exception('', 404);
        }
    }

    public static function getPageDetailsByPermalink($permalink)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page.*, img.id, img.path, img.name')
            ->from('KC\Entity\Page', 'page')
            ->leftJoin('page.logo', 'img')
            ->setParameter(1, $permalink)
            ->where('page.permalink = ?1')
            ->setParameter(2, 1)
            ->andWhere('page.publish = ?2')
            ->setParameter(3, 0)
            ->andWhere('page.pagelock = ?3')
            ->setParameter(4, 0)
            ->andWhere('page.pagelock = ?4');
        $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetails;
    }

    public static function getSpecialListPages()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page.*, img.*')
            ->from('KC\Entity\Page', 'page')
            ->leftJoin('page.logo', 'img')
            ->setParameter(1, 'offer')
            ->where('page.pagetype = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2');
        $specialListPages = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $specialListPages;
    }
    

    public static function getDefaultPageProperties($permalink)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page.*')
            ->from('KC\Entity\Page', 'page')
            ->setParameter(1, $permalink)
            ->where('page.permalink = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2');
        $pageProperties = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageProperties;
    }

    public static function getPageDetailFromPermalink($permalink)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page.content, page.pagetitle')
            ->from('KC\Entity\Page', 'page')
            ->setParameter(1, $permalink)
            ->where('page.permalink = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2');
        $pageDetail = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetail;
    }

    public static function updatePageAttributeId()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Page', 'page');
        for ($i = 1; $i <= 3; $i++) {
                $query->set('page.pageattributeid', $i)->getQuery();
            if ($i == 1) {
                $query->setParameter(1, 'info/contact')->where('permalink = ?1')->getQuery();
            } else if ($i == 2) {
                $query->setParameter(1, 'info/faq')->where('permalink = ?1')->getQuery();
            } else if ($i == 3) {
                $query->setParameter(1, 'info/contact')
                ->where('permalink = ?1')
                ->setParameter(2, 'info/faq')
                ->where('permalink = ?2')
                ->getQuery();
            }
            $query->execute();
        }
        return true;
    }

    public static function replaceToPlusPage()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Page', 'page')
            ->set('page.permalink', 'plus')
            ->setParameter(1, 66)
            ->where('page.id = ?1')
            ->getQuery();
            $query->execute();
        return true;
    }

    public static function addSpecialPagesOffersCount($spcialPageId, $offersCount)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Page', 'page')
            ->set('page.offersCount', $offersCount)
            ->setParameter(1, $spcialPageId)
            ->where('page.id = ?1')
            ->getQuery();
            $query->execute();
        return true;
    }
}