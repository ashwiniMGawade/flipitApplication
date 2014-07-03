<?php
class Application_Service_Infrastructure_Cdn_Writer extends Application_Service_Infrastructure_Cdn_Base
{
    public function putFile($localFilePath, $cdnFilePath)
    {
        $s3 = new Zend_Service_Amazon_S3(S3KEY, S3SECRET);
        $s3->putFile($localFilePath, S3BUCKET.$cdnFilePath);
    }
}