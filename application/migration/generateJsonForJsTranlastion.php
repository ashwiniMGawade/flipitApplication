<?php

/**
 * Generate json file of javascript translation
 *
 * @author Surinderpal Singh
 *
 * @file generateJsonForJsTranlastion.php
 *
 */
class CreateTranslationJSON
{
    protected $_localePath = '';


    public function __construct()
    {
        ini_set('memory_limit', '-1');

        set_time_limit(0);


        // Define path to application directory
        defined('APPLICATION_PATH')
        || define('APPLICATION_PATH',
                dirname(dirname(__FILE__)));

        defined('LIBRARY_PATH')
        || define('LIBRARY_PATH', realpath(dirname(dirname(dirname(__FILE__))). '/library'));

        defined('DOCTRINE_PATH') || define('DOCTRINE_PATH', LIBRARY_PATH . '/Doctrine1');

        // Define application environment
        defined('APPLICATION_ENV')
        || define('APPLICATION_ENV',
                (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
                        : 'production'));


        //Ensure library/ is on include_path
        set_include_path(
                implode(PATH_SEPARATOR,
                        array(realpath(APPLICATION_PATH . '/../library'),
                                get_include_path(),)));
        set_include_path(
                implode(PATH_SEPARATOR,
                        array(realpath(DOCTRINE_PATH), get_include_path(),)));

        /** Zend_Application */
        require_once(LIBRARY_PATH.'/FrontEnd/Helper/viewHelper-v1.php');
        require_once (LIBRARY_PATH . '/Zend/Application.php');
        require_once(DOCTRINE_PATH . '/Doctrine.php');

        // Create application, bootstrap, and run
        $application = new Zend_Application(APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini');

        $connections = $application->getOption('doctrine');
        spl_autoload_register(array('Doctrine', 'autoload'));

        $manager = Doctrine_Manager::getInstance();

        $imbull = $connections['imbull'];

        // cycle through  all site database
        foreach ( $connections as $key => $connection ) {

            // check database is being must be site
            if ($key != 'imbull') {
                try {
                    $this->generateJSON ( $connection ['dsn'], $key ,$imbull );
                } catch ( Exception $e ) {

                    echo $e->getMessage ();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }


    }

    protected function generateJSON($dsn, $locale,$imbull)
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


        defined('PUBLIC_PATH')
        || define('PUBLIC_PATH',
                dirname(dirname(dirname(__FILE__)))."/public/");

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
        $json = preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", json_encode($out));
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
