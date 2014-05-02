<?php
class AlterColumnVisitor extends Doctrine_Migration_Base
{

    protected $_db = null;
    protected $_locale = '';
    protected $_connInstance = null;

    public function __construct()
    {
        # get connected db name
        foreach(Doctrine_Manager::getInstance()->getConnections() as $connection){

            $this->_connInstance  = $connection ;
            $conn = $connection->getOptions();
            preg_match('/dbname=(.*)/', $conn['dsn'], $db);
            $this->_db = $db[1];
        }

         # get the locale of db
        $parts = array_reverse( explode('_' , $this->_db) );

        # check locale length for flipit
        if(strlen($parts[0]) == 2) {
            # set the locale
            $this->_locale = $parts[0];
        }

    }

    public function up()
    {

        $this->changeColumn( 'visitor', 'locale', 'string', 5 ,
                        array('default' => $this->_locale ,
                              'notnull' => true,
                              'collation' => 'utf8_general_ci' ));
    }

    public function down()
    {
        $this->changeColumn( 'visitor', 'locale', 'string', 5 ,
                        array('default' => '', 'notnull' => true ));
    }

    # update locale value for all existing visitors
    public function postUp()
    {
        $v = Doctrine_Query::create($this->_connInstance)
                ->update('Visitor')->set('locale' , "'". $this->_locale ."'" )	;
        $v->execute();
    }

}
