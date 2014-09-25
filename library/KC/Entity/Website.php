<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="website")
 */
class Website
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
    private $url;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $deleted;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @ORM\ManyToMany(targetEntity="KC\Entity\Chain", mappedBy="website")
     */
    private $chain;

    /**
     * @ORM\ManyToMany(targetEntity="KC\Entity\User", inversedBy="website")
     * @ORM\JoinTable(
     *     name="ref_user_website",
     *     joinColumns={@ORM\JoinColumn(name="websiteid", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="userid", referencedColumnName="id", nullable=false)}
     * )
     */
    private $user;
    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    public static function getAllWebsites()
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('w.id, w.name, w.status')
            ->from('KC\Entity\Website', 'w')
            ->setParameter(1, '0')
            ->where('w.deleted = ?1');
        $websites = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return \BackEnd_Helper_viewHelper::msort($websites, "name", array("kortingscode.nl"));
    }

    public static function getWebsiteDetails($websiteId = null, $websiteName = null)
    {
        $websiteId =  \FrontEnd_Helper_viewHelper::sanitize($websiteId);
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('w.id, w.name, w.url, w.chain')
            ->from('KC\Entity\Website', 'w')
            ->setParameter(1, '0')
            ->where('w.deleted = ?1');

        if ($websiteName) {
            $query->setParameter(2, $websiteName)
            ->andWhere("w.name = ?2");
        } else {
            $query->setParameter(3, $websiteId)
            ->andWhere("w.id = ?3");
        }
        return $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public static function setLocaleStatus($localeStatus, $websiteName)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Website', 'w')
                ->set('w.status', $queryBuilder->expr()->literal($localeStatus))
                ->setParameter(1, $websiteName)
                ->where('w.name = ?1')
                ->getQuery();
        $query->execute();
        return true;
    }

    public static function getLocaleStatus($websiteName)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('w.status')
            ->from('KC\Entity\Website', 'w')
            ->setParameter(1, $websiteName)
            ->where('w.name = ?1');
        $localeStatus = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $localeStatus;
    }

    public static function saveChain($chain, $websiteName)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Website', 'w')
                ->set('w.chain', $queryBuilder->expr()->literal($chain))
                ->setParameter(1, $websiteName)
                ->where('w.name = ?1')
                ->getQuery();
        $query->execute();
        return true;
    }
}
