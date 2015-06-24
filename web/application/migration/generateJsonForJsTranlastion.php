<?php
class CreateTranslationJSON
{
    protected $_localePath = '';
    public function __construct()
    {
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->generateJSON($connection ['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
    }

    protected function generateJSON($dsn, $locale)
    {
        //setting to no time limit,
        set_time_limit(0);


        if ($locale == 'en') {
            $this->_localePath = '';
            $suffix = "" ;
        } else {
            $this->_localePath = $locale . "/";
            $suffix = "_" . strtoupper($locale) ;
        }


        $pathToDir = PUBLIC_PATH . $this->_localePath . "js/front_end/json/";
        $fileName = "translation.js";
        # add suffix according to locale
        $path =  PUBLIC_PATH . $this->_localePath . "language/frontend_js" . strtoupper($suffix). ".po";
        $homepage = file_get_contents($path);
        preg_match_all('/msgid "(.*)"/', $homepage, $keys);
        preg_match_all('/msgstr "(.*)"/', $homepage, $strings);

        $str = array();
        foreach ($strings[1] as $key => $value) {
            $str[] = array('null',$value);
        }

        $out = @array_combine($keys[1], $str);
        $json = preg_replace_callback(
            "/\\\\u([a-f0-9]{4})/",
            function ($matches) {
                return iconv('UCS-4LE', 'UTF-8', pack('V', hexdec('U'.$matches[1])));
            },
            json_encode($out)
        );
        $translations = " var json = " . $json . " ;  var jsonData = {};  jsonData['frontend_js'] =json;  var gt = new Gettext({ 'domain' : 'frontend_js' , 'locale_data' : jsonData });";



        if(!file_exists($pathToDir))
            mkdir($pathToDir, 0776, TRUE);

        $pathToJSONFile = $pathToDir . $fileName ;

        $jsonHandle = fopen($pathToJSONFile, 'w');

        fwrite($jsonHandle, $translations);
        fclose($jsonHandle);


        // echo "\n";
        // print "$locale - JSON file for shops has been created successfully!!!";

    }

}


new CreateTranslationJSON ();
