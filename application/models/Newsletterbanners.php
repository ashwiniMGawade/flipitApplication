<?php

class Newsletterbanners extends BaseNewsletterbanners
{
    public static function getHeaderOrFooterImage($imageType)
    {
        $existedNewsLetterHeaderImage = Doctrine_Query::create()
            ->select('s.name, s.path')
            ->from("Newsletterbanners s")
             ->where('s.imagetype = "'.$imageType.'"')
            ->fetchArray(null, Doctrine::HYDRATE_ARRAY);
        if (!empty($existedNewsLetterHeaderImage)) {
            $existedNewsLetterHeaderImage = $existedNewsLetterHeaderImage[0];
        }
        return $existedNewsLetterHeaderImage;
    }
    
    public static function updateNewsletterImages($params, $type)
    {
        $imageType = $type == 'footer' ? 'F' : 'H';
        $uploadedFile = $type == 'footer' ? 'newsLetterFooterImage' : 'newsLetterHeaderImage';
        if (isset($_FILES[$uploadedFile])) {
            $result = self::uploadImage($uploadedFile);
            if ($result['status'] == '200') {
                $existedNewsLetterHeaderImage = Doctrine_Query::create()
                    ->select('s.name, s.path')
                    ->from("Newsletterbanners s")
                    ->where('s.imagetype = "'.$imageType.'"')
                    ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
                if (empty($existedNewsLetterHeaderImage)) {
                    $newsLetterHeaderImage = new Newsletterbanners();
                    $newsLetterHeaderImage->name = $result['fileName'];
                    $newsLetterHeaderImage->path = $result['path'];
                    $newsLetterHeaderImage->imagetype = $imageType;
                    $newsLetterHeaderImage->save();
                } else {
                    $fileName = $existedNewsLetterHeaderImage['name'];
                    $filePath = $existedNewsLetterHeaderImage['path'];
                    @unlink(ROOT_PATH. $filePath . $fileName);
                    @unlink(ROOT_PATH. $filePath . $result['cmsFilename_prefix'] . $fileName);
                    Doctrine_Query::create()
                        ->update('Newsletterbanners')
                        ->set('name', '?', $result['fileName'])
                        ->set('path', '?', $result['path'])
                        ->execute();
                }
                return $result ;
            }
        }
    }

    public static function uploadImage($file)
    {
        $uploadPath = "images/front_end/newsletterbannerimages/";
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $rootPath = ROOT_PATH . $uploadPath;
        $files = $adapter->getFileInfo($file);
        if (!file_exists($rootPath)) {
            mkdir($rootPath);
        }
        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('jpg,jpeg,png', true));
        $name = $adapter->getFileName($file, false);
        $newName = time() . "_" . $name;
        $cp = $rootPath . $newName;
        $adapter->addFilter(
            new Zend_Filter_File_Rename(
                array('target' => $cp, 'overwrite' => true)
            ),
            null,
            $file
        );
        $adapter->receive($file);
        if ($adapter->isValid($file)) {
            return array(
                "fileName" => $newName,
                "status" => "200",
                "msg" => "File uploaded successfully",
                "path" => $uploadPath
            );
        } else {
            return array(
                "status" => "-1",
                "msg" => "Please upload the valid file"
            );
        }
    }

    public static function deleteNewsletterImages($params, $imageType)
    {
        $existedNewsLetterHeaderImage = Doctrine_Query::create()
            ->select('s.name, s.path')
            ->from("Newsletterbanners s")
            ->where('s.imagetype = "'.$imageType.'"')
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        if (!empty($existedNewsLetterHeaderImage)) {
            $fileName = $existedNewsLetterHeaderImage['name'];
            $filePath = $existedNewsLetterHeaderImage['path'];
            @unlink(ROOT_PATH. $filePath . $fileName);
        }
        Doctrine_Query::create()
            ->delete('Newsletterbanners s')
            ->where('s.imagetype = "'.$imageType.'"')
            ->execute();
        return true ;
    }
}
