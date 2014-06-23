<?php
class FrontEnd_Helper_lessCompiler
{
    public $cssFrontEndPath = '';

    public function __construct()
    {
        $this->cssFrontEndPath = APPLICATION_PATH.'/../public/css/front_end/';
    }

    public function lessToCss()
    {
        $this->checkAjaxRequest();
        if (APPLICATION_ENV == 'development') {
            $updatedLessFiles = $this->getUpdatedLessFiles();

            if ($updatedLessFiles['flag'] == 1) {
                $this->writeLogFileWithUpdatedContent($updatedLessFiles['logFileContentValues']);
            } 
            
        }
        return;
    }

    public function checkAjaxRequest()
    {
        if(
        	!empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
        	&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
        return;
        }
    }

    public function getUpdatedLessFiles()
    {
        $lessLogFileArrayCounter = 0;
        $flag = 0;
        $logFileContentValues = $this->getLogFileDetails();             
        $pathToLessFolder  = $this->cssFrontEndPath . 'less/';
        foreach (glob($pathToLessFolder."*.less") as $lessFileName) {
            ini_set('max_execution_time', 300);
            $lessFileName = explode('front_end/less/', $lessFileName);
            $cssFileName = explode('.', $lessFileName[1]);
            $lessFile = $this->cssFrontEndPath.'less/'.$lessFileName[1]; 
            $cssFile = $this->cssFrontEndPath.$cssFileName[0].'.css'; 

            if ($logFileContentValues[$lessLogFileArrayCounter] != date("FdYHis",filemtime($lessFile))) {
                $logFileContentValues[$lessLogFileArrayCounter] = date("FdYHis",filemtime($lessFile));
                $flag = 1;
                $this->parseLessFilesToCss($lessFile, $cssFile);
            }     

            $lessLogFileArrayCounter++;    
        }
        return array('logFileContentValues' => $logFileContentValues, 'flag' => $flag);
    }

    public function getLogFileDetails()
    {
        $logFileRead = fopen($this->cssFrontEndPath.'less/log.log', 'r');
        $logFileContent = fread($logFileRead, filesize($this->cssFrontEndPath.'less/log.log'));      
        $logFileContentValues = explode('-',$logFileContent);
        return $logFileContentValues;
    }

    public function parseLessFilesToCss($lessFile, $cssFile)
    {
        require_once APPLICATION_PATH.'/../library/less.php/Less.php'; 
        $directories = array( $this->cssFrontEndPath.'less/' => $this->cssFrontEndPath.'less-bootstrap/');
        $parser = new Less_Parser();
        $parser->SetImportDirs( $directories );
        $parser->parseFile( $lessFile, '../' );
        $parsedCss = $parser->getCss();
        file_put_contents($cssFile, $parsedCss);
    }

    public function writeLogFileWithUpdatedContent($updatedLessFilesContent)
    {
        $fileHandler = fopen($this->cssFrontEndPath.'less/log.log', 'w');
        fwrite($fileHandler, implode('-', array_values($updatedLessFilesContent)));  
        fclose($fileHandler); 
    }
}
$lessObject = new FrontEnd_Helper_lessCompiler();
$lessObject->lessToCss();