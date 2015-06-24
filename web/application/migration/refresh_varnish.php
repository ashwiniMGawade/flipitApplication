<?php
//error_reporting(E_ALL|E_STRICT);
/**
 * RefreshVarnish
 *
 * used to procees the varnish queue
 *
 * @author Surinderpal Singh
 *
 */
class RefreshVarnish
{
    public function __construct()
    {
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');

        CommonMigrationFunctions::setTimeAndMemoryLimit();

        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();


        # cycle htoruh all site database
        foreach ($connections as $key => $connection) {
            # check database is being must be site
            if ($key != 'imbull') {
                try {
                    $this->refresh($connection['dsn'], $key);

                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n" ;
                }
            }
        }



    }


    protected function refresh($dsn, $key)
    {
        $connName ='doctrine_site_'.$key ;
        # auto load doctrine library
        spl_autoload_register(array('Doctrine', 'autoload'));


        # create coonection
        $DMC = Doctrine_Manager::connection($dsn, $connName);
                //$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');

        # auto  model class
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));

                # cretae donctrine mager
        $manager = Doctrine_Manager::getInstance();

        # set manager attribute like table class, base classes etc
        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');

        $varnishObj = new Varnish($connName);
        $varnishObj->processQueue();

        #close connection
        $manager->closeConnection($DMC);
        print "\n$key : Varnish has been successfully refreshed!!!" ;


    }
}



define( 'LOCK_FILE', dirname(__FILE__) . "/" .basename( $argv[0], ".php" ).".lock" );

if( isLocked() ) die( "Script is already running.\n" );

# The rest of your script goes here....
new RefreshVarnish();

unlink( LOCK_FILE );
exit(0);

function isLocked()
{
    # If lock file exists, check if stale.  If exists and is not stale, return TRUE
    # Else, create lock file and return FALSE.

    if( file_exists( LOCK_FILE ) ) {
        # check if it's stale
        $lockingPID = trim( file_get_contents( LOCK_FILE ) );


        # this is for Linux OS
        # Get all active PIDs.

        $pids = explode( "\n", trim( `ps -e | awk '{print $1}'` ) );



        # uncommnet this for Window OS
        # Get all active PIDs.
        # $pids = explode( "\n", trim( `for /f "tokens=2" %a in ('tasklist /NH /FI "PID gt 0"') do  @ECHO %a` ) );


        # If PID is still active, return true
        if( in_array( $lockingPID, $pids ) )  return true;

        # Lock-file is stale, so kill it.  Then move on to re-creating it.
        echo "Removing stale lock file.\n";
        unlink( LOCK_FILE );
    }

    file_put_contents( LOCK_FILE, getmypid() . "\n" );
    return false;

}
