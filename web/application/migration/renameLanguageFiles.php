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
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');

        CommonMigrationFunctions::setTimeAndMemoryLimit();

        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();

        $imbull = $connections ['imbull'];

        // cycle htoruh all site database
        foreach ($connections as $key => $connection) {
            // check database is being must be site
            if ($key != 'imbull') {
                try {

                    $this->renameFiles($connection ['dsn'], $key);
                } catch (Exception $e) {

                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
    }

    protected function renameFiles($dsn, $key)
    {
        $this->_locale = $key ;

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
