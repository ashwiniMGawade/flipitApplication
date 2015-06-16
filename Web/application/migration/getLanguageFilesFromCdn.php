<?php
class getLanguageFilesFromCdn
{
    public function getLanguageFiles()
    {
        $cdn = new Application_Service_Infrastructure_Cdn_Reader();
        // $s3->putFile($localFilePath, S3BUCKET.$cdnFilePath);
        $cdn->getObject($cdnFilePath);
        echo $cdn;
    }
}