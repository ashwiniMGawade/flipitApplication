<?php
namespace KC\Repository;
class SeenIn extends \Core\Domain\Entity\SeenIn
{
    #####################################################
    ######### REFACTORED CODE #########
    #####################################################
    public static function getSeenInContent()
    {
        $seenInContents = '';
        $seeInContentNames = self::checkSeenInContent();
        if ($seeInContentNames) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->select('s,l')
            ->from('\Core\Domain\Entity\SeenIn', 's')
            ->leftJoin('s.logo', 'l')
            ->setParameter(1, $seeInContentNames)
            ->where($queryBuilder->expr()->in('s.id', '?1'));
            $seenInContents = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $seenInContents;
    }

    public static function checkSeenInContent()
    {
        $seenIn = array();
        $seenIn[] = \KC\Repository\Settings::getSettings(\KC\Repository\Settings::SEENIN_1);
        $seenIn[] = \KC\Repository\Settings::getSettings(\KC\Repository\Settings::SEENIN_2);
        $seenIn[] = \KC\Repository\Settings::getSettings(\KC\Repository\Settings::SEENIN_3);
        $seenIn[] = \KC\Repository\Settings::getSettings(\KC\Repository\Settings::SEENIN_4);
        $seenIn[] = \KC\Repository\Settings::getSettings(\KC\Repository\Settings::SEENIN_5);
        $seenIn[] = \KC\Repository\Settings::getSettings(\KC\Repository\Settings::SEENIN_6);
        return $seenIn;
    }
    #####################################################
    ######### REFACTORED CODE #########
    #####################################################

    public static function update($params)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        for ($i=1; $i<7; $i++) {
            $retVal = self::checkSeenInContent1("seenin_". $i);
            # check if it has integer id of footer
            if ($retVal) {
                # create object of previous data
                $seenIn =  \Zend_Registry::get('emLocale')->find('KC\Entity\SeenIn', $retVal);
            } else {
                # new object
                $seenIn = new \KC\Entity\SeenIn();
            }
            $seenIn->altText = @$params['alt-'. $i] ?  $params['alt-'. $i] : null;
            if (isset($_FILES['image-'.$i])) {
                $result = self::uploadImage('image-'.$i);
                if ($result['status'] == '200') {
                    $viewHelper = new \BackEnd_Helper_viewHelper();
                    $ext = $viewHelper->getImageExtension($result['fileName']);
                    $seenInImage  = new \KC\Entity\Logo();
                    $seenInImage->ext = @$ext;
                    $seenInImage->path = @$result['path'];
                    $seenInImage->name = @\BackEnd_Helper_viewHelper::stripSlashesFromString(
                        $result['fileName']
                    );
                    $seenInImage->deleted = 0;
                    $seenInImage->created_at = new \DateTime('now');
                    $seenInImage->updated_at = new \DateTime('now');
                    $entityManagerLocale->persist($seenInImage);
                    $entityManagerLocale->flush();
                    $seenIn->logo =  $entityManagerLocale->find('KC\Entity\Logo', $seenInImage->getId());
                }
            }
            $seenIn->status =  0;
            $seenIn->created_at = new \DateTime('now');
            $seenIn->updated_at = new \DateTime('now');
            $entityManagerLocale->persist($seenIn);
            $entityManagerLocale->flush();
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeSeenIn_list');
            if (!$retVal) {
                self::newSeenInSetting($seenIn->id, "SEENIN_". $i);
            }
        }
    }

    public static function checkSeenInContent1($name)
    {
        return  \KC\Repository\Settings::getSettings($name);
    }

    public static function newSeenInSetting($id, $name)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $settings =  new KC\Entity\Settings();
        $settings->name =  constant("Settings::" . $name);
        $settings->value = $id;
        $entityManagerLocale->persist($settings);
        $entityManagerLocale->flush();
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeSeenIn_list');
    }

    public static function uploadImage($file)
    {
        if (!file_exists(UPLOAD_IMG_PATH)) {
            mkdir(UPLOAD_IMG_PATH);
        }
        // generate upload path for images related to shop
        $uploadPath = UPLOAD_IMG_PATH . "homepage/";
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0776, TRUE);
        }
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        // generate real path for upload path
        $rootPath = ROOT_PATH . $uploadPath;
        // get upload file info
        $files = $adapter->getFileInfo($file);
        // check upload directory exists, if no then create upload directory
        if (!file_exists($rootPath)) {
            mkdir($rootPath);
        }
        // set destination path and apply validations
        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('jpg,jpeg,png', true));
        // get file name
        $name = $adapter->getFileName($file, false);
        // rename file name to by prefixing current unix timestamp
        $newName = time() . "_" . $name;
        // generates complete path of image
        $cp = $rootPath . $newName;
        $path = ROOT_PATH . $uploadPath . "thum_" . $newName;
        $viewHelper = new \BackEnd_Helper_viewHelper();
        $viewHelper->resizeImage($files[$file], $newName, 132, 0, $path);


        $path = ROOT_PATH . $uploadPath . "thum_SeenIn_large" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 124, 0, $path);
        // apply filter to rename file name and set target
        $adapter
        ->addFilter(
            new \Zend_Filter_File_Rename(
                array(
                    'target' => $cp,
                    'overwrite' => true
                )
            ),
            null,
            $file
        );
        // recieve file for upload
        $adapter->receive($file);
        // check is file is valid then
        if ($adapter->isValid($file)) {
            return array("fileName" => $newName, "status" => "200",
                    "msg" => "File uploaded successfully",
                    "path" => $uploadPath);
        } else {
            return array("status" => "-1",
                    "msg" => "Please upload the valid file");
        }
    }
}