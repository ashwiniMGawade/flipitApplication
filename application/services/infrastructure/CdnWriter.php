<?php
class Application_Service_Infrastructure_CdnWriter extends Application_Service_Infrastructure_CdnBase
{
    public function putFile($localFilePath, $cdnFilePath)
    {
        $s3 = new Zend_Service_Amazon_S3(S3KEY, S3SECRET);
        $s3->putFile($localFilePath, S3BUCKET.$cdnFilePath);
    }
}