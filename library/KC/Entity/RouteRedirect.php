<?php
namespace KC\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="route_redirect", indexes={@ORM\Index(name="orignalurl_idx", columns={"orignalurl"})})
 */
class RouteRedirect
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=12)
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $deleted;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    public static function getRoute($orignalurl)
    {
        $orignalurl= trim($orignalurl, '/');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('route')
            ->from('KC\Entity\RouteRedirect', 'route')
            ->setParameter(1, $orignalurl)
            ->where('route.orignalurl = ?1');
        $routeRedirectInfo = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $routeRedirectInfo;
    }

    public static function getRedirects($redirectto)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('route.orignalurl')
            ->from('KC\Entity\RouteRedirect', 'route')
            ->setParameter(1, $redirectto)
            ->where('route.redirectto = ?1')
            ->setParameter(2, '0')
            ->andWhere('route.deleted = ?2');
        $orignalUrl = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $orignalUrl;
    }

    public static function addRedirect($params)
    {
        $routeRedirect = new RouteRedirect();
        $routeRedirect->orignalurl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['orignalurl']);
        $routeRedirect->redirectto = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['redirectto']);
        $routeRedirect->created_at = new \DateTime('now');
        $routeRedirect->updated_at = new \DateTime('now');
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $entityManagerLocale->persist($routeRedirect);
        $entityManagerLocale->flush();
        return true;
    }

    public static function getRedirect($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $redirectList = $queryBuilder->select('e.orignalurl as orignalurl,e.redirectto as redirectto,e.created_at as created_at')
            ->from('KC\Entity\RouteRedirect', 'e')
            ->orderBy('e.id', 'DESC')->getQuery();
        $list = \DataTable_Helper::generateDataTableResponse(
            $redirectList,
            $params,
            array("__identifier" => 'e.id','e.id','orignalurl','redirectto','created_at'),
            array(),
            array()
        );
        return $list;
    }

    public static function getRedirectForEdit($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('route')
            ->from('KC\Entity\RouteRedirect', 'route')
            ->setParameter(1, $id)
            ->where('route.id = ?1');
        $routeRedirectInfo = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $routeRedirectInfo;
    }

    public static function updateRedirect($params)
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $routeRedirect =  $entityManagerLocale->find('KC\Entity\RouteRedirect', $params['id']);
        $routeRedirect->orignalurl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['orignalurl']);
        $routeRedirect->redirectto = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['redirectto']);
        $entityManagerLocale->persist($routeRedirect);
        $entityManagerLocale->flush();
        return true;
    }

    public static function exportRedirectList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('route')
            ->from('KC\Entity\RouteRedirect', 'route')
            ->orderBy('route.id', 'DESC');
        $routeRedirectList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $routeRedirectList;
    }

    public static function deleteRedirect($id)
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $routeRedirect =  $entityManagerLocale->find('KC\Entity\RouteRedirect', $id);
        $entityManagerLocale->remove($routeRedirect);
        $entityManagerLocale->flush();
        return true;
    }

    public function uploadExcel($file, $import = false)
    {
        if (!file_exists(UPLOAD_EXCEL_PATH)) {
            mkdir(UPLOAD_EXCEL_PATH, 0776, true);
        }

        // generate upload path for images related to shop
        $rootPath = UPLOAD_EXCEL_PATH;
        if ($import) {
            $rootPath .= 'import/';
        }

        // check upload directory exists, if no then create upload directory
        if (!file_exists($rootPath)) {
            mkdir($rootPath, 0775, true);
        }

        $adapter = new Zend_File_Transfer_Adapter_Http();
        // set destination path and apply validations
        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('xlsx', true));
        $adapter->addValidator('Size', false, array('min' => 20, 'max' => '2MB'));
        // get upload file info

        $files = $adapter->getFileInfo($file);
        // get file name
        $name = $adapter->getFileName($file, false);

        // rename file name to by prefixing current unix timestamp
        $newName = time() . "_" . $name;

        // generates complete path of image
        $cp = $rootPath . $newName;


        // apply filter to rename file name and set target
        $adapter
        ->addFilter(
            new Zend_Filter_File_Rename(
                array('target' => $cp, 'overwrite' => true)
            ),
            null,
            $file
        );

        // recieve file for upload
        $adapter->receive($file);

        // check is file is valid then
        $messages = $adapter->getMessages();
        echo '<pre>'.print_r($messages, true).'</pre>';
        if ($adapter->isValid($newName)) {

            return array("fileName" => $newName, "status" => "200",
                    "msg" => "File uploaded successfully",
                    "path" => $rootPath);

        } else {

            return array("status" => "-1",
                    "msg" => "Please upload the valid file");

        }

    }
}
