<?php
namespace KC\Repository;

class RouteRedirect extends \KC\Entity\RouteRedirect
{
    public static function getRoute($orignalurl)
    {
        $orignalurl= trim($orignalurl, '/');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('route')
            ->from('KC\Entity\RouteRedirect', 'route')
            ->setParameter(1, \FrontEnd_Helper_viewHelper::sanitize($orignalurl))
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
        $routeRedirect = new \KC\Entity\RouteRedirect();
        $routeRedirect->orignalurl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['orignalurl']);
        $routeRedirect->redirectto = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['redirectto']);
        $routeRedirect->deleted = 0;
        $routeRedirect->created_at = new \DateTime('now');
        $routeRedirect->updated_at = new \DateTime('now');
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $entityManagerLocale->persist($routeRedirect);
        $entityManagerLocale->flush();
        return true;
    }

    public static function getRedirect($params)
    {
        $request  = \DataTable_Helper::createSearchRequest($params, array('id','orignalurl', 'redirectto', 'created_at'));
        $qb = \Zend_Registry::get('emLocale')->createQueryBuilder()->from('KC\Entity\RouteRedirect', 'p');
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($qb)
            ->add('number', 'p.id')
            ->add('text', 'p.orignalurl')
            ->add('text', 'p.redirectto')
            ->add('number', 'p.created_at');

        $results = $builder->getTable()->getResponseArray();

        return $results;
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
        $routeRedirect->updated_at = new \DateTime('now');
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

        $adapter = new \Zend_File_Transfer_Adapter_Http();
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
            new \Zend_Filter_File_Rename(
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
