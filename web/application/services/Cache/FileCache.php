<?php

class Application_Service_Cache_FileCache
{
    public $filePath = '/tmp/';

    public function save($cacheId, $content)
    {
        $filePointer = fopen($this->filePath.$cacheId.'.php', 'w');
        fwrite($filePointer, $content);
        fclose($filePointer);
    }

    public function fetch($cacheId)
    {
        $filePointer = fopen($this->filePath.$cacheId.'.php', 'r');
        $content = fread($filePointer, filesize($this->filePath.$cacheId.'.php'));
        fclose($filePointer);
        return $content;
    }

    public function contains($cacheId)
    {
        return file_exists($this->filePath.$cacheId.'.php') ? true : false;
    }

    public function delete($cacheId)
    {
        unlink($this->filePath.$cacheId.'.php');
    }
}
