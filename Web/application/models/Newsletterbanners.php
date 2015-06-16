<?php

class Newsletterbanners extends BaseNewsletterbanners
{
    public static function getHeaderOrFooterImage($imageType)
    {
        $existedNewsLetterImage = Doctrine_Query::create()
            ->select('s.name, s.path, s.headerurl, s.footerurl, s.imagetype')
            ->from("Newsletterbanners s")
            ->where('s.imagetype = "'.$imageType.'"')
            ->fetchArray(null, Doctrine::HYDRATE_ARRAY);
        if (!empty($existedNewsLetterImage)) {
            $existedNewsLetterImage = $existedNewsLetterImage[0];
        }
        return $existedNewsLetterImage;
    }
    
    public static function getHeaderOrFooterImageUrl($columnName, $imageType)
    {
        $columnName = 's.'.$columnName;
        $existedNewsLetterImageUrl = Doctrine_Query::create()
            ->select($columnName)
            ->from("Newsletterbanners s")
            ->where('s.imagetype = "'.$imageType.'"')
            ->fetchArray(null, Doctrine::HYDRATE_ARRAY);
        if (!empty($existedNewsLetterImageUrl)) {
            $existedNewsLetterImageUrl = $existedNewsLetterImageUrl[0];
        }
        return $existedNewsLetterImageUrl;
    }

    public static function saveNewsletterImagesUrl($columnName, $value)
    {
        $imageType = $columnName == 'headerurl' ? 'header' : 'footer';
        $updatedColumnName = 's.'.$columnName;
        $existedNewsLetterImage = self::getHeaderOrFooterImage($imageType);
        if (!empty($existedNewsLetterImage[$columnName])) {
            self::updateNewsLetterBannerUrl($updatedColumnName, $value, $imageType);
        } else {
            if (!empty($existedNewsLetterImage['path'])) {
                self::updateNewsLetterBannerUrl($updatedColumnName, $value, $imageType);
            } else {
                self::saveNewsLetterBannerUrl($columnName, $value, $imageType);
            }
        }
        return true;
    }

    public static function updateNewsLetterBannerUrl($updatedColumnName, $value, $imageType)
    {
        $newsLetterImageUrl = Doctrine_Query::create()
            ->update('Newsletterbanners s')
            ->set($updatedColumnName, '"'. $value .'"')
            ->where('s.imagetype = "'. $imageType .'"')
            ->execute();
        return true;
    }

    public static function saveNewsLetterBannerUrl($columnName, $value, $imageType)
    {
        $newsLetterImageUrl = new Newsletterbanners();
        $newsLetterImageUrl->$columnName = $value;
        $newsLetterImageUrl->imagetype = $imageType;
        $newsLetterImageUrl->save();
        return true;
    }

    public static function updateNewsletterImages($imageType)
    {
        $uploadedFile = $imageType == 'footer' ? 'newsLetterFooterImage' : 'newsLetterHeaderImage';
        $columnName = $imageType == 'footer' ? 'footerurl' : 'headerurl';
        if (isset($_FILES[$uploadedFile])) {
            $uploadedImage = self::uploadImage($uploadedFile);
            if ($uploadedImage['status'] == '200') {
                $existedNewsLetterImage = self::getHeaderOrFooterImage($imageType);
                if (!empty($existedNewsLetterImage)) {
                    self::unlinkFileFromDirectory($existedNewsLetterImage);
                    $uploadedImages = array(
                        'fileName'=>$uploadedImage['fileName'],
                        'path'=>$uploadedImage['path'],
                        'footerurl'=>$existedNewsLetterImage['footerurl'],
                        'headerurl'=>$existedNewsLetterImage['headerurl'],
                    );
                    self::updateNewsletterBanners($uploadedImages, $imageType);
                } else {
                    self::saveNewsletterImages($uploadedImage, $imageType);
                }
                return $uploadedImage;
            }
        }
        return true;
    }

    public static function saveNewsletterImages($uploadedImage, $imageType)
    {
        $newsLetterHeaderImage = new Newsletterbanners();
        $newsLetterHeaderImage->name = urlencode($uploadedImage['fileName']);
        $newsLetterHeaderImage->path = $uploadedImage['path'];
        $newsLetterHeaderImage->imagetype = $imageType;
        $newsLetterHeaderImage->save();
        return true;
    }

    public static function unlinkFileFromDirectory($existedNewsLetterImage)
    {
        $fileName = $existedNewsLetterImage['name'];
        $filePath = $existedNewsLetterImage['path'];
        @unlink(ROOT_PATH. $filePath . $fileName);
        return true;
    }

    public static function updateNewsletterBanners($uploadedImage, $imageType)
    {
        Doctrine_Query::create()
            ->update('Newsletterbanners n')
            ->set('n.name', "'".urlencode($uploadedImage['fileName'])."'")
            ->set('n.path', "'".$uploadedImage['path']."'")
            ->set('n.footerurl', "'".$uploadedImage['footerurl']."'")
            ->set('n.headerurl', "'".$uploadedImage['headerurl']."'")
            ->where('n.imagetype = "'.$imageType.'"')
            ->execute();
        return true;
    }

    public static function uploadImage($file)
    {
        $uploadPath = UPLOAD_IMG_PATH. "newsletterbannerimages/";
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $rootPath = ROOT_PATH . $uploadPath;
        $adapter->getFileInfo($file);
        if (!file_exists($rootPath)) {
            mkdir($rootPath, 0755, true);
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
                "msg" => FrontEnd_Helper_viewHelper::__translate("File uploaded successfully"),
                "path" => $uploadPath
            );
        } else {
            return array(
                "status" => "-1",
                "msg" => FrontEnd_Helper_viewHelper::__translate("Please upload the valid file")
            );
        }
    }

    public static function deleteNewsletterImages($imageType)
    {
        $existedNewsLetterImage = self::getHeaderOrFooterImage($imageType);
        if (!empty($existedNewsLetterImage['path'])) {
            self::unlinkFileFromDirectory($existedNewsLetterImage);
        }
        Doctrine_Query::create()
            ->update('Newsletterbanners n')
            ->set('n.name', '?', '')
            ->set('n.path', '?', '')
            ->where('n.imagetype = "'.$imageType.'"')
            ->execute();
        return true ;
    }
}
