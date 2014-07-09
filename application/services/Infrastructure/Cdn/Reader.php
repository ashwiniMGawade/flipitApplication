<?php
class Application_Service_Infrastructure_Cdn_Reader extends Application_Service_Infrastructure_Cdn_Base
{
    public function __constructor()
    {
    }  

    // public function getObject($localFilePath, $cdnFilePath)
    // {
    //     // $s3->putFile($localFilePath, S3BUCKET.$cdnFilePath);
    //     $this->getObject(S3BUCKET.$cdnFilePath);
    // }

    public function getObjectsByBucket($cdnFilePath)
    {
        $s3 = new Zend_Service_Amazon_S3(S3KEY, S3SECRET);
        $s3->getObject(S3BUCKET.$cdnFilePath); 
        echo "<pre>";
        print_r($s3);
    }
}