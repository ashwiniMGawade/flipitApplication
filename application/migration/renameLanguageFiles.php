<?php

/**
 * Rename Language Files by adding suffix locale to thie names
 *
 * @author Surinderpal Singh
 *
 */
class renameLanguageFiles
{
    protected $_locale = 'en';
    protected $_localePath = '/';
    protected $_rootDir = "" ;


    public function __construct()
    {
        ini_set ( 'memory_limit', '-1' );

        set_time_limit ( 0 );

        /*
         * $domain1 = $_SERVER['HOSTNAME']; $domain = 'http://www.'.$domain1;
         */

        // Define path to application directory
        defined ( 'APPLICATION_PATH' ) || define ( 'APPLICATION_PATH', dirname ( dirname ( __FILE__ ) ) );

        defined ( 'LIBRARY_PATH' ) || define ( 'LIBRARY_PATH', realpath ( dirname ( dirname ( dirname ( __FILE__ ) ) ) . '/library' ) );

        defined ( 'DOCTRINE_PATH' ) || define ( 'DOCTRINE_PATH', LIBRARY_PATH . '/Doctrine1' );

        // Define application environment
        defined ( 'APPLICATION_ENV' ) || define ( 'APPLICATION_ENV', (getenv ( 'APPLICATION_ENV' ) ? getenv ( 'APPLICATION_ENV' ) : 'production') );

        // Ensure library/ is on include_path
        set_include_path ( implode ( PATH_SEPARATOR, array (realpath ( APPLICATION_PATH . '/../library' ), get_include_path () ) ) );
        set_include_path ( implode ( PATH_SEPARATOR, array (realpath ( DOCTRINE_PATH ), get_include_path () ) ) );

        /**
         * Zend_Application
         */

        // echo LIBRARY_PATH;
        // echo DOCTRINE_PATH;
        // die;
        require_once (LIBRARY_PATH . '/FrontEnd/Helper/viewHelper-v1.php');
        require_once (LIBRARY_PATH . '/Zend/Application.php');
        require_once (DOCTRINE_PATH . '/Doctrine.php');

        // Create application, bootstrap, and run
        $application = new Zend_Application ( APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini' );

        $connections = $application->getOption ( 'doctrine' );
        spl_autoload_register ( array ('Doctrine', 'autoload' ) );

        $manager = Doctrine_Manager::getInstance ();

        $imbull = $connections ['imbull'];

        // cycle htoruh all site database
        foreach ( $connections as $key => $connection ) {
            // check database is being must be site
            if ($key != 'imbull') {
                try {

                    $this->renameFiles ( $connection ['dsn'], $key, $imbull );
                } catch ( Exception $e ) {

                    echo $e->getMessage ();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
    }

    protected function renameFiles($dsn, $key, $imbull)
    {
        $this->_locale = $key ;

        defined ( 'PUBLIC_PATH' ) || define ( 'PUBLIC_PATH', dirname ( dirname ( dirname ( __FILE__ ) ) ) . "/public/" );



        if ($key == 'en') {
            $this->_localePath = '';

        } else {
            $this->_localePath = $key . "/";
        }

        $this->_rootDir = PUBLIC_PATH . $this->_localePath ;


        $this->traverseDir($this->_rootDir);
    }

     function traverseDir($dir)
     {
        # print root dir
        if (basename ( $dir ) == 'language') {

            print "Traversing  $dir  \n";
        }



        if (! ($dp = opendir ( $dir )))
            die ( "Cannot open $dir." );

        while ( (false !== $file = readdir ( $dp )) ) {

            # do nothing for root or parent directory and traverse untill langauge dir not found
            if ($file != '.' && $file != '..') {

                # read language dir
                if (is_dir ( $dir . $file ) && basename ( $file ) == 'language') {
                    $this->traverseDir ( $dir . $file );
                }

                # rename po and mo file if current dir id language dir
                if (basename ( $dir ) == 'language') {


                    # add suffix according to locale
                    if ($this->_locale == 'en') {
                        $suffix = "" ;
                    } else 	{
                        $suffix = "_" . strtoupper($this->_locale) ;
                    }

                    # verify current file is alreay not having locale sufix mo or po file
                    if (! strstr ( $file, $suffix . ".po" ) &&  ! strstr ( $file, $suffix . ".mo" ) ) {


                        $filePath = $this->_rootDir . 'language/';

                        # create new file name and rename old ones

                        //$file1 = str_replace ( array(".po",".mo","_%s%s_.mo"),array( $suffix . ".po",$suffix . ".mo",$suffix . ".mo"), $file );

                        $pattern = '/([A-Z\_]{3})(\.)(mo|po)/i';

                        $replacement = $suffix.'$2$3';

                        $file1 = preg_replace($pattern, $replacement, $file);

                        rename ( $filePath . $file, $filePath . $file1 );

                        print "this is file $file1 \n";
                    }
                }
            }

        }
        closedir ( $dp );
    }
}

new RenameLanguageFiles ();
