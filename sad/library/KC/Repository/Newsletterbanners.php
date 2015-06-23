<?php
namespace KC\Repository;
class Newsletterbanners extends \KC\Entity\NewsLetterBanners
{
    public static function getHeaderOrFooterImage($imageType)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
            ->select('s.name, s.path, s.headerurl, s.footerurl, s.imagetype')
            ->from("KC\Entity\NewsLetterBanners", "s")
            ->where('s.imagetype ='.$entityManagerLocale->expr()->literal($imageType));
        $existedNewsLetterImage = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (!empty($existedNewsLetterImage)) {
            $existedNewsLetterImage = $existedNewsLetterImage[0];
        }
        return $existedNewsLetterImage;
    }
    
    public static function getHeaderOrFooterImageUrl($columnName, $imageType)
    {
        $columnName = 's.'.$columnName;
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
            ->select($columnName)
            ->from("KC\Entity\NewsLetterBanners", "s")
            ->where('s.imagetype ='.$entityManagerLocale->expr()->literal($imageType));
        $existedNewsLetterImageUrl = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
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
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $entityManagerLocale->update('KC\Entity\NewsLetterBanners', 's')
        ->set($updatedColumnName, $entityManagerLocale->expr()->literal($value))
        ->where('s.imagetype ='.$entityManagerLocale->expr()->literal($imageType))
        ->getQuery()->execute();
        return true;
    }

    public static function saveNewsLetterBannerUrl($columnName, $value, $imageType)
    {
        $newsLetterImageUrl = new KC\Entity\NewsLetterBanners();
        $newsLetterImageUrl->$columnName = $value;
        $newsLetterImageUrl->imagetype = $imageType;
        $newsLetterImageUrl->deleted = 0;
        $newsLetterImageUrl->created_at = new \DateTime('now');
        $newsLetterImageUrl->updated_at = new \DateTime('now');
        \Zend_Registry::get('emLocale')->persist($newsLetterImageUrl);
        \Zend_Registry::get('emLocale')->flush();
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
        $newsLetterHeaderImage = new \KC\Entity\NewsLetterBanners();
        $newsLetterHeaderImage->name = urlencode($uploadedImage['fileName']);
        $newsLetterHeaderImage->path = $uploadedImage['path'];
        $newsLetterHeaderImage->imagetype = $imageType;
        $newsLetterHeaderImage->deleted = 0;
        $newsLetterHeaderImage->created_at = new \DateTime('now');
        $newsLetterHeaderImage->updated_at = new \DateTime('now');
        \Zend_Registry::get('emLocale')->persist($newsLetterHeaderImage);
        \Zend_Registry::get('emLocale')->flush();
        return true;
    }

    public static function updateNewsletterBanners($uploadedImage, $imageType)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $entityManagerLocale->update('KC\Entity\NewsLetterBanners', 'n')
            ->set('n.name', $entityManagerLocale->expr()->literal(urlencode($uploadedImage['fileName'])))
            ->set('n.path', $entityManagerLocale->expr()->literal($uploadedImage['path']))
            ->set('n.footerurl', $entityManagerLocale->expr()->literal($uploadedImage['footerurl']))
            ->set('n.headerurl', $entityManagerLocale->expr()->literal($uploadedImage['headerurl']))
            ->where('n.imagetype ='. $entityManagerLocale->expr()->literal($imageType))
            ->getQuery()->execute();
        return true;
    }

    public static function uploadImage($file)
    {
        $uploadPath = UPLOAD_IMG_PATH. "newsletterbannerimages/";
        $adapter = new \Zend_File_Transfer_Adapter_Http();
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
            new \Zend_Filter_File_Rename(
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
                "msg" => \FrontEnd_Helper_viewHelper::__translate("File uploaded successfully"),
                "path" => $uploadPath
            );
        } else {
            return array(
                "status" => "-1",
                "msg" => \FrontEnd_Helper_viewHelper::__translate("Please upload the valid file")
            );
        }
    }

    public static function deleteNewsletterImages($imageType)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $entityManagerLocale->update('KC\Entity\NewsLetterBanners', 'n')
            ->set('n.name', $entityManagerLocale->expr()->literal(''))
            ->set('n.path', $entityManagerLocale->expr()->literal(''))
            ->where('n.imagetype ='.$entityManagerLocale->expr()->literal($imageType))
            ->getQuery()->execute();
        return true ;
    }
}