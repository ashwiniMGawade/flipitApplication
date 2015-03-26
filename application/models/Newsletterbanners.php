<?php

class Newsletterbanners extends BaseNewsletterbanners
{
    public static function getHeaderOrFooterImage($imageType)
    {
        $existedNewsLetterImage = Doctrine_Query::create()
            ->select('s.name, s.path')
            ->from("Newsletterbanners s")
             ->where('s.imagetype = "'.$imageType.'"')
            ->fetchArray(null, Doctrine::HYDRATE_ARRAY);
        if (!empty($existedNewsLetterImage)) {
            $existedNewsLetterImage = $existedNewsLetterImage[0];
        }
        return $existedNewsLetterImage;
    }
    
    public static function updateNewsletterImages($params, $type)
    {
        $imageType = $type == 'footer' ? 'F' : 'H';
        $uploadedFile = $type == 'footer' ? 'newsLetterFooterImage' : 'newsLetterHeaderImage';
        if (isset($_FILES[$uploadedFile])) {
            $uploadedImage = self::uploadImage($uploadedFile);
            if ($uploadedImage['status'] == '200') {
                $existedNewsLetterImage = Doctrine_Query::create()
                    ->select('s.name, s.path')
                    ->from("Newsletterbanners s")
                    ->where('s.imagetype = "'.$imageType.'"')
                    ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
                if (empty($existedNewsLetterImage)) {
                    $newsLetterHeaderImage = new Newsletterbanners();
                    $newsLetterHeaderImage->name = $uploadedImage['fileName'];
                    $newsLetterHeaderImage->path = $uploadedImage['path'];
                    $newsLetterHeaderImage->imagetype = $imageType;
                    $newsLetterHeaderImage->save();
                } else {
                    $fileName = $existedNewsLetterImage['name'];
                    $filePath = $existedNewsLetterImage['path'];
                    @unlink(ROOT_PATH. $filePath . $fileName);
                    @unlink(ROOT_PATH. $filePath . $uploadedImage['cmsFilename_prefix'] . $fileName);
                    Doctrine_Query::create()
                        ->update('Newsletterbanners')
                        ->set('name', '?', $uploadedImage['fileName'])
                        ->set('path', '?', $uploadedImage['path'])
                        ->execute();
                }
                return $uploadedImage;
            }
        }
    }

    public static function uploadImage($file)
    {
        $uploadPath = "images/front_end/newsletterbannerimages/";
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $rootPath = ROOT_PATH . $uploadPath;
        $adapter->getFileInfo($file);
        if (!file_exists($rootPath)) {
            mkdir($rootPath);
        }
        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('jpg,jpeg,png', true));
        $imageName = $adapter->getFileName($file, false);
        $changedImageName = time() . "_" . $imageName;
        $targetPath = $rootPath . $changedImageName;
        $adapter->addFilter(
            new Zend_Filter_File_Rename(
                array('target' => $targetPath, 'overwrite' => true)
            ),
            null,
            $file
        );
        $adapter->receive($file);
        if ($adapter->isValid($file)) {
            return array(
                "fileName" => $changedImageName,
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
        $existedNewsLetterImage = Doctrine_Query::create()
            ->select('s.name, s.path')
            ->from("Newsletterbanners s")
            ->where('s.imagetype = "'.$imageType.'"')
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        if (!empty($existedNewsLetterImage)) {
            $fileName = $existedNewsLetterImage['name'];
            $filePath = $existedNewsLetterImage['path'];
            @unlink(ROOT_PATH. $filePath . $fileName);
        }
        Doctrine_Query::create()
            ->delete('Newsletterbanners s')
            ->where('s.imagetype = "'.$imageType.'"')
            ->execute();
        return true ;
    }
}
