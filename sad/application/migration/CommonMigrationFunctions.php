<?php
class CommonMigrationFunctions
{
    /**
     * Function copyDirectory
     *
     * @param Source directory
     *
     * @param Destination directory
     */

    public static function copyDirectory($source, $destination)
    {
        if (!is_dir($destination)) {
            $oldumask = umask(0);
            mkdir($destination, 01777); // so you get the sticky bit set
            umask($oldumask);
        }
        $dir_handle = @opendir($source) or die("Unable to open");
        while ($file = readdir($dir_handle)) {
            if($file!="." && $file!=".." && !is_dir("$source/$file")) //if it is file
                copy("$source/$file", "$destination/$file");
            if($file!="." && $file!=".." && is_dir("$source/$file")) //if it is folder
                self::copyDirectory("$source/$file", "$destination/$file");
        }
        closedir($dir_handle);
    }

    /**
     * Function deleteDirectory
     *
     * @param directory path
     *
     */

    public static function deleteDirectory($directoryPath)
    {
        if (! is_dir($directoryPath)) {
            throw new InvalidArgumentException("$directoryPath must be a directory");
        }
        if (substr($directoryPath, strlen($directoryPath) - 1, 1) != '/') {
            $directoryPath .= '/';
        }
        $files = glob($directoryPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDirectory($file);
            } else {
                unlink($file);
            }
        }
        rmdir($directoryPath);
    }

    public static function loadDoctrineModels()
    {
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');

        return $manager;
    }

    public static function getDoctrineSiteConnection($dsn)
    {
        $doctrineSiteConnection = Doctrine_Manager::connection($dsn, 'doctrine_site');

        return $doctrineSiteConnection;
    }

    public static function getAllConnectionStrings()
    {
        $application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        $connections = $application->getOption('doctrine');
        return $connections;
    }

    public static function getGlobalDbConnection($connections)
    {
        $doctrineImbullDbConnection = Doctrine_Manager::connection($connections['imbull'], 'doctrine');
        return $doctrineImbullDbConnection;
    }

    public static function getGlobalDbConnectionManger()
    {
        spl_autoload_register(array('Doctrine', 'autoload'));
        $manager = Doctrine_Manager::getInstance();
        return $manager;
    }
    public static function showProgressMessage($message)
    {
        $message = "\n".$message ."\n";
        return $message;
    }
    public static function setTimeAndMemoryLimit()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
    }

    public static function connectionToPDO($dsn)
    {
        $pdoCredentials             = parse_url($dsn);
        $pdoCredentials['dbname']   = ltrim ($pdoCredentials['path'],'/');
        return new PDO("mysql:host={$pdoCredentials['host']};dbname={$pdoCredentials['dbname']}",
                        $pdoCredentials['user'], $pdoCredentials['pass']);
    }

    public static function pathToTempExcelFolder($locale)
    {
        $localePath         = $locale == 'en' ? '' : $locale.'/';
        $pathToExcelFolder  = UPLOAD_EXCEL_TMP_PATH . strtolower($localePath) . 'excels/';
        if(!file_exists($pathToExcelFolder)) mkdir($pathToExcelFolder, 0774, TRUE);
        return $pathToExcelFolder;
    }

    public static function dateFormatConstants()
    {
        $date = new Zend_Date();
        $month = $date->get(Zend_Date::MONTH_NAME);
        $year = $date->get(Zend_Date::YEAR);
        $day = $date->get(Zend_Date::DAY);
        defined('CURRENT_MONTH') || define('CURRENT_MONTH', $month);
        defined('CURRENT_YEAR') || define('CURRENT_YEAR', $year);
        defined('CURRENT_DAY') || define('CURRENT_DAY', $day);
    }
}
