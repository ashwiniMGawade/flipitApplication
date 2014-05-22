<?php
class FrontEnd_Helper_lessCssHelper
{
    public static function lessToCss()
    {
        self::checkAjaxRequest();
        if (APPLICATION_ENV == 'development') {
            $cssFrontEndPath = APPLICATION_PATH.'/../public/css/front_end/';
            $updatedLessFiles = self::getUpdatedLessFiles($cssFrontEndPath);

            if ($updatedLessFiles['flag'] == 1) {
                self::writeLogFileWithUpdatedContent($cssFrontEndPath, $updatedLessFiles['logFileContentValues']);
            } 
            
        }
        return;
    }

    public static function checkAjaxRequest()
    {
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        return;
        }
    }

    public static function getUpdatedLessFiles($cssFrontEndPath)
    {
        $lessLogFileArrayCounter = 0;
        $flag = 0;
        $logFileContentValues = self::getLogFileDetails($cssFrontEndPath);             
        $lessFilesNames = scanDir($cssFrontEndPath.'less/');

        foreach ($lessFilesNames as $lessFileName) {      
            ini_set('max_execution_time', 300);
            if ($lessFileName != '.' && $lessFileName != '..') {  
                if (pathinfo($lessFileName, PATHINFO_EXTENSION) == 'less') {
                    $cssFileName = explode('.', $lessFileName);
                    $lessFile = $cssFrontEndPath.'less/'.$lessFileName; 
                    $cssFile = $cssFrontEndPath.$cssFileName[0].'.css'; 
                    
                    if ($logFileContentValues[$lessLogFileArrayCounter] == date("FdYHis",filemtime($lessFile))) {
                    } else {
                        $logFileContentValues[$lessLogFileArrayCounter] = date("FdYHis",filemtime($lessFile));
                        $flag = 1;
                        self::parseLessFilesToCss($cssFrontEndPath, $lessFile, $cssFile);
                    }                       
                $lessLogFileArrayCounter++;   
               }
            }    
        }
        return array('logFileContentValues' => $logFileContentValues, 'flag' => $flag);
    }

    public static function getLogFileDetails($cssFrontEndPath)
    {
        $logFileRead = fopen($cssFrontEndPath.'less/log.txt', 'r');
        $logFileContent = fread($logFileRead, filesize($cssFrontEndPath.'less/log.txt'));      
        $logFileContentValues = explode('-',$logFileContent);
        return $logFileContentValues;
    }

    public static function parseLessFilesToCss($cssFrontEndPath, $lessFile, $cssFile)
    {
        require_once APPLICATION_PATH.'/../library/less.php/Less.php'; 
        $directories = array( $cssFrontEndPath.'less/' => $cssFrontEndPath.'less-bootstrap/');
        $parser = new Less_Parser();
        $parser->SetImportDirs( $directories );
        $parser->parseFile( $lessFile, '../' );
        $parsedCss = $parser->getCss();
        file_put_contents($cssFile, $parsedCss);
    }

    public static function writeLogFileWithUpdatedContent($cssFrontEndPath, $updatedLessFilesContent)
    {
        $fileHandler = fopen($cssFrontEndPath.'less/log.txt', 'w');
        fwrite($fileHandler, implode('-', array_values($updatedLessFilesContent)));  
        fclose($fileHandler); 
    }
}
FrontEnd_Helper_lessCssHelper::lessToCss();